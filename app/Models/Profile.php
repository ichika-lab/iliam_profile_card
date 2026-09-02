<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'display_name',
        'oshi_mark',
        'fan_name',
        'bio',
        'avatar_url',
        'theme_color',
        'iriam_url',
        'twitter_id',
        'marshmallow_url',
    ];

    /**
     * このプロフィールを所有するユーザーを取得
     */
    public function user(): BelongsTo
    {
        return $table = $this->belongsTo(User::class);
    }
}