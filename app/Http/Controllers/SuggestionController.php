<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Suggestion;
use App\Models\User;
use App\Models\SuggestionScore;
use App\Models\SuggestionPeriod;
use App\Models\SuggestionRewardFormula;
use App\Models\SuggestionRewardFormulaItem;
use App\Models\SuggestionRewardFormulaItemCriteria;
use App\Models\SuggestionRewardTier;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\SuggestionPhoto;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class SuggestionController extends Controller
{

    // ── Helper: ambil user yang sedang login (session-based) ──
    private function currentUser(): User
{
    return User::with(['roles', 'departments'])
        ->findOrFail(Session::get('suggestion_user_id'));
}

    private function hasRole(User $user, string $role): bool
{
    return $user->roles->contains('name', $role);
}

    private function isImprovement(User $user): bool
    {
        return strtolower($user->departments->first()?->name) === 'improvement';
    }

    private function isSpv(User $user): bool
{
    return $user->roles()
        ->where('name', 'Supervisor Special Access')
        ->exists();
}

private function isManager(User $user): bool
{
    return $user->roles()
        ->where('name', 'Manager Special Access')
        ->exists();
}

    private function buildQuery(User $user)
{
    $query = Suggestion::query();

    if ($this->isImprovement($user)) {
        return $query;
    }

    if ($this->isSpv($user) || $this->isManager($user)) {

       $deptIds = $user->departments()->pluck('id')->toArray();

return $query->whereIn('department', $deptIds);
    }

    return $query->where('user_id', $user->id);
}
    // ================================================================
    // DASHBOARD
    // ================================================================
    public function index(Request $request): View|\Illuminate\Http\JsonResponse
{
    /*
    |--------------------------------------------------------------------------
    | USER LOGIN
    |--------------------------------------------------------------------------
    */

    $user = $this->currentUser();

    /*
    |--------------------------------------------------------------------------
    | BASE QUERY SESUAI ROLE
    |--------------------------------------------------------------------------
    */

    $baseQuery = $this->buildQuery($user);

    /*
    |--------------------------------------------------------------------------
    | AJAX TABLE (NO DATATABLES)
    |--------------------------------------------------------------------------
    */

   if ($request->ajax()) {
    $query = (clone $baseQuery)
        ->with(['user', 'departments'])
        ->select('suggestions.*');

    /*
    |------------------------------------------------------------------
    | FILTER
    |------------------------------------------------------------------
    */

    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    if ($request->filled('ss_number')) {
        $query->where(
            'ss_number',
            'like',
            '%' . $request->ss_number . '%'
        );
    }

    if ($request->filled('theme')) {
        $query->where(
            'theme',
            'like',
            '%' . $request->theme . '%'
        );
    }

    if ($request->filled('department')) {
        $query->where(
            'department',
            'like',
            '%' . $request->department . '%'
        );
    }

    if ($request->filled('category')) {
        $query->whereJsonContains(
            'categories',
            $request->category
        );
    }

    /*
    |------------------------------------------------------------------
    | PAGINATION
    |------------------------------------------------------------------
    */

    $perPage = (int) $request->get('per_page', 10);
    $page    = (int) $request->get('page', 1);

    $allowedPerPage = [10, 25, 50, 100];

    if (!in_array($perPage, $allowedPerPage)) {
        $perPage = 10;
    }

    $total = (clone $query)->count();

    $rows = $query
        ->latest()
        ->skip(($page - 1) * $perPage)
        ->take($perPage)
        ->get();

    /*
    |------------------------------------------------------------------
    | BUILD HTML ROWS (TANPA PARTIAL)
    |------------------------------------------------------------------
    */

    $html = '';

    foreach ($rows as $row) {
        $applicant = $row->user?->name ?? '-';

        $departmentName = $row->departments?->name
            ?? $row->department
            ?? '-';

        $categories = '—';

        if ($row->categories) {
            $decoded = is_array($row->categories)
                ? $row->categories
                : json_decode($row->categories, true);

            $categories = collect((array) $decoded)
                ->filter()
                ->implode(', ');
        }

        $scoreTotal = $row->score_total
            ? number_format($row->score_total, 1)
            : '—';

        $rewardAmount = $row->reward_amount
            ? 'Rp ' . number_format($row->reward_amount, 0, ',', '.')
            : '—';

        $createdAt = $row->created_at
            ? $row->created_at->format('d M Y')
            : '-';

        /*
        |--------------------------------------------------------------
        | STATUS BADGE
        |--------------------------------------------------------------
        */

        $statusBadge = match ($row->status) {
            'draft' => '<span class="px-2 py-1 text-[11px] rounded-full bg-gray-100 text-gray-700">Draft</span>',
            'submitted' => '<span class="px-2 py-1 text-[11px] rounded-full bg-yellow-100 text-yellow-700">Submitted</span>',
            'approved_spv' => '<span class="px-2 py-1 text-[11px] rounded-full bg-blue-100 text-blue-700">Approved</span>',
            'rejected_spv' => '<span class="px-2 py-1 text-[11px] rounded-full bg-red-100 text-red-700">Rejected</span>',
            'returned_spv' => '<span class="px-2 py-1 text-[11px] rounded-full bg-orange-100 text-orange-700">Returned</span>',
            'scored' => '<span class="px-2 py-1 text-[11px] rounded-full bg-purple-100 text-purple-700">Scored</span>',
            'closed' => '<span class="px-2 py-1 text-[11px] rounded-full bg-green-100 text-green-700">Closed</span>',
            default => '<span class="px-2 py-1 text-[11px] rounded-full bg-gray-100 text-gray-700">-</span>',
        };

        /*
        |--------------------------------------------------------------
        | ACTION BUTTONS
        |--------------------------------------------------------------
        */

       $action = '
    <div class="flex items-center gap-2">
        <button
            type="button"
            onclick="openSlideOver(' . $row->id . ')"
            class="inline-flex items-center px-3 py-1.5 text-xs border border-gray-200 rounded-lg hover:bg-gray-50">
            Lihat
        </button>
';

        if ($this->isSpv($user) && $row->status === 'submitted') {
            $action .= '
                <button type="button"
                    onclick="openSpvModal(' . $row->id . ', \'' . e($row->ss_number) . '\')"
                    class="px-3 py-1.5 text-xs rounded-lg border border-green-200 bg-green-50 text-green-700">
                    Review
                </button>
            ';
        }

        if ($this->isManager($user) && $row->status === 'approved_spv') {
            $action .= '
                <button type="button"
                    onclick="openScoreModal(' . $row->id . ', \'' . e($row->ss_number) . '\')"
                    class="px-3 py-1.5 text-xs rounded-lg border border-purple-200 bg-purple-50 text-purple-700">
                    Nilai
                </button>
            ';
        }

        $action .= '</div>';

        /*
        |--------------------------------------------------------------
        | TABLE ROW
        |--------------------------------------------------------------
        */

  /*
|--------------------------------------------------------------------------
| BUILD HTML ROWS (TANPA PARTIAL)
|--------------------------------------------------------------------------
*/

$html = '';

foreach ($rows as $index => $row) {
    $applicant = $row->user?->name ?? '-';

    $departmentName = $row->departments?->name
        ?? $row->department
        ?? '-';

    $categories = '—';

    if ($row->categories) {
        $decoded = is_array($row->categories)
            ? $row->categories
            : json_decode($row->categories, true);

        $categories = collect((array) $decoded)
            ->filter()
            ->implode(', ');
    }

    $scoreTotal = $row->score_total
        ? number_format($row->score_total, 1)
        : '—';

    $rewardAmount = $row->reward_amount
        ? 'Rp ' . number_format($row->reward_amount, 0, ',', '.')
        : '—';

    $createdAt = $row->created_at
        ? $row->created_at->format('d M Y')
        : '-';

    /*
    |--------------------------------------------------------------------------
    | ROW STYLE (ODD / EVEN)
    |--------------------------------------------------------------------------
    */

    $rowClass = $index % 2 === 0
        ? 'bg-white'
        : 'bg-slate-50/60';

    /*
    |--------------------------------------------------------------------------
    | STATUS BADGE
    |--------------------------------------------------------------------------
    */

    $statusBadge = match ($row->status) {
        'draft' => '<span class="px-2 py-1 text-[11px] rounded-full bg-gray-100 text-gray-700">Draft</span>',
        'submitted' => '<span class="px-2 py-1 text-[11px] rounded-full bg-yellow-100 text-yellow-700">Submitted</span>',
        'approved_spv' => '<span class="px-2 py-1 text-[11px] rounded-full bg-blue-100 text-blue-700">Approved</span>',
        'rejected_spv' => '<span class="px-2 py-1 text-[11px] rounded-full bg-red-100 text-red-700">Rejected</span>',
        'returned_spv' => '<span class="px-2 py-1 text-[11px] rounded-full bg-orange-100 text-orange-700">Returned</span>',
        'scored' => '<span class="px-2 py-1 text-[11px] rounded-full bg-purple-100 text-purple-700">Scored</span>',
        'closed' => '<span class="px-2 py-1 text-[11px] rounded-full bg-green-100 text-green-700">Closed</span>',
        default => '<span class="px-2 py-1 text-[11px] rounded-full bg-gray-100 text-gray-700">-</span>',
    };

    /*
    |--------------------------------------------------------------------------
    | ACTION BUTTONS
    |--------------------------------------------------------------------------
    */

   $action = '
    <div class="flex items-center gap-2">
        <button
            type="button"
            onclick="openSlideOver(' . $row->id . ')"
            class="inline-flex items-center px-3 py-1.5 text-xs border border-gray-200 rounded-lg hover:bg-gray-50">
            Lihat
        </button>
';

    if ($this->isSpv($user) && $row->status === 'submitted') {
        $action .= '
            <button type="button"
                onclick="openSpvModal(' . $row->id . ', \'' . e($row->ss_number) . '\')"
                class="px-3 py-1.5 text-xs rounded-lg border border-green-200 bg-green-50 text-green-700">
                Review
            </button>
        ';
    }

    if ($this->isManager($user) && $row->status === 'approved_spv') {
        $action .= '
            <button type="button"
                onclick="openScoreModal(' . $row->id . ', \'' . e($row->ss_number) . '\')"
                class="px-3 py-1.5 text-xs rounded-lg border border-purple-200 bg-purple-50 text-purple-700">
                Nilai
            </button>
        ';
    }

    $action .= '</div>';

    /*
    |--------------------------------------------------------------------------
    | TABLE ROW (PREMIUM / ENTERPRISE)
    |--------------------------------------------------------------------------
    */

  $html .= '
<div class="px-6 py-5 hover:bg-slate-50 transition border-b border-slate-100">

    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

      
        <div class="min-w-0 flex-1">

          
           <div class="mt-1 flex items-center gap-2 flex-wrap">

    <div class="text-xs font-semibold text-[#1e3a5f] font-mono tracking-wide">
        ' . e($row->ss_number) . '
    </div>

    ' . $statusBadge . '

</div>

          <div class="mt-3 flex flex-wrap gap-2">
    ' . collect($row->categories ?? [])
        ->map(function ($cat) {
            return '
                <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md border border-slate-200 bg-slate-50 text-[10px] font-medium text-slate-500">

                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400 shrink-0"></span>

                    ' . e($cat) . '

                </span>
            ';
        })->implode('') . '
</div>

           
            <div class="mt-1 text-sm font-semibold text-slate-800 leading-snug">
                ' . e($row->theme ?: '-') . '
            </div>

            
            <div class="mt-1 text-xs text-slate-500">
                ' . e($applicant) . ' · ' . e($departmentName) . '
            </div>

           
            

           
            <div class="mt-3 text-xs text-slate-400">
                Submitted ' . e($createdAt) . '
            </div>

        </div>

       
        <div class="flex flex-col sm:flex-row sm:items-center gap-4">

           
            ' . (
                !is_null($row->scored_by_manager)
                    ? '
                    <div class="flex items-center gap-3 min-w-[120px]">

                       ' . (function () use ($row) {
    $score = (float) $row->scored_by_manager;

    if ($score < 3) {
        $borderClass = 'border-red-200';
        $bgClass     = 'bg-red-50';
        $textClass   = 'text-red-700';
    } elseif ($score <= 6) {
        $borderClass = 'border-amber-200';
        $bgClass     = 'bg-amber-50';
        $textClass   = 'text-amber-700';
    } else {
        $borderClass = 'border-emerald-200';
        $bgClass     = 'bg-emerald-50';
        $textClass   = 'text-emerald-700';
    }

    return '
        <div class="w-11 h-11 rounded-full border-2 ' . $borderClass . ' ' . $bgClass . ' flex items-center justify-center shadow-sm">
            <span class="text-sm font-bold ' . $textClass . '">
                ' . number_format($score, 1) . '
            </span>
        </div>
    ';
})() . '
                        <div class="leading-tight">
                            <div class="text-[10px] uppercase tracking-wide text-slate-400 font-semibold">
                                Reward
                            </div>
                            <div class="text-xs font-medium text-slate-600">
                                 ' . e($row->reward_amount) . '
                            </div>
                        </div>

                    </div>
                    '
                    : '
                    <div class="flex items-center gap-3 min-w-[120px]">

                        <div class="w-11 h-11 rounded-full border border-slate-200 bg-slate-50 flex items-center justify-center">
                            <span class="text-xs font-medium text-slate-400">
                                —
                            </span>
                        </div>

                        <div class="leading-tight">
                            <div class="text-[10px] uppercase tracking-wide text-slate-400 font-semibold">
                                Reward
                            </div>
                            <div class="text-xs font-medium text-slate-400">
                                Not yet Scored
                            </div>
                        </div>

                    </div>
                    '
            ) . '


         
           '  . (function () use ($row, $user) {

    $isOwner   = $row->user_id == $user->id;
    $isSpv     = $this->isSpv($user);
    $isManager = $this->isManager($user);

    /*
    |----------------------------------------------------------------------
    | DEFAULT ACTION (SLIDE OVER)
    |----------------------------------------------------------------------
    */

    $baseButton = '
        <button
            type="button"
            onclick="openSlideOver(' . $row->id . ')"
            class="inline-flex items-center justify-center px-4 py-2 text-xs font-medium border rounded-md transition border-slate-300 hover:bg-slate-50 text-slate-700">
            Lihat
        </button>
    ';

    /*
    |----------------------------------------------------------------------
    | SUBMITTED + SPV → REVIEW (MASIH SLIDE OVER, tapi mode review)
    |----------------------------------------------------------------------
    */

    if (
        $row->status === 'submitted' &&
        $isSpv &&
        !$isOwner
    ) {
        return '
            <button
                type="button"
                onclick="openSlideOver(' . $row->id . ', \'review\')"
                class="inline-flex items-center justify-center px-4 py-2 text-xs font-semibold rounded-md border border-emerald-200 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 transition">
                Review
            </button>
        ';
    }

    /*
    |----------------------------------------------------------------------
    | APPROVED SPV + MANAGER → SCORE MODE
    |----------------------------------------------------------------------
    */

    if (
        $row->status === 'approved_spv' &&
        $isManager
    ) {
        return '
            <button
                type="button"
                onclick="openSlideOver(' . $row->id . ', \'score\')"
                class="inline-flex items-center justify-center px-4 py-2 text-xs font-semibold rounded-md border border-violet-200 bg-violet-50 text-violet-700 hover:bg-violet-100 transition">
                Score
            </button>
        ';
    }

    /*
    |----------------------------------------------------------------------
    | RETURNED → OWNER (REVISI MODE)
    |----------------------------------------------------------------------
    */

    if (
        $row->status === 'returned_spv' &&
        $isOwner
    ) {
        return '
            <button
                type="button"
                onclick="openSlideOver(' . $row->id . ', \'revision\')"
                class="inline-flex items-center justify-center px-4 py-2 text-xs font-semibold rounded-md border border-amber-200 bg-amber-50 text-amber-700 hover:bg-amber-100 transition">
                Revisi
            </button>
        ';
    }

    /*
    |----------------------------------------------------------------------
    | REJECTED → VIEW ONLY
    |----------------------------------------------------------------------
    */

    if ($row->status === 'rejected_spv') {
        return '
            <button
                type="button"
                onclick="openSlideOver(' . $row->id . ')"
                class="inline-flex items-center justify-center px-4 py-2 text-xs font-medium border rounded-md transition border-slate-300 hover:bg-slate-50 text-slate-700">
                Lihat
            </button>
        ';
    }

    /*
    |----------------------------------------------------------------------
    | FALLBACK
    |----------------------------------------------------------------------
    */

    return $baseButton;

})() . '

        </div>

    </div>

</div>
';
}
    }

    /*
    |------------------------------------------------------------------
    | RESPONSE JSON
    |------------------------------------------------------------------
    */

    return response()->json([
        'html' => $html,
        'total' => $total,
        'page' => $page,
        'per_page' => $perPage,
        'last_page' => (int) ceil($total / $perPage),
        'from' => $total ? (($page - 1) * $perPage) + 1 : 0,
        'to' => min($page * $perPage, $total),
    ]);
}

    /*
    |--------------------------------------------------------------------------
    | STATS CARDS
    |--------------------------------------------------------------------------
    */

    $stats = [
        'total'        => (clone $baseQuery)->count(),
        'draft'        => (clone $baseQuery)->where('status', 'draft')->count(),
        'submitted'    => (clone $baseQuery)->where('status', 'submitted')->count(),
        'approved_spv' => (clone $baseQuery)->where('status', 'approved_spv')->count(),
        'scored'       => (clone $baseQuery)->where('status', 'scored')->count(),
        'closed'       => (clone $baseQuery)->where('status', 'closed')->count(),
        'total_reward' => (clone $baseQuery)
            ->whereNotNull('reward_amount')
            ->sum('reward_amount'),
    ];

    /*
    |--------------------------------------------------------------------------
    | INITIAL EMPTY STATE
    |--------------------------------------------------------------------------
    */

    $suggestions = null;

    /*
    |--------------------------------------------------------------------------
    | ACTIVE PERIOD
    |--------------------------------------------------------------------------
    */

    $activePeriod = SuggestionPeriod::where('is_active', true)
        ->latest()
        ->first();

    /*
|--------------------------------------------------------------------------
| DEPARTMENT SUMMARY
|--------------------------------------------------------------------------
*/

$deptSummary = null;
$deptSSStats = null;
$deptQuery   = null;

if ($this->isImprovement($user) || $this->isManager($user)) {

    $deptQuery = $this->isImprovement($user)
        ? Suggestion::query()
        : Suggestion::whereIn(
            'department',
            $user->departments()->pluck('name')->toArray()
        );

    // SUMMARY
    $deptSummary = (clone $deptQuery)
        ->selectRaw('
            department,
            COUNT(*) as total,
            SUM(CASE WHEN status = "closed" THEN 1 ELSE 0 END) as closed,
            COALESCE(SUM(reward_amount), 0) as total_reward,
            AVG(score_total) as avg_score
        ')
        ->groupBy('department')
        ->orderByDesc('total')
        ->get();

    // STATS
    $deptSSStats = (clone $deptQuery)
        ->selectRaw('
            count(*) as total,
            sum(status="submitted") as submitted,
            sum(status in ("approved_spv","approved_manager")) as approved,
            sum(status="rejected_spv") as rejected,
            sum(status="returned_spv") as returned,
            sum(status="scored") as scored,
            sum(status="closed") as closed
        ')
        ->first();
}

    /*
    |--------------------------------------------------------------------------
    | TOP SS
    |--------------------------------------------------------------------------
    */

    $topSS = null;

    if ($this->isImprovement($user) || $this->isManager($user)) {
        $topQuery = $this->isImprovement($user)
            ? Suggestion::query()
            : Suggestion::where(
                'department',
                $user->departments->first()?->name ?? ''
            );

        $topSS = $topQuery
            ->with('user')
            ->whereNotNull('score_total')
            ->orderByDesc('score_total')
            ->limit(8)
            ->get();
    }

    // Stats SS milik user sendiri
$mySSStats = \App\Models\Suggestion::where('user_id', $user->id)
    ->selectRaw('
        count(*) as total,
        sum(status="submitted") as submitted,
        sum(status in ("approved_spv","approved_manager")) as approved,
        sum(status="rejected_spv") as rejected,
        sum(status="returned_spv") as returned,
        sum(status="scored") as scored,
        sum(status="closed") as closed
    ')->first();


// Karyawan aktif + kategori terbanyak di periode aktif
$activeSubmittersCount = $activePeriod
    ? \App\Models\Suggestion::where('period_id', $activePeriod->id)
        ->distinct('user_id')->count('user_id')
    : 0;

$topCategories = $activePeriod
    ? \App\Models\Suggestion::where('period_id', $activePeriod->id)
        ->selectRaw('categories, count(*) as total')
        ->groupBy('categories')->orderByDesc('total')->get()
    : collect();

    /*
    |--------------------------------------------------------------------------
    | ANALYTICS
    |--------------------------------------------------------------------------
    */

    $analyticsData = [];

    /*
    |--------------------------------------------------------------------------
    | PERIODS
    |--------------------------------------------------------------------------
    */

    $periods = $this->isImprovement($user)
        ? SuggestionPeriod::orderByDesc('start_date')->paginate(10)
        : null;

    /*
    |--------------------------------------------------------------------------
    | ACTIVE FORMULA
    |--------------------------------------------------------------------------
    */

    $activeFormula = SuggestionRewardFormula::where('is_active', true)
        ->with(['items', 'tiers'])
        ->first();

    /*
    |--------------------------------------------------------------------------
    | RETURN VIEW
    |--------------------------------------------------------------------------
    */

    return view('ss-dashboard', compact(
        'user',
        'stats',
        'suggestions',
        'activePeriod',
        'deptSummary',
        'topSS',
        'mySSStats',
        'activeSubmittersCount',
        'topCategories',
        'deptSSStats',
        'analyticsData',
        'periods',
        'activeFormula'
    ) + [
        'isSpv'         => $this->isSpv($user),
        'isManager'     => $this->isManager($user),
        'isImprovement' => $this->isImprovement($user),
    ]);
}

    // ================================================================
    // CREATE — tampilkan form
    // ================================================================
    public function create(): View
    {
        $user         = $this->currentUser();
        $activePeriod = SuggestionPeriod::where('is_active', true)->first();

        // Cek batas submit per periode
        if ($activePeriod && $activePeriod->max_submissions) {
            $countThisPeriod = Suggestion::where('user_id', $user->id)
                ->where('period_id', $activePeriod->id)
                ->whereNotIn('status', ['draft'])
                ->count();

            if ($countThisPeriod >= $activePeriod->max_submissions) {
                return redirect()->route('suggestion.dashboard')
                    ->with('error', "Batas pengajuan periode ini ({$activePeriod->max_submissions} SS) telah tercapai.");
            }
        }

        return view('ss-create', compact('user', 'activePeriod'));
    }

    // ================================================================
    // STORE — simpan (draft atau submit)
    // ================================================================
    public function storeDraft(Request $request): RedirectResponse
{
    $user = $this->currentUser();

    $rules = [
        'categories'     => ['required', 'array', 'min:1'],
        'theme'          => ['required', 'string', 'max:255'],
        'discovery_date' => ['required', 'date'],
        'location'       => ['required', 'string', 'max:255'],
        'background'     => ['required', 'string'],
    ];

    $data = $request->validate($rules);

    return $this->saveSuggestion($request, $user, $data, 'draft');
}

public function submit(Request $request)
{
    $user = $this->currentUser();

    $rules = [
        'categories'     => ['required', 'array', 'min:1'],
        'theme'          => ['required', 'string', 'max:255'],
        'discovery_date' => ['required', 'date'],
        'location'       => ['required', 'string', 'max:255'],
        'background'     => ['required', 'string'],
        'root_cause'     => ['required', 'string'],
    ];

    $validator = Validator::make($request->all(), $rules);

    // 🔥 HANDLE ERROR AJAX
    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => $validator->errors()->first()
        ], 422);
    }

    return $this->saveSuggestion(
        $request,
        $user,
        $validator->validated(),
        'submitted'
    );
}

private function saveSuggestion(Request $request, $user, $data, $status)
{
    $activePeriod = SuggestionPeriod::where('is_active', true)->first();
    $now = now();

    $suggestion = Suggestion::create([
        'user_id' => $user->id,
        'ss_number' => Suggestion::generateSsNumber(),
        'categories' => $data['categories'],
        'theme' => $data['theme'],
        'department' => $user->departments->first()?->id,
        'discovery_date' => $data['discovery_date'],
        'location' => $data['location'],
        'background' => $data['background'],
        'root_cause' => $request->root_cause,
        'improvement_activity' => $request->improvement_activity,
        'evaluation_result' => $request->evaluation_result,
        'standardization' => $request->standardization,
        'status' => $status,
        'period_id' => $activePeriod?->id,
        'draft_at' => $status === 'draft' ? $now : null,
        'submitted_at' => $status === 'submitted' ? $now : null,
    ]);

    $this->uploadPhotos($request, $suggestion);

    return response()->json([
        'success'  => true,
        'message'  => $status === 'submitted'
            ? "SS #{$suggestion->ss_number} berhasil diajukan!"
            : "Draft berhasil disimpan.",
        'redirect' => route('suggestion.dashboard'),
    ]);
}

    public function storeFormula(Request $request): JsonResponse
{
    $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'items' => ['required', 'array', 'min:1'],
        'items.*.item_name' => ['required', 'string', 'max:255'],

        'items.*.criterias' => ['required', 'array', 'min:1'],
        'items.*.criterias.*.grade' => ['required', 'string', 'max:50'],
        'items.*.criterias.*.min_point' => ['required', 'numeric', 'min:0'],
        'items.*.criterias.*.max_point' => ['required', 'numeric', 'min:0'],
        'items.*.criterias.*.description' => ['required', 'string'],

        'tiers' => ['required', 'array', 'min:1'],
        'tiers.*.min_score' => ['required', 'numeric', 'min:0'],
        'tiers.*.max_score' => ['required', 'numeric', 'min:0'],
        'tiers.*.reward_amount' => ['required', 'numeric', 'min:0'],

        'notes' => ['nullable', 'string'],
        'is_active' => ['nullable'],
    ], [
        'name.required' => 'Nama formula wajib diisi.',
        'items.required' => 'Minimal 1 item penilaian wajib dibuat.',
        'tiers.required' => 'Minimal 1 tier reward wajib dibuat.',
    ]);

    DB::beginTransaction();

    try {
        /*
        |--------------------------------------------------------------------------
        | Jika formula baru diaktifkan → nonaktifkan formula lama
        |--------------------------------------------------------------------------
        */
        if ($request->boolean('is_active')) {
            SuggestionRewardFormula::query()
                ->update([
                    'is_active' => false
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Simpan Formula Utama
        |--------------------------------------------------------------------------
        */
        $formula = SuggestionRewardFormula::create([
            'name'       => $request->name,
            'is_active'  => $request->boolean('is_active'),
            'notes'      => $request->notes,
            'created_by' => $this->currentUser()->id,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Simpan Item + Criteria
        |--------------------------------------------------------------------------
        */
        foreach ($request->items as $itemIndex => $itemData) {

            $item = SuggestionRewardFormulaItem::create([
                'formula_id' => $formula->id,
                'item_name'  => $itemData['item_name'],
                'sort_order' => $itemIndex + 1,
            ]);

            foreach ($itemData['criterias'] as $criteriaIndex => $criteriaData) {
                SuggestionRewardFormulaItemCriteria::create([
                    'item_id'     => $item->id,
                    'grade'       => $criteriaData['grade'],
                    'min_point'   => $criteriaData['min_point'],
                    'max_point'   => $criteriaData['max_point'],
                    'description' => $criteriaData['description'],
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Simpan Reward Tier
        |--------------------------------------------------------------------------
        */
        foreach ($request->tiers as $tierIndex => $tierData) {
            SuggestionRewardTier::create([
                'formula_id'    => $formula->id,
                'min_score'     => $tierData['min_score'],
                'max_score'     => $tierData['max_score'],
                'reward_amount' => $tierData['reward_amount'],
                'sort_order'    => $tierIndex + 1,
            ]);
        }

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Formula penilaian berhasil disimpan.',
            'data' => [
                'formula_id' => $formula->id,
                'name'       => $formula->name,
                'is_active'  => $formula->is_active,
            ]
        ]);

    } catch (\Throwable $e) {
        DB::rollBack();

        return response()->json([
            'success' => false,
            'message' => 'Gagal menyimpan formula.',
            'error'   => $e->getMessage(),
        ], 500);
    }
}

    // ================================================================
    // SHOW — detail SS
    // ================================================================
    public function detail($id)
{
    $user = $this->currentUser();

    $s = Suggestion::with([
        'user',
        'departments',
        'photosBefore',
        'photosAfter',
        'scores'
    ])->findOrFail($id);

    return response()->json([
        'id'        => $s->id,
        'ss_number' => $s->ss_number,
        'theme'     => $s->theme,
        'user'      => $s->user?->name,
        'department'=> $s->departments?->name ?? $s->department,
        'status'    => $s->status,
        'status_label' => $s->status_label,
        'status_color' => $s->status_color,
        'period' => $s->period?->name,
        'discovery_date' => optional($s->discovery_date)->format('d M Y'),
        'location' => $s->location,
        'evaluation_result' => $s->evaluation_result,
        'standardization' => $s->standardization,
        'background'   => $s->background,
        'root_cause'      => $s->root_cause,
        'improvement_activity'  => $s->improvement_activity,

        'categories'=> $s->categories,

        'photos_before' => $s->photosBefore,
        'photos_after'  => $s->photosAfter,
         'scores' => $s->scores->map(fn($sc) => [
    'score'        => $sc->score,
    'formula_item' => ['item_name' => $sc->formulaItem?->item_name],
]),
    'reward_amount' => $s->reward_amount,
    'score_total' => $s->score_total,
'manager_note' => $s->manager_note,
'user_name'          => $s->user?->name,
'draft_at'           => $s->draft_at?->format('d M Y, H:i') . ' WIB',
'submitted_at'       => $s->submitted_at?->format('d M Y, H:i') . ' WIB',
'reviewed_at_spv'    => $s->reviewed_at_spv?->format('d M Y, H:i') . ' WIB',
'reviewed_by_spv'    => $s->spvReviewer?->name,
'scored_at'          => $s->scored_at?->format('d M Y, H:i') . ' WIB',
'scored_by_manager'  => $s->managerScorer?->name,
'closed_at'          => $s->closed_at?->format('d M Y, H:i') . ' WIB',
'acknowledge_by'     => $s->acknowledgedBy?->name,
        // 🔥 INI PENTING
        'actions'   => $s->getAvailableActions($user),
    ]);
}

    // ================================================================
    // UPDATE — update parsial
    // ================================================================
    public function update(Request $request, Suggestion $suggestion): RedirectResponse
    {
        $user = $this->currentUser();

        abort_unless(
            $suggestion->user_id === $user->id
            && in_array($suggestion->status, ['draft', 'returned_spv']),
            403
        );

        $action = $request->input('action', 'save');

        $data = $request->only([
            'categories', 'theme', 'discovery_date', 'location',
            'background', 'root_cause',
            'improvement_activity', 'evaluation_result', 'standardization',
        ]);

        // Recalculate step
        $step = 1;
        if ($suggestion->background || $request->filled('background'))             $step = 1;
        if ($suggestion->root_cause || $request->filled('root_cause'))             $step = 2;
        if ($suggestion->improvement_activity || $request->filled('improvement_activity')) $step = 3;
        if ($suggestion->photosBefore()->exists() || $request->hasFile('photos_before') ||
            $suggestion->photosAfter()->exists()  || $request->hasFile('photos_after'))   $step = 4;
        if (($suggestion->evaluation_result || $request->filled('evaluation_result')) &&
            ($suggestion->standardization   || $request->filled('standardization')))      $step = 5;

        $data['completion_step'] = $step;

        if ($action === 'submit') {
            abort_unless($suggestion->root_cause || $request->filled('root_cause'), 422,
                'Analisa penyebab masalah harus diisi sebelum mengajukan.');
            $data['status'] = 'submitted';
        }

        $suggestion->update($data);
        $this->uploadPhotos($request, $suggestion);

        return redirect()->route('suggestion.show', $suggestion->id)
            ->with('success', $action === 'submit' ? 'SS berhasil diajukan!' : 'SS berhasil diperbarui.');
    }

    // ================================================================
    // SPV ACTIONS: Approve / Reject / Return
    // ================================================================
    public function spvAction(Request $request, Suggestion $suggestion): \Illuminate\Http\JsonResponse
{
    $user = $this->currentUser();

    abort_unless(
        $suggestion->status === 'submitted',
        422,
        'Hanya SS dengan status "Menunggu Review" yang bisa diproses.'
    );

    $request->validate([
        'action' => ['required', 'in:approve,reject,return'],
        'note'   => [
            'required_if:action,reject',
            'required_if:action,return',
            'nullable',
            'string'
        ],
    ]);

    $status = match ($request->action) {
        'approve' => Suggestion::STATUS_APPROVED_SPV,
        'reject'  => Suggestion::STATUS_REJECTED_SPV,
        'return'  => Suggestion::STATUS_RETURNED_SPV,
    };

    $suggestion->update([
        'status'           => $status,
        'reviewed_by_spv'  => $user->id,
        'reviewed_at_spv'  => now(),
        'spv_note'         => $request->note,
    ]);

    $label = match ($request->action) {
        'approve' => 'disetujui',
        'reject'  => 'ditolak',
        'return'  => 'dikembalikan',
    };

    return response()->json([
        'success' => true,
        'message' => "SS #{$suggestion->ss_number} berhasil {$label}.",
        'status'  => $status,
    ]);
}

    // ================================================================
    // MANAGER SCORING
    // ================================================================

public function score(Request $request, Suggestion $suggestion): RedirectResponse
{
    $user = $this->currentUser();


    abort_unless(
        $suggestion->status === Suggestion::STATUS_APPROVED_SPV,
        422,
        'Hanya SS yang sudah disetujui SPV yang bisa dinilai.'
    );

    // Ambil formula aktif beserta item dan tier
    $formula = SuggestionRewardFormula::with([
        'items',
        'tiers'
    ])
    ->where('is_active', true)
    ->first();

    abort_unless(
        $formula,
        422,
        'Formula penilaian aktif tidak ditemukan.'
    );

    /*
    |--------------------------------------------------------------------------
    | VALIDASI
    |--------------------------------------------------------------------------
    */

    $rules = [
        'scores'       => ['required', 'array'],
        'manager_note' => ['nullable', 'string'],
    ];

    foreach ($formula->items as $item) {
        $rules["scores.{$item->id}"] = [
            'required',
            'numeric',
            'min:0',
            'max:10',
        ];
    }

    $validated = $request->validate($rules);

    $scores = $validated['scores'];

    /*
    |--------------------------------------------------------------------------
    | HITUNG TOTAL + AVERAGE
    |--------------------------------------------------------------------------
    */

    $totalScore = collect($scores)->sum();

    $averageScore = count($scores) > 0
        ? round($totalScore / count($scores), 2)
        : 0;

    /*
    |--------------------------------------------------------------------------
    | HITUNG REWARD BERDASARKAN TIER
    |--------------------------------------------------------------------------
    */

    $reward = 0;

    foreach ($formula->tiers as $tier) {
        if (
            $averageScore >= $tier->min_score &&
            $averageScore <= $tier->max_score
        ) {
            $reward = $tier->reward_amount;
            break;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | SIMPAN DATA
    |--------------------------------------------------------------------------
    */

    DB::transaction(function () use (
        $suggestion,
        $user,
        $scores,
        $averageScore,
        $reward,
        $validated
    ) {
        // Hapus score lama jika pernah dinilai ulang
        $suggestion->scores()->delete();

        // Simpan detail score per item
        foreach ($scores as $itemId => $scoreValue) {
            SuggestionScore::create([
                'suggestion_id'   => $suggestion->id,
                'formula_item_id' => $itemId,
                'score'           => $scoreValue,
            ]);
        }

        // Update summary di tabel suggestions
        $suggestion->update([
            'status'            => Suggestion::STATUS_SCORED,
            'scored_by_manager' => $user->id,
            'scored_at'         => now(),
            'score_total'       => $averageScore,
            'reward_amount'     => $reward,
            'manager_note'      => $validated['manager_note'] ?? null,
        ]);
    });

    return redirect()
        ->route('suggestion.show', $suggestion->id)
        ->with(
            'success',
            "Penilaian SS #{$suggestion->ss_number} berhasil disimpan"
        );
}

    // ================================================================
    // CLOSE SS (Improvement only)
    // ================================================================
    public function close(Suggestion $suggestion): RedirectResponse
    {
        $user = $this->currentUser();
        abort_unless($this->isImprovement($user), 403);

        $suggestion->update(['status' => Suggestion::STATUS_CLOSED]);

        return back()->with('success', "SS #{$suggestion->ss_number} berhasil ditutup.");
    }

    // ================================================================
    // MY SUGGESTIONS (list khusus milik sendiri)
    // ================================================================
    public function mySuggestions(): View
    {
        $user        = $this->currentUser();
        $suggestions = Suggestion::where('user_id', $user->id)
            ->with('period')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('suggestion.my-suggestions', compact('user', 'suggestions'));
    }

    // ================================================================
    // UPLOAD FOTO HELPER
    // ================================================================
   private function uploadPhotos(Request $request, Suggestion $suggestion): void
{
    foreach (['before', 'after'] as $type) {

        $field = "photos_{$type}";

        if (!$request->hasFile($field)) continue;

        foreach ($request->file($field) as $file) {

            // 🔒 skip kalau file tidak valid
            if (!$file || !$file->isValid()) continue;

            // 🔒 ambil dulu sebelum move
            $originalName = $file->getClientOriginalName();
            $size         = $file->getSize();
            $ext          = $file->getClientOriginalExtension();

            $filename = uniqid() . '.' . $ext;

            $destination = public_path("suggestions/{$suggestion->id}/{$type}");

            if (!file_exists($destination)) {
                mkdir($destination, 0777, true);
            }

            // 🔥 move terakhir
            $file->move($destination, $filename);

            SuggestionPhoto::create([
                'suggestion_id' => $suggestion->id,
                'type'          => $type,
                'file_path'     => "suggestions/{$suggestion->id}/{$type}/{$filename}",
                'file_name'     => $originalName,
                'file_size'     => $size,
            ]);
        }
    }
}

    // ================================================================
    // DELETE FOTO
    // ================================================================
    public function deletePhoto(SuggestionPhoto $photo): RedirectResponse
    {
        $user = $this->currentUser();
        $suggestion = $photo->suggestion;

        abort_unless($suggestion->user_id === $user->id, 403);

        Storage::disk('public')->delete($photo->file_path);
        $photo->delete();

        return back()->with('success', 'Foto berhasil dihapus.');
    }
}