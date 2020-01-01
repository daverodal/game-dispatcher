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

        \App\User::create(['email' => 'dave.rodal@gmail.com', 'name' => 'TheCreator', 'password' => bcrypt('2makegames'), 'is_admin' => 1, 'is_editor' => 1]);
    }

}