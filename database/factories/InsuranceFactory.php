<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Insurance;
use Faker\Generator as Faker;

$factory->define(Insurance::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'discount' => $faker->numberBetween(0, 100),
        'general_code' => $faker->randomNumber(3, true)
    ];
});
