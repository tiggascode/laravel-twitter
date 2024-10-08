<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['user_id', 'body', 'group_id', 'preview', 'preview_url'];

    protected $casts = [
        'preview' => 'json'
    ];

    public static function postsForTimeline($userId, $getLatest = true)
    {
        $query = Post::query()
            ->withCount('reactions')
            ->with([
                'user',
                'group',
                'group.currentUserGroup',
                'attachments',
                'comments' => function ($query) {
                    $query->withCount('reactions');
                },
                'comments.user',
                'comments.reactions' => function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                },
                'reactions' => function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                }]);

        if ($getLatest) {
            $query->latest();
        }

        return $query;
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
