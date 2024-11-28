<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = ['article_id', 'name', 'email', 'body'];

    public function article()
    {
        return $this->belongsTo(Article::class);
    }
}
