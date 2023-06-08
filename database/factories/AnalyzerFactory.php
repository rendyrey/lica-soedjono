<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Analyzer;
use Faker\Generator as Faker;

$factory->define(Analyzer::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
    ];
});
