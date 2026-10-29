<?php

namespace App\Console\Commands;

use App\Mail\BalanceDueReminder;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendBalanceReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:send-balance-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send balance reminders for preorder orders that are due or overdue';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $orders = Order::with('member')
            ->whereNotNull('balance_due_date')
            ->whereIn('status', [
                Order::STATUS_PAID,
                Order::STATUS_PROCESSING,
                Order::STATUS_READY_TO_SHIP,
            ])
            ->whereDate('balance_due_date', '<=', today())
            ->get();

        $sent = 0;

        foreach ($orders as $order) {
            $paid = (int) $order->payments()->where('status', Payment::STATUS_VERIFIED)->sum('amount');
            if ($order->total <= $paid) {
                continue;
            }

            if ($order->balance_reminder_sent_at && $order->balance_reminder_sent_at->isToday()) {
                continue;
            }

            $due = $order->total - $paid;
            $email = $order->member?->email ?? null;

            if ($email) {
                try {
                    Mail::to($email)->send(new BalanceDueReminder($order));
                } catch (\Throwable $e) {
                    Log::error('balance_reminder_failed', [
                        'order_id' => $order->id,
                        'email' => $email,
                        'error' => $e->getMessage(),
                    ]);

                    continue;
                }

                Log::info('balance_reminder', [
                    'order_id' => $order->id,
                    'order_no' => $order->order_no,
                    'email' => $email,
                    'due' => $due,
                    'balance_due_date' => $order->balance_due_date->toDateString(),
                ]);
            }

            $order->update(['balance_reminder_sent_at' => now()]);
            $sent++;
        }

        $this->info("Processed {$sent} balance reminders.");

        return self::SUCCESS;
    }
}
