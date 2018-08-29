<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         $this->call(UserTableSeeder::class);
    }
}

class UserTableSeeder extends Seeder {

    public function run()
    {
//        DB::table('users')->delete();

        \App\User::create(['email' => 'you.name@example.com', 'name' => 'PlayerOne', 'password' => bcrypt('password'), 'is_admin' => 1, 'is_editor' => 1]);
    }

}