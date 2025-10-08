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
        // Daftarkan command custom kamu di sini
        \App\Console\Commands\AutoCloseTickets::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Jalankan command tickets:autoclose setiap menit (untuk testing)
        $schedule->command('tickets:autoclose')
            ->everyMinute()
            ->before(function () {
                \Log::info('Scheduler tickets:autoclose mulai jalan');
            })
            ->after(function () {
                \Log::info('Scheduler tickets:autoclose selesai jalan');
            });
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        // Load semua command di app/Console/Commands
        $this->load(__DIR__.'/Commands');

        // Load routes/console.php (wajib)
        require base_path('routes/console.php');
    }

    /**
     * Timezone untuk schedule.
     */
    protected function scheduleTimezone(): string
    {
        return 'Asia/Jakarta';
    }
}
