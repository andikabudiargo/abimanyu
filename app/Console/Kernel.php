<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array<int, class-string>
     */
    protected $commands = [
        \App\Console\Commands\AutoCloseTickets::class,
        \App\Console\Commands\SyncFingerLogs::class, // ← wajib daftar
         \App\Console\Commands\CapaReminderCommand::class, // ← wajib daftar
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Jalankan auto close tickets tiap menit
        $schedule->command('tickets:autoclose')
            ->everyMinute();

        // 🔥 Sync fingerprint setiap 1 menit
        $schedule->command('finger:sync')->everyMinute();
        
         $schedule->command('apd:send-reminder')->dailyAt('09:00');

         $schedule->command('capa:reminder')->dailyAt('15:20');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }

    /**
     * Timezone schedule.
     */
    protected function scheduleTimezone(): string
    {
        return 'Asia/Jakarta';
    }
}
