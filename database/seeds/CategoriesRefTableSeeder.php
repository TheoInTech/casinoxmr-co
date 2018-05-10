<?php

use Illuminate\Database\Seeder;
use App\CategoriesRef;

class CategoriesRefTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CategoriesRef::create([
            'name'          =>  'entry',
            'description'   =>  'Chip shares added to global pot',
            'is_gain'       =>  1,
            'symbol'        =>  'fa fa-plus-square'
        ]);

        CategoriesRef::create([
            'name'          =>  'jwin',
            'description'   =>  'Jackpot won',
            'is_gain'       =>  1,
            'symbol'        =>  'fa fa-trophy'
        ]);

        CategoriesRef::create([
            'name'          =>  'pwin',
            'description'   =>  'Prize won',
            'is_gain'       =>  1,
            'symbol'        =>  'fa fa-trophy'
        ]);

        CategoriesRef::create([
            'name'          =>  'consolation',
            'description'   =>  'Consolation chips received',
            'is_gain'       =>  1,
            'symbol'        =>  'fa fa-plus-square'
        ]);

        CategoriesRef::create([
            'name'          =>  'initiate',
            'description'   =>  'Raffle draw initiated',
            'is_gain'       =>  0,
            'symbol'        =>  'fas fa-hourglass'
        ]);

        CategoriesRef::create([
            'name'          =>  'cashout',
            'description'   =>  'Monero cashout',
            'is_gain'       =>  0,
            'symbol'        =>  'fa fa-money-bill-alt'
        ]);
    }
}
