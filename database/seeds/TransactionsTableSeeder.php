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
        $client = new Client(['verify' => false]);
        $monero = json_decode($client->request('GET', 'http://moneroblocks.info/api/get_stats')->getBody()->getContents());

        $difficulty = $monero->difficulty;
        $reward = $monero->last_reward / 1000000000000;

        $hashes = array(
            '100000000',
            '200000000',
            '300000000',
            '500000000',
            '6000000',
            '700000000',
            '800000000',
            '900000000',
            '1000000000'
        );

        $day = Carbon::now();
        $format = 'Y-m-d H:i:s';
        $category = CategoriesRef::where('name', 'entry')->first();
        $data = array();
        $users = User::all();
        foreach($users as $user) {
            for($i = 1; $i <= 10; $i++) {
                $hash = $hashes[array_rand($hashes, 1)];
                $xmr = (($hash / $difficulty) * ($reward)) * 0.75;
                $chip = ($xmr / 0.000001);

                $transaction = Transaction::create([
                    'category_id'   =>  $category->id,
                    'chips'       =>  $chip,
                    'hashes'        =>  $hash
                ]);

                $user->transactions()->attach($transaction->id);
            }
        }
    }
}
