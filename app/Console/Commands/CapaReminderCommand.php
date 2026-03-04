<?php

namespace App\Console\Commands;

use App\Models\CAPAAction;
use Illuminate\Console\Command;
use App\Models\Department;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\CapaReminderMail;

class CapaReminderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'capa:reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
  public function handle()
{
    $today = Carbon::today();

    $dates = [
        $today->copy()->addDays(3),
        $today,
        $today->copy()->subDays(3),
    ];

    $actions = CAPAAction::with('capa.representative')
        ->whereIn('type', ['CA', 'PA'])
        ->where('status', '!=', 'Closed')
        ->whereIn('due_date', $dates)
        ->get();

    foreach ($actions as $action) {

        // Ambil department representative
        $representative = $action->capa?->representative;
        if (!$representative) continue;

     // Ambil SEMUA departemen milik representative
$depts = $representative->departments;
if ($depts->isEmpty()) continue;

// TO: Supervisor dari semua departemen yang SAMA dengan dept_representative
$deptIds = $depts->pluck('id')->toArray();

$supervisors = User::whereHas('departments', function ($q) use ($deptIds) {
                    $q->whereIn('departments.id', $deptIds);
                })
                ->whereHas('roles', function ($q) {
                    $q->where('name', 'Supervisor Special Access');
                })
                ->whereNotNull('email')
                ->pluck('email')
                ->toArray();

        if (empty($supervisors)) continue;

        // CC: User dari departemen yang bernama "Management Representative"
        $mgmtRepDept = Department::where('name', 'Management Representative')->first();
        $ccUsers = [];

        if ($mgmtRepDept) {
            $ccUsers = User::whereHas('departments', function ($q) use ($mgmtRepDept) {
                            $q->where('departments.id', $mgmtRepDept->id);
                        })
                        ->whereNotNull('email')
                        ->pluck('email')
                        ->toArray();
        }

        // Kirim email
        Mail::to($supervisors)
            ->cc($ccUsers)
            ->send(new CapaReminderMail($action));
    }

    return 0;
}

}
