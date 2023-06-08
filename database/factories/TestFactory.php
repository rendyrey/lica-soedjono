<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Test;
use Faker\Generator as Faker;

$factory->define(Test::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'initial' => $faker->lexify('??'),
        'unit' => $faker->randomElement(['kg','mm','cm','L','mmHg']),
        'volume' => $faker->randomNumber(5),
        'range_type' => $faker->randomElement(['number','label','description','free_formatted_text']),
        'sequence' => $faker->randomNumber(2),
        'sub_group' => $faker->name,
        'normal_notes' => $faker->sentence(5),
        'general_code' => $faker->randomNumber(3, true)
    ];
});
