<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LinkPage extends Model
{
    protected $fillable = [
        'uuid',
        'name',
        'bio',
        'profile_image'
    ];

    // UUID 자동 생성
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($linkPage) {
            if (empty($linkPage->uuid)) {
                $linkPage->uuid = (string) Str::uuid();
            }
        });
    }

    // 이 페이지에 속한 링크들 (관계 설정)
    public function links()
    {
        return $this->hasMany(Link::class)->orderBy('order');
    }
}
