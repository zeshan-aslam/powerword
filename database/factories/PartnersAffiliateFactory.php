<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\User;
use Faker\Generator as Faker;
use Illuminate\Support\Str;
use App\Models\PartnersAffiliate;



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

