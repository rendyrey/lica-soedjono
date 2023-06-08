<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Package;
use Faker\Generator as Faker;

$factory->define(Package::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'general_code' => $faker->randomNumber(3, true)
    ];
});
