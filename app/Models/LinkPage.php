<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LinkPage extends Model
{
    use HasFactory;

    protected $fillable = [
        // 기본 정보
        'user_id',
        'uuid',
        'password',
        'name',
        'bio',
        'profile_image',

        // Phase 1: 비주얼
        'preset',
        'color',
        'background_type',
        'background_color',
        'background_secondary_color',
        'background_image',
        'background_blur',
        'background_brightness',
        'background_overlay',
        'profile_layout',
        'cover_image',
        'banner_radius',    // ✅ 추가
        'banner_height',    // ✅ 추가
        'badges',
        'animation_entrance',
        'animation_speed',

        // Phase 6: SEO
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_image',
        'custom_domain',
        'domain_verified',
        'qr_logo',
        'qr_color',
        'qr_bg_color',
        'qr_style',
        'is_public',
        'searchable',
        'show_branding',
        'adult_content',

        // 추가
        'text_color',
        'text_size',
        'text_weight',
        'cover_bg_color',
        'background_video_url',
        'background_video_file',
    ];

    protected $casts = [
        'badges' => 'array',
        'background_blur' => 'integer',
        'background_brightness' => 'integer',
        'domain_verified' => 'boolean',
        'is_public' => 'boolean',
        'searchable' => 'boolean',
        'show_branding' => 'boolean',
        'adult_content' => 'boolean',
        'banner_radius' => 'integer',   // ✅ 추가
        'banner_height' => 'integer',   // ✅ 추가
    ];

    protected $hidden = [
        'password',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($linkPage) {
            if (empty($linkPage->uuid)) {
                $linkPage->uuid = (string) Str::uuid();
            }
        });
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function links()
    {
        return $this->hasMany(Link::class)->orderBy('order');
    }

    public function activeLinks()
    {
        return $this->hasMany(Link::class)
            ->where('is_active', true)
            ->whereNull('scheduled_at')
            ->orWhere('scheduled_at', '<=', now())
            ->whereNull('expired_at')
            ->orWhere('expired_at', '>', now())
            ->orderBy('order');
    }

    // Phase 3: Marketing
    public function leadForms()
    {
        return $this->hasMany(LeadForm::class)->where('is_active', true)->orderBy('order');
    }

    public function coupons()
    {
        return $this->hasMany(Coupon::class)->where('is_active', true)->orderBy('order');
    }

    public function countdowns()
    {
        return $this->hasMany(Countdown::class)->where('is_active', true)->orderBy('order');
    }

    // Phase 4: Social
    public function guestbookEntries()
    {
        return $this->hasMany(GuestbookEntry::class)
            ->where('is_approved', true)
            ->orderBy('is_pinned', 'desc')
            ->orderBy('created_at', 'desc');
    }

    public function anonymousQuestions()
    {
        return $this->hasMany(AnonymousQuestion::class)
            ->where('is_public', true)
            ->whereNotNull('answer')
            ->orderBy('answered_at', 'desc');
    }

    public function reactions()
    {
        return $this->hasMany(Reaction::class);
    }

    public function socialFeeds()
    {
        return $this->hasMany(SocialFeed::class)->where('is_active', true)->orderBy('order');
    }

    // Phase 5: Analytics
    public function pageViews()
    {
        return $this->hasMany(PageView::class);
    }

    public function dailyStats()
    {
        return $this->hasMany(DailyStat::class)->orderBy('date', 'desc');
    }

    // Phase 6: SEO
    public function backups()
    {
        return $this->hasMany(PageBackup::class)->orderBy('created_at', 'desc');
    }

    public function slugs()
    {
        return $this->hasMany(PageSlug::class);
    }

    public function activeSlug()
    {
        return $this->hasOne(PageSlug::class)->where('is_active', true);
    }

    // Helper Methods

    /**
     * 공개 URL 가져오기
     */
    public function getPublicUrl()
    {
        if ($this->custom_domain && $this->domain_verified) {
            return 'https://' . $this->custom_domain;
        }

        $activeSlug = $this->activeSlug;
        if ($activeSlug) {
            return url('/s/' . $activeSlug->slug);
        }

        return url('/u/' . $this->uuid);
    }

    /**
     * QR 코드 URL
     */
    public function getQrCodeUrl()
    {
        $url = $this->getPublicUrl();
        $color = str_replace('#', '', $this->qr_color ?? '000000');
        $bgColor = str_replace('#', '', $this->qr_bg_color ?? 'FFFFFF');

        return "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($url)
            . "&color=" . $color
            . "&bgcolor=" . $bgColor;
    }

    /**
     * 비밀번호 확인
     */
    public function checkPassword($password)
    {
        return !$this->password || $this->password === $password;
    }

    /**
     * 수정 권한 확인
     */
    public function canEdit($user = null, $password = null)
    {
        // 회원 페이지인 경우
        if ($this->user_id) {
            return $user && $user->id === $this->user_id;
        }

        // 비회원 페이지인 경우
        return $password && $this->checkPassword($password);
    }

    /**
     * 총 클릭 수
     */
    public function getTotalClicks()
    {
        return $this->links()->sum('clicks');
    }

    /**
     * 총 조회 수
     */
    public function getTotalViews()
    {
        return $this->pageViews()->count();
    }

    /**
     * 오늘 조회 수
     */
    public function getTodayViews()
    {
        return $this->pageViews()
            ->whereDate('viewed_at', today())
            ->count();
    }

    /**
     * 배경 스타일 CSS 생성
     */
    public function getBackgroundStyle()
    {
        $style = '';

        switch ($this->background_type) {
            case 'solid':
                $style = "background-color: {$this->background_color};";
                break;

            case 'gradient':
                $color1 = $this->background_color ?? '#2B7FFF';
                $color2 = $this->background_secondary_color ?? '#9333EA';
                $style = "background: linear-gradient(135deg, {$color1} 0%, {$color2} 100%);";
                break;

            case 'image':
                if ($this->background_image) {
                    $style = "background-image: url('{$this->background_image}');";
                    $style .= "background-size: cover;";
                    $style .= "background-position: center;";
                    $style .= "background-repeat: no-repeat;";

                    if ($this->background_blur > 0) {
                        // 블러는 별도 레이어로 처리
                    }

                    if ($this->background_brightness != 100) {
                        $brightness = $this->background_brightness / 100;
                        $style .= "filter: brightness({$brightness});";
                    }
                }
                break;
        }

        return $style;
    }

    /**
     * 애니메이션 클래스 생성
     */
    public function getAnimationClass()
    {
        $entrance = $this->animation_entrance ?? 'fade';
        $speed = $this->animation_speed ?? 'normal';

        return "animate-{$entrance} animate-{$speed}";
    }

    /**
     * 통계 요약
     */
    public function getStatsSummary($days = 7)
    {
        $stats = $this->dailyStats()
            ->where('date', '>=', now()->subDays($days)->toDateString())
            ->get();

        return [
            'total_views' => $stats->sum('page_views'),
            'unique_visitors' => $stats->sum('unique_visitors'),
            'total_clicks' => $stats->sum('total_clicks'),
            'avg_click_rate' => $stats->avg('click_rate'),
        ];
    }
}
