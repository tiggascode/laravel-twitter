<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    public int $numOfComments = 0;
    public array $childComments = [];

    protected $fillable = ['user_id', 'post_id', 'comment', 'parent_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function reactions()
    {
        return $this->morphMany(Reaction::class, 'object');
    }

    public function comments()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function isOwner($userId)
    {
        return $this->user_id == $userId;
    }
}
