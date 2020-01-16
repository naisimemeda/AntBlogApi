<?php

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = app(Faker\Generator::class);

        // 生成数据集合
        $users = factory(User::class)
            ->times(10)
            ->make();

        $user_array = $users->makeVisible(['password'])->toArray();

        // 插入到数据库中
        User::insert($user_array);
    }
}
