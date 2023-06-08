<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Room;
use Faker\Generator as Faker;

$factory->define(Room::class, function (Faker $faker) {
    return [
        'room' => $faker->name,
        'room_code' => $faker->randomNumber(2, true),
        'class' => $faker->randomElement(['1','2','3']),
        'auto_checkin' => $faker->randomElement([true, false]),
        'auto_draw' => $faker->randomElement([true, false]),
        'type' => $faker->randomElement(['rawat_inap', 'rawat_jalan','igd','rujukan']),
        'referral_address' => $faker->address,
        'referral_email' => $faker->safeEmail,
        'referral_no_phone' => $faker->phoneNumber,
        'general_code' => $faker->randomNumber(5, true)
    ];
});
