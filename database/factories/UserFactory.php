<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\User;
use Faker\Generator as Faker;
use Illuminate\Support\Str;
use App\Models\PartnersAffiliate;
use App\Models\PartnersJoinPgm;

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

// $factory->define(User::class, function (Faker $faker) {
//     return [
//         'name' => $faker->name,
//         'email' => $faker->unique()->safeEmail,
//         'email_verified_at' => now(),
//         'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
//         'remember_token' => Str::random(10),
//     ];
// });


$factory->define(PartnersJoinPgm::class, function (Faker $faker) {
    return [
        'joinpgm_programid' => rand(1,20),
        'joinpgm_merchantid' =>  rand(303,320),
        'joinpgm_affiliateid' => 2,
        'joinpgm_date' => now()->format('Y-m-d'),
        'joinpgm_status' => 'approved',
        'joinpgm_group' => 0,
        'joinpgm_commissionid' => 0,
        'joinpgm_lead_count' => 0,
        'joinpgm_sale_count' => 0
    ];
});


$factory->define(PartnersAffiliate::class, function (Faker $faker) {
    return [
        'affiliate_firstname' => $this->faker->name(),
        'affiliate_lastname' => $this->faker->name(),
        'affilate_profileimage' => '',
        'affiliate_company' => $this->faker->title(),
        'affiliate_address' => $this->faker->address(),
        'affiliate_city' => $this->faker->city(),
        'affiliate_country' => $this->faker->country(),
        'affiliate_url' => $this->faker->url(),
        'affiliate_category' => 'Charities',
        'affiliate_date' => now()->format('Y-m-d'),
        'affiliate_status' => 'approved',
        'affiliate_fax' => $this->faker->phoneNumber(),
        'affiliate_phone' => $this->faker->phoneNumber(),
        'affiliate_state' => $this->faker->city(),
        'affiliate_timezone' => $this->faker->name(),
        'affiliate_zipcode' => $this->faker->numberBetween(1,1500),
        'affiliate_taxId' => $this->faker->numberBetween(1,5000),
        'affiliate_currency' => $this->faker->currencyCode(),
        'affiliate_group' => $this->faker->boolean(),
        'affiliate_secretkey' => Str::random(10), // 1a2v3a4z5c6h7a8t9b0ots2c5ri00pt,
        'affiliate_parentid' =>  0,
    ];
});

