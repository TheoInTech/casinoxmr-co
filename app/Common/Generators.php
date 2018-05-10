<?php

namespace App\Common;

use App\User;

trait Generators {

    protected function generateReference($length = 10) {
        $exist = true;

        while($exist) {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            $randomString = '';
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }

            $reference = User::where('reference_key', $randomString)->first();
            if (!$reference) {
                $exist = false;
            }
        }

        return $randomString;
    }
}