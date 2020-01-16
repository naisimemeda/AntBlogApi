<?php

use App\Models\Article;
use App\Models\ArticleTag;
use Illuminate\Database\Seeder;

class ArticleTagTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $article_ids = Article::all()->pluck('id')->toArray();

        $faker = app(Faker\Generator::class);

        $tag = factory(ArticleTag::class)
            ->times(25)
            ->make()
            ->each(function ($article, $index) use ($article_ids, $faker) {
                $article->article_id = $faker->randomElement($article_ids);
            });

        ArticleTag::query()->insert($tag->toArray());
    }
}
