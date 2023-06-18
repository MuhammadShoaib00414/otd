<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')
        ->where('email', 'shayannshahid@gmail.com')
        ->update(['is_super_admin' => 1]);
    }
}
