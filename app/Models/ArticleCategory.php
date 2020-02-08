<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleCategory extends Model
{
    protected $guarded = [];

    public function article()
    {
        return $this->hasMany(Article::class, 'category_id', 'id');
    }
}
