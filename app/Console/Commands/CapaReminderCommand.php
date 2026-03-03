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
        $today->copy()->addDays(3),  // H-3
        $today,                     // H
        $today->copy()->subDays(3),  // H+3
    ];

    $actions = CAPAAction::with('capa.representative')
        ->whereIn('type', ['CA','PA'])
        ->where('status', 'Open')
        ->whereIn('due_date', $dates)
        ->get();

            // Ambil ID department ManagementRepresentative
$dept = Department::where('name', 'Management Representative')->first();
$ccUsers = [];

if ($dept) {
    $ccUsers = User::whereHas('departments', function ($q) use ($dept) {
                    $q->where('departments.id', $dept->id);
                })
                ->whereNotNull('email')
                ->pluck('email')
                ->toArray();
}

foreach ($actions as $action) {
    $representative = $action->capa?->representative;
    if (!$representative || !$representative->email) continue;

    Mail::to($representative->email)
        ->cc($ccUsers)
        ->send(new CapaReminderMail($action));
}

    return 0;
}
}
