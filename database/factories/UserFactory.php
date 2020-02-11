<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Models\User;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(User::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'avatar' => $faker->randomElement([
            'http://q5hmom1p0.bkt.clouddn.com/images/avatars/WX20191202-205134@2x.png',
            'http://q5hmom1p0.bkt.clouddn.com/images/avatars/a2baf0f5c5e8cf8fe1bbc5a632f0d863.jpg',
            'http://q5hmom1p0.bkt.clouddn.com/images/avatars/ABFDB571-9672-40AB-AD08-8E2205FE5E52.jpeg',
            'http://q5hmom1p0.bkt.clouddn.com/images/avatars/F990CA97-0859-4421-8095-ADD08C47EA28.jpeg',
            'http://q5hmom1p0.bkt.clouddn.com/images/avatars/D464DDF4-5A50-429F-963B-545F45835F2C_4_5005_c.jpeg',
            'http://q5hmom1p0.bkt.clouddn.com/images/avatars/7E673CEC-C7F9-4462-A36D-EC927D000328.jpeg',
            'http://q5hmom1p0.bkt.clouddn.com/images/avatars/7B46D5E3-145A-4F42-A608-7CDB248A964C.jpeg',
            'http://q5hmom1p0.bkt.clouddn.com/images/avatars/C0A754F9-0A48-4A9C-B585-532632B7540C_1_105_c.jpeg',
            'http://q5hmom1p0.bkt.clouddn.com/images/avatars/D6BF620B-F58C-45B2-8373-7FC58165EEA1_1_105_c.jpeg',
            'http://q5hmom1p0.bkt.clouddn.com/images/avatars/5335A57A-46B9-4CD5-9384-ABECAA70EE64_1_105_c.jpeg',
            'http://q5hmom1p0.bkt.clouddn.com/images/avatars/5E916AC1-A7E7-41D3-9141-410C70764F4C_1_105_c.jpeg'
        ]),
        'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
    ];
});
