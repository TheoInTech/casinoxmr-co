<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Common\Generators;
use Carbon\Carbon;

class UsersTableSeeder extends Seeder
{
    use Generators;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = array();
        for($i = 1; $i <= 10; $i++) {
            array_push($data,[
                'address'       => $this->generateRandomString(95),
                'reference_key' => $this->generateReference()
            ]);
        }
        User::insert($data);
    }

    protected function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
