<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class Article extends Model
{
    protected $guarded = [];

    protected $casts = [
        'status' => 'boolean'
    ];

    protected $dates = [
        'created_at', 'updated_at'
    ];

    protected static function boot()
    {
        parent::boot();
        // 监听模型创建事件，在写入数据库之前触发
        static::creating(function ($model) {
            $model->body = clean($model->body, 'user_article_body');
        });

        static::saving(function ($model) {
            $model->body = clean($model->body, 'user_article_body');
        });
    }


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

        $arr['on_sale'] = $this->status;

        $arr['body']  = strip_tags($this->body);

        $arr['tags']  = $this->tags->map(function (ArticleTag $tag) {
            return Arr::only($tag->toArray(), ['name']);
        });

        return $arr;
    }

    public function category()
    {
        return $this->belongsTo(ArticleCategory::class, 'category_id', 'id');
    }

    public function like()
    {
        return $this->morphMany(Like::class, 'like');
    }

    public function getDiffAttribute()
    {
        return $this->created_at->diffForHumans();
    }
}
