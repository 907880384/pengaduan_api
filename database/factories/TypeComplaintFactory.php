<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;
use App\Models\Role;

$factory->define(\App\Models\TypeComplaint::class, function (Faker $faker) {
    $roles = Role::where('slug','!=', 'admin')->where('slug','!=', 'pegawai')->pluck('id')->toArray();
    return [
        'title' => $faker->sentence(6,true), 
        'role_id' => $roles[array_rand($roles)],
    ];
});
