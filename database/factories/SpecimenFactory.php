<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Specimen;
use Faker\Generator as Faker;

$factory->define(Specimen::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'color' => $faker->colorName,
        'code' => $faker->randomNumber(2, true)
    ];
});
