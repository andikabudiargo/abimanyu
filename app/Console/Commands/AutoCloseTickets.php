<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Ticket;

class AutoCloseTickets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tickets:autoclose';

    /**
     * The console command description.
     *
     * @var string
     */
   protected $description = 'Auto close tickets older than 1 days if still done';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $affected = Ticket::where('status', 'Done')
            ->whereNull('closed_at')
            ->where('done_at', '<=', now()->subDay())
            ->update([
                'status'    => 'Closed',
                'closed_at' => now(),
            ]);

        $this->info("Auto closed {$affected} tickets.");
    }
}
