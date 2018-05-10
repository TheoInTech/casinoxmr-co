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
                'email'         =>  'roquevtr+bl'.$i.'@gmail.com',
                'password'      =>  Hash::make(Config::get('salt.key').'asdasd'),
                'first_name'    =>  'Theo '.$i,
                'last_name'     =>  'Roque '.$i,
                'birth_date'    =>  Carbon::parse('May 3, 1995'),
                'country'       =>  'Philippines',
                'reference_key' =>  $this->generateReference()
            ]);
        }
        User::insert($data);
    }
}
