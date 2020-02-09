<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = ['user_id', 'article_id', 'parent_id', 'content', 'image', 'is_directory', 'level'];

    protected $casts = [
        'is_directory' => 'boolean',
    ];

    protected $dates = [
        'updated_at'
    ];
    protected $appends = [
        'diff'
    ];

    protected static function boot()
    {
        parent::boot();
        // 监听 Category 的创建事件，用于初始化 path 和 level 字段值
        static::creating(function (Comment $comment) {
            // 如果创建的是一个根类目
            if (is_null($comment->parent_id)) {
                // 将层级设为 0
                $comment->is_directory = false;
                $comment->level = 0;
            } else {
                $comment->is_directory = true;
                // 将层级设为父类目的层级 + 1
                $comment->level = $comment->parent->level + 1;
            }
        });
    }

    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    public function children()
    {
        return $this->hasMany($this, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo($this);
    }

    public function user()
    {
       return $this->belongsTo(User::class);
    }

    public function getDiffAttribute()
    {
        return $this->updated_at->diffForHumans();
    }
}
