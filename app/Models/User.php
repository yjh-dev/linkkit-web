<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'provider',        // 추가
        'provider_id',     // 추가
        'profile_image',   // 추가
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // ✨ 관계: 사용자의 링크 페이지들
    public function linkPages()
    {
        return $this->hasMany(LinkPage::class);
    }

    // ✨ 소셜 로그인으로 사용자 찾기 또는 생성
    public static function findOrCreateSocialUser($provider, $socialUser)
    {
        // 이미 등록된 사용자 찾기
        $user = self::where('provider', $provider)
            ->where('provider_id', $socialUser->getId())
            ->first();

        if ($user) {
            return $user;
        }


        // 신규 사용자 생성
        return self::create([
            'provider' => $provider,
            'provider_id' => $socialUser->getId(),
            'name' => $socialUser->getName() ?? $socialUser->getNickname(),
            'email' => $socialUser->getEmail(),
            'profile_image' => $socialUser->getAvatar(),
        ]);
    }
}
