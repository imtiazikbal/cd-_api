<?php

namespace App\Jobs;

use App\Services\Sms;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendSmsJob implements ShouldQueue
{
    use Dispatchable;

    use InteractsWithQueue;

    use Queueable;

    use SerializesModels;

    public $phone;

    public $msg;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($phone, $msg)
    {
        $this->phone = $phone;
        $this->msg = $msg;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $sms = new Sms();
        $sms->send($this->phone, $this->msg);
    }
}
