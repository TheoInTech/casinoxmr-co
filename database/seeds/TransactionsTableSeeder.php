<?php

use Illuminate\Database\Seeder;
use App\Transaction;
use App\CategoriesRef;
use App\User;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class TransactionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $client = new Client();
        $monero = json_decode($client->request('GET', 'https://chainradar.com/api/v1/mro/status')->getBody()->getContents());

        $difficulty = $monero->difficulty;
        $reward = $monero->baseReward / 1000000000000;

        $hashes = array(
            '10000000000',
            '20000000000',
            '30000000000',
            '50000000000',
            '600000000',
            '70000000000',
            '80000000000',
            '90000000000',
            '100000000000'
        );

        $day = Carbon::now();
        $format = 'Y-m-d H:i:s';
        $category = CategoriesRef::where('name', 'entry')->first();
        $data = array();
        for($i = 1; $i <= 10; $i++) {
            $hash = $hashes[array_rand($hashes, 1)];
            $xmr = (($hash / $difficulty) * ($reward)) * 0.75;
            $chip = ($xmr / 0.000001);

            $transaction = Transaction::create([
                'category_id'   =>  $category->id,
                'tickets'       =>  $chip,
                'hashes'        =>  $hash
            ]);

            $user = User::find($i);
            $user->transactions()->attach($transaction->id);
        }
    }
}
