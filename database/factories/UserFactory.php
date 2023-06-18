<?php

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\User::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret
        'remember_token' => \Illuminate\Support\Str::random(10),
        'is_admin' => 0,
        'job_title' => $faker->jobTitle,
        'summary' => $faker->text,
        'company' => $faker->company,
        'location' => $faker->city . ', ' . $faker->stateAbbr,
        'twitter' => $faker->userName,
        'instagram' => $faker->userName,
        'facebook' => $faker->userName,
        'linkedin' => $faker->userName,
        'website' => $faker->domainName,
        'is_enabled' => 1,
        'timezone' => $faker->timezone,
        'superpower' => $faker->text(200),
        'is_mentor' => $faker->numberBetween(0,1),
    ];
});
