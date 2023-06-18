<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = DB::table('categories')->select('id')->pluck('id')->toArray();
        $keywords = DB::table('keywords')->select('id')->pluck('id')->toArray();
        $skills = DB::table('skills')->select('id')->pluck('id')->toArray();
        factory(App\Group::class, 6)->create();
        $groups = DB::table('groups')->select('id')->pluck('id')->toArray();

        factory(App\User::class, 50)->create()->each(function ($user) use ($groups, $categories, $keywords, $skills) {
            shuffle($groups);
            $user->groups()->attach(array_slice($groups, 0, 3));

            shuffle($categories);
            $user->categories()->attach(array_slice($categories, 0, 3));

            shuffle($keywords);
            $user->keywords()->attach(array_slice($keywords, 0, 7));

            shuffle($skills);
            $user->skills()->attach(array_slice($skills, 0, 5));
        });
    }
}
