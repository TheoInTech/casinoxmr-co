<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            CategoriesRefTableSeeder::class,
            PotTableSeeder::class,
            UsersTableSeeder::class,
            TransactionsTableSeeder::class
        ]);
    }
}
