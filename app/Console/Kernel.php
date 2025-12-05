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
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Jalankan auto close tickets tiap menit
        $schedule->command('tickets:autoclose')
            ->everyMinute()
            ->before(function () {
                \Log::info('Scheduler tickets:autoclose mulai jalan');
            })
            ->after(function () {
                \Log::info('Scheduler tickets:autoclose selesai jalan');
            });

        // 🔥 Sync fingerprint setiap 1 menit
        $schedule->command('finger:sync')->everyMinute();
        
         $schedule->command('apd:send-reminder')->dailyAt('09:00');
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
