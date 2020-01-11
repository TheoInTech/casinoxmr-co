<?php

namespace App\Common;

use DB;
use Session;
use Auth;
use App\User;
use App\Transaction;
use App\Pot;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

trait Getters {

    protected $exchange;
    protected $last;
    protected $next;

    public function __construct() {
        $client = new Client(['verify' => false]);
        $this->exchange = json_decode($client->request('GET', 'https://api.coinmarketcap.com/v1/ticker/monero')->getBody()->getContents());
        // $this->exchange[0] = json_decode(json_encode(array(
        //     'price_usd' => 231.00
        // )));

        $this->last = Carbon::parse('last Monday 12:00:01 am');
        $this->next = Carbon::parse('next Monday 12:00:00 am');
    }

    public function getExchange() {
        return $this->exchange;
    }

    public function setExchange($exchange) {
        $this->exchange = $exchange;
    }

    public function getLast() {
        return $this->last;
    }

    public function setLast($last) {
        $this->last = $last;
    }

    public function getNext() {
        return $this->next;
    }

    public function setNext($next) {
        $this->next = $next;
    }

    public function getTransactions($take = 10) {
        $user = Auth::user();
        
        $transactions = $user->transactions()
            ->join('categories_ref as cr', 'cr.id', 'transactions.category_id')
            ->select('transactions.*','cr.name as name','cr.description as description')
            ->whereBetween('transactions.created_at', [Carbon::parse('last week'), Carbon::now()])
            ->orderBy('transactions.created_at', 'desc')
            ->get();

        $collected = collect($transactions);
        $collected->transform(function ($item, $key) {
            $item['transacted_at'] = $item->created_at->format('Y-m-d');
            $item->chips = number_format($item->chips, 2, '.', ',');

            return $item;
        });

        return $collected;
    }

    /**
     * Get Total Size of Global Pot
     *
     * @return mixed
     */
    public function getTotalPotSize() {
        $totalPotSize = Transaction::join('categories_ref', 'categories_ref.id', 'transactions.category_id')
                        ->where(function($query){
                            $query->where('categories_ref.name', 'entry');
                            $query->orWhere('categories_ref.name', 'consolation');
                        })
                        ->whereBetween('transactions.created_at', [$this->last, $this->next])
                        ->sum('transactions.chips');

        $totalLoss = Transaction::join('categories_ref', 'categories_ref.id', 'transactions.category_id')
                        ->where('categories_ref.name', 'initiate')
                        ->whereBetween('transactions.created_at', [$this->last, $this->next])
                        ->sum('transactions.chips');

        $totalPotSize = abs($totalPotSize - $totalLoss);

        $usd = ($totalPotSize * 0.000001) * $this->exchange[0]->price_usd;
        $xmr = ($totalPotSize * 0.000001);
        $pot = Pot::whereDate('raffle_date', $this->next)->first();
        $pot->raffle_date = Carbon::parse($pot->raffle_date)->format('Y-m-d\TH:i:s.uP');
        return json_encode([
            'USDEqual'    => $this->exchange[0]->price_usd,
            'exchange'      => array(
                'usd' => round($usd, 2),
                'xmr' => round($xmr, 2)
            ),
            'totalPotSize'  => number_format($totalPotSize, 2, '.', ','),
            'pot'           => $pot
        ]);
    }

    public function getTotalChips() {
        $user = Auth::user();

        $totalChip = $user->transactions()
                        ->join('categories_ref', 'categories_ref.id', 'transactions.category_id')
                        ->where(function($query){
                            $query->where('categories_ref.name', 'entry');
                            $query->orWhere('categories_ref.name', 'consolation');
                        })
                        ->whereBetween('transactions.created_at', [$this->last, $this->next])
                        ->sum('transactions.chips');

        $totalLoss = $user->transactions()
                        ->join('categories_ref', 'categories_ref.id', 'transactions.category_id')
                        ->where('categories_ref.name', 'initiate')
                        ->whereBetween('transactions.created_at', [$this->last, $this->next])
                        ->sum('transactions.chips');


        $totalChip = abs($totalChip - $totalLoss);
        return number_format($totalChip, 2, '.', ',');
    }

    public function getHistory() {
        $ago0 = Carbon::today();
        $ago1 = Carbon::parse('1 day ago');
        $ago2 = Carbon::parse('2 days ago');
        $ago3 = Carbon::parse('3 days ago');
        $ago4 = Carbon::parse('4 days ago');
        $ago5 = Carbon::parse('5 days ago');
        $ago6 = Carbon::parse('6 days ago');

        $user = Auth::user();
        $history = $user->transactions()
                        ->join('categories_ref', 'categories_ref.id', 'transactions.category_id')
                        ->where(function($query){
                            $query->where('categories_ref.name', 'entry');
                            $query->orWhere('categories_ref.name', 'consolation');
                        })
                        ->where('transactions.created_at', '>=', $ago6)
                        ->get();

        $collect = collect($history);

        $days0 = number_format($collect->where('created_at', '>=', $ago0)->sum('chips'), 2, '.', '');
        $days1 = number_format($collect->where('created_at', '>=', $ago1)->where('created_at', '<', $ago0)->sum('chips'), 2, '.', '');
        $days2 = number_format($collect->where('created_at', '>=', $ago2)->where('created_at', '<', $ago1)->sum('chips'), 2, '.', '');
        $days3 = number_format($collect->where('created_at', '>=', $ago3)->where('created_at', '<', $ago2)->sum('chips'), 2, '.', '');
        $days4 = number_format($collect->where('created_at', '>=', $ago4)->where('created_at', '<', $ago3)->sum('chips'), 2, '.', '');
        $days5 = number_format($collect->where('created_at', '>=', $ago5)->where('created_at', '<', $ago4)->sum('chips'), 2, '.', '');
        $days6 = number_format($collect->where('created_at', '>=', $ago6)->where('created_at', '<', $ago5)->sum('chips'), 2, '.', '');

        return array($days6, $days5, $days4, $days3, $days2, $days1, $days0);
    }

    public function getTotalWinnings() {
        $winning = Pot::join('pot_user as pu', 'pots.id', 'pu.pot_id')->where('pots.is_drawn',1)->sum('pu.winning');
        $usd = ($winning * 0.000001) * $this->exchange[0]->price_usd;
        return $usd;
    }

    public function getNextRaffle() {
        $pot = Pot::whereDate('raffle_date', $this->next)->first();
        $pot->raffle_date = Carbon::parse($pot->raffle_date)->format('Y-m-d\TH:i:s.uP');

        return $pot->raffle_date;
    }

    public function getRecentWinners() {
        $today = Carbon::today();
        $latest = Carbon::parse('Monday 12:00:00 am');
        $list = array();
        // if ( $today->isMonday() ) {
        //     $latest = Carbon::parse('Monday 12:00:00 am');
        // }
        // else {
        //     $latest = Carbon::parse('last Monday 12:00:00 am');
        // }

        $pot = Pot::whereDate('raffle_date', $latest)->where('is_drawn', 1)->first();
        
        if ( $pot && $pot->users ) {
            $winners = $pot->users;

            foreach ( $winners as $winner ) {
                $winning = ($winner->pivot->winning * 0.000001) * $this->exchange[0]->price_usd;
                array_push($list, [ 
                    'date' => Carbon::parse($pot->raffle_date)->format('Y-M-d'),
                    'address' => $winner->address,
                    'winning' => $winning
                ]);
            }
        }
        
        return json_encode($list);
    }
}