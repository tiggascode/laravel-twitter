<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['user_id', 'body', 'group_id'];

    public static function postsForTimeline($userId)
    {
        return Post::query()
            ->withCount('reactions')
            ->with([
                'comments' => function ($query) {
                    $query->withCount('reactions');
                },
                'reactions' => function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                }])
            ->latest();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function attachments()
    {
        return $this->hasMany(PostAttachment::class)->latest();
    }

    public function reactions()
    {
        return $this->morphMany(Reaction::class, 'object');
    }

    public function comments()
    {
        return $this->hasMany(Comment ::class)->latest();
    }

    public function latest5Comments()
    {
        return $this->hasMany(Comment ::class)->latest();
    }

    public function isOwner($userId)
    {
        return $this->user_id == $userId;
    }
}
