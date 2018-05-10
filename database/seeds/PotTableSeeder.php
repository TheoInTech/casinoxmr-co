<?php

use Illuminate\Database\Seeder;
use App\Pot;
use Carbon\Carbon;

class PotTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Pot::create([
            'raffle_date' => Carbon::parse('today 6:00 am')
        ]);
    }
}
