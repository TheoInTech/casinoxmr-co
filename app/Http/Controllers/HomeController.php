<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use App\Common\Getters;
use App\Common\Generators;
use App\Rules\ValidAddress;
use App\User;
use Carbon\Carbon;
use Hash;
use Config;
use Countries;
use Auth;
use Log;
use Validator;

class HomeController extends Controller
{
    use Generators, Getters;

    public function index() {
        if(Auth::check()){
            return redirect('/dashboard');
        }

        $pot = json_decode($this->getTotalPotSize());

        return view('pages.index')->with([
            'potSize'       => $pot->exchange->usd,
            'nextRaffleDate'=> $this->getNextRaffle(),
            'currentDate'   => Carbon::now()->format('Y-m-d\TH:i:s.uP')
        ]);
    }

    public function signup(Request $request) {
        $validator = Validator::make($request->all(), [
            'address'       => ['required', new ValidAddress]
        ]);

        if ( $validator->fails() ) {
            return redirect()->back()->with('error_code', 'signup')->withErrors($validator)->withInput();
        }

        $address = $request['address'];
        $ref = $request['ref'];
        $user = User::where('address', $address)->first();

        if ( !$user ) {
            try {
                $user = User::create([
                    'address'       =>  $request['address'],
                    'reference_key' =>  $this->generateReference(),
                    'referred_by'   =>  !empty($ref) ? $ref : null
                ]);
            }
            catch (\Illuminate\Database\QueryException $e) {
                Log::error($e->getMessage());
                abort(500);
            }
        }

        Auth::login($user);

        return redirect()->intended('dashboard');
    }

    public function login(Request $request) {
        $validator = Validator::make($request->all(), [
            'email-login'   => 'required|email|max:255',
            'password'      => 'required|string|min:6|max:255'
        ]);

        if ( $validator->fails() ) {
            return redirect()->back()->with('error_code', 'login')->withErrors($validator)->withInput();
        }

        if (Auth::attempt(['email' => $request['email-login'], 'password' => Config::get('salt.key').$request['password']])) {
            return redirect('/dashboard');
        }
        else {
            return redirect()->back()->with('error_code', 'login')->withErrors($validator)->withInput();
        }
    }
}
