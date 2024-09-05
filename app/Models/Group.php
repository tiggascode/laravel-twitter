<?php

namespace App\Models;

use App\Http\Enums\GroupUserRole;
use App\Http\Enums\GroupUserStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Group extends Model
{
    use HasFactory;
    use HasSlug;
    use SoftDeletes;

    protected $fillable = ['name', 'user_id', 'auto_approval', 'about', 'cover_path', 'thumbnail_path', 'pinned_post_id'];


    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate();
    }

    public function currentUserGroup()
    {
        return $this->hasOne(GroupUser::class)->where('user_id', Auth::id());
    }

    public function isAdmin($userId)
    {
        return GroupUser::query()
            ->where('user_id', $userId)
            ->where('group_id', $this->id)
            ->where('role', GroupUserRole::ADMIN->value)
            ->exists();
    }

    public function hasApprovedUser($userId)
    {
        return GroupUser::query()
            ->where('user_id', $userId)
            ->where('group_id', $this->id)
            ->where('status', GroupUserStatus::APPROVED->value)
            ->exists();
    }


    public function isOwner($userId)
    {
        return GroupUser::query()->where('created_by', $userId)->exists();
    }

    public function adminUsers()
    {
        return $this->belongsToMany(User::class, 'group_users')
            ->wherePivot('role', GroupUserRole::ADMIN->value);
    }

    public function pendingUsers()
    {
        return $this->belongsToMany(User::class, 'group_users')
            ->wherePivot('status', GroupUserStatus::PENDING->value);
    }


    public function approvedUsers()
    {
        return $this->belongsToMany(User::class, 'group_users')
            ->wherePivot('status', GroupUserStatus::APPROVED->value);
    }
}
