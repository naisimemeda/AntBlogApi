<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    protected $table = 'user_like';

    protected $fillable = ['user_id'];

    public function like()
    {
        return $this->morphTo();
    }
}
