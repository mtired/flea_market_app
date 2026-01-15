<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'user_id',
        'image',
        'status',
        'brand',
        'description',
        'condition_id',
        'price',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function condition()
    {
        return $this->belongsTo(Condition::class, 'condition_id');
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function likedUsers()
    {
        return $this->belongsToMany(User::class, 'likes', 'item_id', 'user_id')
            ->withTimestamps();
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'item_categories');
    }

    public function order()
    {
        return $this->hasOne(Order::class);
    }

    public function getImageUrlAttribute(): string
    {
        $image = $this->image ?? '';

        if (Str::startsWith($image, ['http://', 'https://'])) {
            return $image; // AWSなどのフルURL
        }

        // storage（public disk）想定: item_images/xxx.jpg
        return Storage::url($image); // => /storage/item_images/xxx.jpg
    }
}
