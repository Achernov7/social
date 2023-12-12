<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Music extends Model
{
    use HasFactory;

    protected $fillable = [
        'url',
        'path',
        'name',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'music_users', 'music_id', 'user_id');
    }

    public function image()
    {
        return $this->morphOne(Image::class, 'imageable');
    }
}
