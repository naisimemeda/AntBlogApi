<?php

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\User;
use Illuminate\Database\Seeder;

class ArticleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user_ids = User::all()->pluck('id')->toArray();

        $category_ids = ArticleCategory::all()->pluck('id')->toArray();

        $faker = app(Faker\Generator::class);

        $article = factory(Article::class)
            ->times(10)
            ->make()
            ->each(function ($article, $index) use ($user_ids, $category_ids, $faker) {
                $article->user_id = $faker->randomElement($user_ids);
                $article->category_id = $faker->randomElement($category_ids);
            });
        Article::query()->insert($article->toArray());
    }
}
