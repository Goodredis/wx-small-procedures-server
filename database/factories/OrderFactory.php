<?php
/**
 * Created by PhpStorm.
 * User: w17600101602
 * Date: 2019/9/17
 * Time: 11:31
 */
$factory->define(App\Models\Order::class, function (Faker\Generator $faker) {
    return [
        'uid'                   => str_random(32),
        'firstName'             => $faker->firstName,
        'lastName'              => $faker->lastName,
        'email'                 => $faker->email,
        'middleName'            => $faker->lastName,
        'password'              => \Illuminate\Support\Facades\Hash::make('test-password'),
        'address'               => $faker->address,
        'zipCode'               => $faker->postcode,
        'username'              => $faker->userName,
        'city'                  => $faker->city,
        'state'                 => $faker->state,
        'country'               => $faker->country,
        'phone'                 => $faker->phoneNumber,
        'mobile'                => $faker->phoneNumber,
        'role'                  => \App\Models\User::BASIC_ROLE,
        'isActive'              => rand(0,1),
        'profileImage'          => $faker->imageUrl('100')
    ];
});