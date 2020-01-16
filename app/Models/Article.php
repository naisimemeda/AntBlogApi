<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class Article extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tags()
    {
        return $this->hasMany(ArticleTag::class);
    }

    public function toEsArray()
    {
        $arr = Arr::only($this->toArray(), [
            'id',
            'title',
            'body',
            'category_id',
            'user_id',
            'reply_count',
            'view_count',
            'hot',
        ]);

        $arr['body']  = strip_tags($this->body);

        $arr['tags']  = $this->tags->map(function (ArticleTag $tag) {
            return Arr::only($tag->toArray(), ['name']);
        });

        return $arr;
    }
}
