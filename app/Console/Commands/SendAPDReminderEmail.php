<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\APDDistributionItem;
use Illuminate\Support\Facades\Mail;
use App\Mail\APDReminderMail;

class SendAPDReminderEmail extends Command
{
    protected $signature = 'apd:send-reminder';
    protected $description = 'Kirim email reminder APD 2 bulan sebelum habis masa pakai';

    public function handle()
    {
        $now = Carbon::now();
        $items = APDDistributionItem::with(['apd', 'receiverUser', 'distribution'])->get();
        $reminderItems = [];

        foreach ($items as $item) {
            if (!$item->receiverUser || !$item->distribution) continue;

            $lifetime = is_numeric($item->apd->lifetime) && $item->apd->lifetime > 0
                        ? (int)$item->apd->lifetime
                        : 12;

            $distribution_date = $item->distribution->distribution_date;
            if (!$distribution_date) continue;

            $due_date = Carbon::parse($distribution_date)->addMonths($lifetime);
            $reminder_date = $due_date->copy()->subMonths(2);

            if ($now->toDateString() === $reminder_date->toDateString()) {
                $remaining = $item->qty - ($item->qty_return ?? 0);
                if ($remaining <= 0) continue;

                $reminderItems[] = [
                    'name' => $item->receiverUser->name,
                    'department' => $item->receiverUser->position->name ?? '-',
                    'apd_name' => $item->apd->name,
                    'due' => $due_date->format('Y-m-d'),
                    'status' => "APD Masih Di Karyawan",
                ];
            }
        }

        if (count($reminderItems) > 0) {
            Mail::to('admin.generalaffair@asnusantara.co.id')->send(new APDReminderMail($reminderItems));
        }

        $this->info('APD reminder sent: ' . count($reminderItems));
    }
}
