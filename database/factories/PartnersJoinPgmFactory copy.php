<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\User;
use Faker\Generator as Faker;
use Illuminate\Support\Str;
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

