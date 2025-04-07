<?php

namespace App\Console\Commands;

use App\Jobs\OrderPermanentlyDeleteJob;
use Illuminate\Console\Command;

class OrderPermanentlyDeleteCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:delete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        dispatch(new OrderPermanentlyDeleteJob());
    }
}
