<?php

namespace App\Jobs;

use App\Models\Fraud;
use App\Models\FraudNote;
use App\Services\FraudCheckerService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class FraudChecker implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $number;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $number)
    {
        $this->number = $this->format_number($number);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $checker = new FraudCheckerService();
        $fraud_data = $checker->check($this->number);
        $cache_key = "fraud_by_{$this->number}";
        $frauds_report = (object) [
            'fraud_entry' => 0,
            'fraud_delivery' => 0,
            'fraud_return' => 0,
            'fraud_report' => 0,
            'fraud_processing' => true,
        ];

        if ($fraud_data->status == 'error') {
            if (!Cache::has($cache_key)) {
                Cache::put($cache_key, $frauds_report);
            }
            return; // rerun void
        }

        $couriers_report = [];
        $frauds = $fraud_data->frauds;

        foreach ($frauds as $fraud_info) {
            $courier = $fraud_info->courier;
            $orders = $fraud_info->total_orders;
            $delivered = $fraud_info->delivered_orders;
            $cancelled = $fraud_info->cancelled_orders;
            $cancel_percent = $fraud_info->cancel_percent;
            $success_percent = $fraud_info->success_percent;
            $details = $fraud_info->details;

            $fraud = Fraud::query()
                ->where('courier', $courier)
                ->where('number', $this->number)
                ->first();

            // Create new fraud if not exists
            if ($fraud == null) {
                $fraud = new Fraud();
                $fraud->number = $this->number;
                $fraud->courier = $courier;
                $fraud->orders = $orders;
                $fraud->delivered = $delivered;
                $fraud->cancelled = $cancelled;
                $fraud->cancel_percent = $cancel_percent;
                $fraud->success_percent = $success_percent;
                $fraud->save();
            } elseif ($orders > $fraud->orders || $delivered > $fraud->delivered || $cancelled > $fraud->cancelled) {
                // Update only if any data changed
                $fraud->orders = $orders;
                $fraud->delivered = $delivered;
                $fraud->cancelled = $cancelled;
                $fraud->cancel_percent = $cancel_percent;
                $fraud->success_percent = $success_percent;
                $fraud->save();
            }

            $frauds_report->fraud_entry += (int)$fraud->orders;
            $frauds_report->fraud_delivery += (int)$fraud->delivered;
            $frauds_report->fraud_return += (int)$fraud->cancelled;

            $courier_report = (object) [
                'id' => $fraud->id,
                'number' => $fraud->number,
                'courier' => $fraud->courier,
                'orders' => $fraud->orders,
                'delivered' => $fraud->delivered,
                'cancelled' => $fraud->cancelled,
            ];

            // Count total fraud notes by fraud
            $total_fn = FraudNote::query()
                ->where('fraud_id', $fraud->id)
                ->count();

            $courier_report->fraud_report = $total_fn;

            // Continue to next if same or less
            if ($total_fn >= count($details)) {
                $frauds_report->fraud_report += $total_fn;
                $couriers_report[] = $courier_report;
                continue;
            }

            // Latest data
            $details = array_slice($details, $total_fn);

            foreach ($details as $detail) {
                $fraud_note = new FraudNote();
                $fraud_note->fraud_id = $fraud->id;
                $fraud_note->courier_uid = $detail->id;
                $fraud_note->phone = $detail->phone;
                $fraud_note->name = $detail->name;
                $fraud_note->note = $detail->details;
                $fraud_note->mark_at = $detail->created_at;
                $fraud_note->save();
            }

            $courier_report->fraud_report += count($details);
            $couriers_report[] = $courier_report;
        }

        $frauds_report->fraud_processing = false;
        Cache::put($cache_key, $frauds_report);

        $cache_f_couriers_key = "f_couriers_by_{$this->number}";
        Cache::put($cache_f_couriers_key, collect($couriers_report));
    }

    /**
     * Format Number
     * @param string $number
     * @return string $number
     * @version 1.0
     * @since 1.0
     */
    private function format_number(string $number): string
    {
        $number = preg_replace('/[^\d+]/', '', $number);
        $number = ltrim($number, '88');
        return '0' . ltrim($number, '0');
    }
}
