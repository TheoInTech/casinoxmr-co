<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use App\Transaction;
use App\User;
use App\CategoriesRef;
use App\Common\Generators;
use App\Common\Getters;
use App\Rules\ValidAddress;
use App\Rules\ValidPaymentId;
use Carbon\Carbon;
use Aws\S3\AmazonS3;
use Auth;
use Config;
use Session;
use Validator;
use Hash;
use Countries;
use Filesystem;
use Storage;

class DashboardController extends Controller
{
    use Generators, Getters;

    public function dashboard() {
        return view('layouts.dashboard')->with([
            'nextRaffleDate'=> $this->getNextRaffle(),
            'currentDate'   => Carbon::now()->format('Y-m-d\TH:i:s.uP'),
            'winners' => json_decode($this->getRecentWinners())
        ]);
    }

    public function submitAddress(Request $request) {

        $validator = Validator::make($request->all(), [
            'address'   => ['required','max:95','min:95', new ValidAddress],
            'ref'       => 'sometimes|nullable|exists:addresses,reference_key',
            'payment_id'=> ['required', new ValidPaymentId]
        ],
        [ 'ref.exists' => 'The reference key is not valid.' ]);

        if ( $validator->fails() ) {
            return redirect()->back()->with('error_code', 'login')->withErrors($validator)->withInput();
        }

        $hashed = Hash::make(Config::get('salt.key').$request['payment_id']);
        $address = User::where('address', $request['address'])->get();

        if ( $address ) {
            Session::put('user', $address);
        }
        else {
            try {
                $address = User::create([
                    'address'       => $request['address'],
                    'reference_key' => $this->generateReference(),
                    'referred_by'   => $request['ref'] ? $request['ref'] : null,
                    'payment_id'    => $hashed
                ]);
            }
            catch (\Illuminate\Database\QueryException $e) {
                // do nothing as of the moment
            }
            finally {
                Session::put('address', $address);
            }
        }

        return redirect('/dashboard');
    }

    public function addTransaction(Request $request) {
        $host = $request['host'];
        $key = $request['key'];

        if (
            ($host == 'casinoxmr.co' && $key != '2d2db8cecb5ec54263757ea2d14eccc893153fa37a6ba0fd985c723de85cb031') ||
            ($host == '127.0.0.1' && $key != 'fc7326cf1ab30aa6ea144bb3036c07d5dd57bc275b72fac89f5fdf68746be0d5') ||
            ($host == 'localhost' && $key != 'fc7326cf1ab30aa6ea144bb3036c07d5dd57bc275b72fac89f5fdf68746be0d5')
        ) {
            return response()->json(['message' => 'Invalid key!'], 400);
        }

        // Get monero status and data
        $client = new Client();
        $monero = json_decode($client->request('GET', 'https://chainradar.com/api/v1/mro/status')->getBody()->getContents());
        $difficulty = $monero->difficulty;
        $reward = $monero->baseReward / 1000000000000;

        $user = Auth::user();

        // 0.85 for a percentage error of 15% for only valid hashes
        // No way of getting total of valid hashes YET
        $hashes = $request['hashes'] * 0.85;

        // 0.25 coz we're getting 25%
        $xmr = (($hashes / $difficulty) * ($reward)) * 0.25;
        $chip = ($xmr / 0.000001);
        $category = CategoriesRef::where('name', 'entry')->first();

        $transaction = Transaction::create([
            'category_id'   =>  $category->id,
            'chips'         =>  $chip,
            'hashes'        =>  $hashes
        ]);

        // Attach transaction into address
        $user->transactions()->attach($transaction->id);

        return response()->json(['message' => 'success'], 200);
    }

    public function uploadPicture(Request $request) {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ( $validator->fails() ) {
            return response()->json(['message' => 'Must upload a valid image in .jpeg, .png and .jpg format only!'], 400);
        }

        $imageName = 'uploads/profile/'.Auth::user()->id.'.'.$request->image->getClientOriginalExtension();

        $image = $request->file('image');
        $t = Storage::disk('s3')->put($imageName, file_get_contents($image), 'public');
        $imageName = Storage::disk('s3')->url($imageName);

        Auth::user()->picture_url = $imageName;
        Auth::user()->save();

        return response()->json(['url' => $imageName], 200);
    }

    public function edit(Request $request) {
        $validator = Validator::make($request->all(), [
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'birth_date'    => 'required|string',
            'country'       => 'required|string',
            'contact_number'=> 'required|regex:/^([0-9\s\-\+\(\)]*)$/|between:9,14'
        ],
        [
            'contact_number.between'    => 'Invalid contact number format.',
            'contact_number.regex'      => 'Invalid contact number format.'
        ]);

        if ( $validator->fails() ) {
            return redirect()->back()->with('error_code', 'edit')->withErrors($validator)->withInput();
        }

        try {
            $user = Auth::user();
            $user->first_name = $request['first_name'];
            $user->last_name = $request['last_name'];
            $user->birth_date = Carbon::parse($request['birth_date']);
            $user->country = $request['country'];
            $user->contact_number = $request['contact_number'];
            $user->save();
        }
        catch (\Illuminate\Database\QueryException $e) {
            Log::error($e->getMessage());
            abort(500);
        }

        return redirect()->intended('dashboard')->with('success_code', 'edit');
    }

    public function getCoinimpScript() {
        echo file_get_contents(base_path().DIRECTORY_SEPARATOR.'../tmp/coinimp-cache/dM6m.php');
    }

    public function transactions(Request $request) {
        return response()->json(['transactions' => $this->getTransactions()], 200);
    }

    public function potSize() {
        $data = [
            'potSize'   => $this->getTotalPotSize(),
            'threshold' => Config::get('prizes.threshold')
        ];
        return response()->json($data, 200);
    }

    public function chips() {
        return response()->json(['totalChips' => $this->getTotalChips()], 200);
    }

    public function history() {
        return response()->json(['history' => $this->getHistory()], 200);
    }

    public function logout() {
        Auth::logout();
        Session::flush();

        return redirect('');
    }
}
