<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;

class LinkPage extends Model
{
    protected $fillable = [
        'user_id',
        'uuid',
        'password',
        'preset',
        'color',
        'background_type',              // ✨ 추가
        'background_color',             // ✨ 추가
        'background_secondary_color',   // ✨ 추가
        'name',
        'bio',
        'profile_image'
    ];

    protected $hidden = [
        'password'
    ];
    //모델이 실행될때 자동으로 실행되는 초기화 함수
    // UUID 자동 생성
    protected static function boot()
    {
        parent::boot();

        // "creating" 이벤트 = 데이터 저장하기 직전
        /**
            static::creating()  // 저장 직전
            static::created()   // 저장 직후
            static::updating()  // 수정 직전
            static::updated()   // 수정 직후
            static::deleting()  // 삭제 직전
            static::deleted()   // 삭제 직후
         */
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

    // ✨ 관계: 이 페이지의 소유자
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    // 비밀번호 확인 메서드
    public function checkPassword($password)
    {
        return Hash::check($password, $this->password);
    }

    // ✨ 사용자가 이 페이지의 소유자인지 확인
    public function isOwnedBy($user)
    {
        return $user && $this->user_id === $user->id;
    }
}
