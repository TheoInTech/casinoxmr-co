<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Transaction;
use App\User;
use App\CategoriesRef;
use App\Pot;
use App\Common\Getters;
use Log;
use Config;

class RaffleDraw extends Command
{
    use Getters;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'raffle:draw';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Draw raffle';

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
        $current = Carbon::parse('Monday 12:00:00 am');
        $last = Carbon::parse('last week Monday 12:00:00 am');

        $users = User::join('transaction_user as tu', 'tu.user_id', 'users.id')
                    ->join('transactions as t', 'tu.transaction_id', 't.id')
                    ->join('categories_ref as cr', 'cr.id', 't.category_id')
                    ->where(function($query){
                        $query->where('cr.name', 'entry');
                        $query->orWhere('cr.name', 'consolation');
                    })
                    ->whereBetween('t.created_at', [$last, $current])
                    ->select('users.id','t.chips')
                    ->get();

        $overall = User::join('transaction_user as tu', 'tu.user_id', 'users.id')
                    ->join('transactions as t', 'tu.transaction_id', 't.id')
                    ->join('categories_ref as cr', 'cr.id', 't.category_id')
                    ->where(function($query){
                        $query->where('cr.name', 'entry');
                        $query->orWhere('cr.name', 'consolation');
                    })
                    ->whereBetween('t.created_at', [$last, $current])
                    ->sum('t.chips');

        // FIX BUG!!! 
        $pot = json_decode($this->getTotalPotSize());
        $potSize = ceil( $pot->exchange->usd );
        $threshold = Config::get('prizes.threshold');
        $winnerAttach = array();

        if ( $potSize >= $threshold ) {
            $chips = array();
            $pool = array();
            $usersGrouped = $users->groupBy('id');

            // Get initiate transaction category
            $catInit = CategoriesRef::where('name', 'initiate')->first();
            $catJWin = CategoriesRef::where('name', 'jwin')->first();
            $catPWin = CategoriesRef::where('name', 'pwin')->first();
            $catCon = CategoriesRef::where('name', 'consolation')->first();

            foreach($usersGrouped as $key => $user) {
                /* Group the chips per user */
                $chips[$key] = $user->sum('chips');
                $totalChips = $chips[$key];
                /* Get the probability of winning */
                $pool[$key] = ($totalChips / $overall) * 100;
                /* Create one transaction */
                $userAttach = $user->unique()[0];

                try {
                    $initiate = $userAttach->transactions()->create([
                        'category_id'   =>  $catInit->id,
                        'chips'       =>  $totalChips,
                        'hashes'        =>  0.0
                    ]);
                }
                catch (\Illuminate\Database\QueryException $e) {
                    Log::error($e->getMessage());
                    dd($e->getMessage());
                }

            }

            /* Sleep one second for solving sorting problems of transactions */
            sleep(1);

            /* Get the actual winners and pass number of winners */
            $winnerIds = $this->getWinners($pool, 3);

            /* Compute winnings */
            $numberOfWinners = count($winnerIds);
            $numberOfLosers = count($usersGrouped)-$numberOfWinners;
            $grandPrize = ($overall * Config::get('prizes.jackpot'.$numberOfWinners));
            $firstPrize = ($overall * Config::get('prizes.first'.$numberOfWinners));
            $secondPrize = ($overall * Config::get('prizes.second'.$numberOfWinners));
            $consolationPrize = $numberOfLosers !== 0 ? ($overall * Config::get('prizes.consolation')) / $numberOfLosers : $numberOfLosers;

            $counter = 0;
            foreach($winnerIds as $winnerId) {
                $user = User::find($winnerId);

                switch ($counter) {
                    case 0:
                        // Jackpot Winner
                        try {
                            $jackpot = $user->transactions()->create([
                                'category_id'   =>  $catJWin->id,
                                'chips'       =>  $grandPrize,
                                'hashes'        =>  0.0
                            ]);
                        }
                        catch (\Illuminate\Database\QueryException $e) {
                            Log::error($e->getMessage());
                            dd($e->getMessage());
                        }
                        $winnerAttach[$winnerId] = ['winning' => $grandPrize];
                        break;
                    case 1:
                        // 1st Prize Winner
                        try {
                            $first = $user->transactions()->create([
                                'category_id'   =>  $catPWin->id,
                                'chips'       =>  $firstPrize,
                                'hashes'        =>  0.0
                            ]);
                        }
                        catch (\Illuminate\Database\QueryException $e) {
                            Log::error($e->getMessage());
                            dd($e->getMessage());
                        }
                        $winnerAttach[$winnerId] = ['winning' => $firstPrize];
                        break;
                    case 2:
                        // 2nd Prize Winner
                        try {
                            $second = $user->transactions()->create([
                                'category_id'   =>  $catPWin->id,
                                'chips'       =>  $secondPrize,
                                'hashes'        =>  0.0
                            ]);

                        }
                        catch (\Illuminate\Database\QueryException $e) {
                            Log::error($e->getMessage());
                            dd($e->getMessage());
                        }
                        $winnerAttach[$winnerId] = ['winning' => $secondPrize];
                        break;
                    default:
                        // do nothing
                        break;
                }

                $counter++;
            }

            $userConsolations = $users->whereNotIn('id', $winnerIds)->groupBy('id');
            $shareSum = $users->whereNotIn('id', $winnerIds)->sum('chips');
            foreach($userConsolations as $key => $userCon) {
                /*
                    $share = chip contribution per user
                    $consolationPrize = 30% of total pot size
                    $shareSum = total chip contribution of all non-winners
                 */

                $userConAttach = $userCon->unique()[0];
                $share = $userCon->sum('chips');
                $tix = ($share * $consolationPrize) / $shareSum;

                try {
                    $consolation = $userConAttach->transactions()->create([
                        'category_id'   =>  $catCon->id,
                        'chips'       =>  $tix,
                        'hashes'        =>  0.0
                    ]);
                }
                catch (\Illuminate\Database\QueryException $e) {
                    Log::error($e->getMessage());
                    dd($e->getMessage());
                }
            }

            Log::info('Winners (ID): ' . implode(', ', $winnerIds));
            print 'Winners (ID): ' . implode(', ',$winnerIds);
        }
        else {
            Log::info('Threshold is not reached. Pot value $'.$potSize);
            print 'Threshold is not reached. Pot value $'.$potSize;
        }

        /* Change status of current pot to drawn */
        $raffle = Pot::where('raffle_date', '=', $current)->where('is_drawn', 0)->first();

        if (!$raffle) {
            try {
                $raffle = Pot::create([
                    'raffle_date' => $current
                ]);
            }
            catch (\Illuminate\Database\QueryException $e) {
                Log::error($e->getMessage());
                dd($e->getMessage());
            }
        }

        $raffle->is_drawn = 1;
        $raffle->save();
        $raffle->users()->attach($winnerAttach);
    }

    /**
    *  Winning number based on probability
    */
    private function getWinners($proArr, $number = 3) {
        $winners = array();
        $number = count($proArr) < $number ? count($proArr) : $number;

        for ($w = 0; $w < $number; $w++) {
            $result = '';
            // Total probability accuracy of probability array
            $proSum = floor(array_sum($proArr));
            // Probability array loop
            foreach ($proArr as $key => $proCur) {
                $randNum = mt_rand(0, $proSum);
                if ($randNum <= $proCur) {
                    $result = $key;
                    break;
                } else {
                    $proSum -= $proCur;
                }
            }
            unset($proArr[$result]);
            array_push($winners, $result);
        }
        return $winners;
    }
}
