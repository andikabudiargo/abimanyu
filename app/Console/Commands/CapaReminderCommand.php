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

          foreach ($actions as $action) {

    // Ambil department representative
    $representative = $action->capa?->representative;
    if (!$representative || !$representative->email) continue;

    // Ambil departemen representative
    $dept = $representative->departments()->first(); // asumsi satu department
    $ccUsers = [];

    if ($dept) {
        // Ambil semua user di departemen tersebut yang punya role 'Supervisor Special Access'
        $ccUsers = User::whereHas('departments', function ($q) use ($dept) {
                        $q->where('departments.id', $dept->id);
                    })
                    ->whereHas('roles', function ($q2) {
                        $q2->where('name', 'Supervisor Special Access'); // cek role pivot role_user
                    })
                    ->whereNotNull('email')
                    ->pluck('email')
                    ->toArray();
    }

    // Kirim email
    Mail::to($representative->email)
        ->cc($ccUsers)
        ->send(new CapaReminderMail($action));
}

    return 0;
}
}
