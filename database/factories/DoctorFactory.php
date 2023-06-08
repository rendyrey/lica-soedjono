<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Doctor;
use Faker\Generator as Faker;

$factory->define(Doctor::class, function (Faker $faker) {
    return [
        'name' => $faker->lexify('Dr? ' . $faker->name),
        'general_code' => $faker->randomNumber(3, true)
    ];
});
