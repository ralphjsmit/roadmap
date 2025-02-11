<?php

namespace App\Models;

use App\Traits\Sluggable;
use App\Traits\HasOgImage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory, Sluggable, HasOgImage;

    public $fillable = [
        'title',
        'slug',
        'url',
        'description',
        'private',
    ];

    protected $casts = [
        'private' => 'boolean',
    ];

    public function boards()
    {
        return $this->hasMany(Board::class)->orderBy('sort_order');
    }

    public function items()
    {
        return $this->hasManyThrough(Item::class, Board::class);
    }

    public function scopeVisibleForCurrentUser($query)
    {
        if (auth()->user()?->hasAdminAccess()) {
            return $query;
        }

        return $query->where('private', false);
    }
}
