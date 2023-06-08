<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Patient;
use Faker\Generator as Faker;

$factory->define(Patient::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'medrec' => $faker->randomElement(['Healthy','Sick','Ill','Comma','Operated']),
        'gender' => $faker->randomElement(['M','F']),
        'birthdate' => $faker->date('Y-m-d'),
        'address' => $faker->address,
        'phone' => $faker->phoneNumber,
        'email' => $faker->safeEmail
    ];
});
