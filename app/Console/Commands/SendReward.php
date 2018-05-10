<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendReward extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'raffle:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send gift card rewards to winner';

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
     * @return mixed
     */
    public function handle()
    {
        //
    }
}
