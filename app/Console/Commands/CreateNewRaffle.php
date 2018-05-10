<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Pot;
use Carbon\Carbon;
use Log;

class CreateNewRaffle extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'raffle:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new raffle';

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
        $current = Pot::latest()->first();

        if ($current) {
            $dt = Carbon::parse($current->raffle_date);
        }
        else {
            $dt = Carbon::parse('last Monday 12:00:00 am');
        }

        $raffle = $dt->addDays(7);

        try {
            $new = Pot::create([
                'raffle_date' => $raffle
            ]);
        }
        catch (\Illuminate\Database\QueryException $e) {
            Log::error($e->getMessage());
            dd($e->getMessage());
        }

        Log::info('Next raffle created: ' . $new->raffle_date);
        dd('Next raffle created: ' . $new->raffle_date);

    }
}
