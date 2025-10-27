<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    use HasFactory;

    protected $fillable = [
        'link_page_id',
        'title',
        'url',
        'order',
        'clicks',
        'unique_clicks',

        // Phase 2: 링크 타입
        'type',
        'thumbnail',
        'description',

        // 상품 관련
        'price',
        'sale_price',
        'currency',
        'stock_status',

        // 임베드
        'embed_type',
        'embed_id',

        // 아이콘
        'icon',

        // 파일
        'file_path',
        'file_size',

        // 특수 기능
        'password_protected',
        'password',
        'scheduled_at',
        'expired_at',

        // 스타일
        'button_style',
        'button_size',
        'button_color',
        'hover_effect',

        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'file_size' => 'integer',
        'password_protected' => 'boolean',
        'scheduled_at' => 'datetime',
        'expired_at' => 'datetime',
        'is_active' => 'boolean',
        'clicks' => 'integer',
        'unique_clicks' => 'integer',
    ];

    protected $hidden = [
        'password',
    ];

    // Relationships
    public function linkPage()
    {
        return $this->belongsTo(LinkPage::class);
    }

    public function clickLogs()
    {
        return $this->hasMany(LinkClickLog::class);
    }

    // Helper Methods

    /**
     * 링크 타입별 아이콘
     */
    public function getTypeIcon()
    {
        $icons = [
            'button' => '🔗',
            'product' => '🛍️',
            'image_card' => '🖼️',
            'embed' => '📺',
            'icon' => '⭐',
            'text' => '📝',
            'contact' => '📞',
            'file' => '📁',
        ];

        return $icons[$this->type] ?? '🔗';
    }

    /**
     * 활성 상태 확인
     */
    public function isAvailable()
    {
        if (!$this->is_active) {
            return false;
        }

        // 예약 발행 체크
        if ($this->scheduled_at && $this->scheduled_at > now()) {
            return false;
        }

        // 만료 체크
        if ($this->expired_at && $this->expired_at < now()) {
            return false;
        }

        return true;
    }

    /**
     * 비밀번호 확인
     */
    public function checkPassword($password)
    {
        if (!$this->password_protected) {
            return true;
        }

        return $this->password === $password;
    }

    /**
     * 클릭 수 증가
     */
    public function incrementClicks($isUnique = false)
    {
        $this->increment('clicks');

        if ($isUnique) {
            $this->increment('unique_clicks');
        }
    }

    /**
     * 할인율 계산
     */
    public function getDiscountPercent()
    {
        if (!$this->price || !$this->sale_price) {
            return 0;
        }

        return round((($this->price - $this->sale_price) / $this->price) * 100);
    }

    /**
     * 가격 포맷팅
     */
    public function getFormattedPrice()
    {
        if (!$this->price) {
            return null;
        }

        $currency = $this->currency ?? 'KRW';

        switch ($currency) {
            case 'KRW':
                return number_format($this->price) . '원';
            case 'USD':
                return '$' . number_format($this->price, 2);
            case 'EUR':
                return '€' . number_format($this->price, 2);
            default:
                return $currency . ' ' . number_format($this->price, 2);
        }
    }

    public function getFormattedSalePrice()
    {
        if (!$this->sale_price) {
            return null;
        }

        $currency = $this->currency ?? 'KRW';

        switch ($currency) {
            case 'KRW':
                return number_format($this->sale_price) . '원';
            case 'USD':
                return '$' . number_format($this->sale_price, 2);
            case 'EUR':
                return '€' . number_format($this->sale_price, 2);
            default:
                return $currency . ' ' . number_format($this->sale_price, 2);
        }
    }

    /**
     * 파일 크기 포맷팅
     */
    public function getFormattedFileSize()
    {
        if (!$this->file_size) {
            return null;
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $size = $this->file_size;
        $unit = 0;

        while ($size >= 1024 && $unit < count($units) - 1) {
            $size /= 1024;
            $unit++;
        }

        return round($size, 2) . ' ' . $units[$unit];
    }

    /**
     * 임베드 URL 생성
     */
    public function getEmbedUrl()
    {
        if (!$this->embed_type || !$this->embed_id) {
            return null;
        }

        switch ($this->embed_type) {
            case 'youtube':
                return "https://www.youtube.com/embed/{$this->embed_id}";
            case 'spotify':
                return "https://open.spotify.com/embed/{$this->embed_id}";
            case 'instagram':
                return "https://www.instagram.com/p/{$this->embed_id}/embed";
            default:
                return null;
        }
    }

    /**
     * 버튼 스타일 클래스 생성
     */
    public function getButtonClass()
    {
        $classes = [];

        // 기본 스타일
        $classes[] = 'link-button';

        // 모양
        $style = $this->button_style ?? 'rounded';
        switch ($style) {
            case 'rounded':
                $classes[] = 'rounded-xl';
                break;
            case 'pill':
                $classes[] = 'rounded-full';
                break;
            case 'sharp':
                $classes[] = 'rounded-none';
                break;
            case 'soft':
                $classes[] = 'rounded-lg';
                break;
        }

        // 크기
        $size = $this->button_size ?? 'medium';
        switch ($size) {
            case 'small':
                $classes[] = 'py-2 px-4 text-sm';
                break;
            case 'medium':
                $classes[] = 'py-3 px-6 text-base';
                break;
            case 'large':
                $classes[] = 'py-4 px-8 text-lg';
                break;
        }

        // 호버 효과
        $hover = $this->hover_effect ?? 'none';
        if ($hover !== 'none') {
            $classes[] = "hover-{$hover}";
        }

        return implode(' ', $classes);
    }

    /**
     * 버튼 스타일 (인라인)
     */
    public function getButtonStyle()
    {
        if ($this->button_color) {
            return "background-color: {$this->button_color};";
        }

        return '';
    }

    /**
     * 재고 상태 텍스트
     */
    public function getStockStatusText()
    {
        $statuses = [
            'in_stock' => '판매중',
            'out_of_stock' => '품절',
            'pre_order' => '예약판매',
        ];

        return $statuses[$this->stock_status] ?? '판매중';
    }

    /**
     * 재고 상태 색상
     */
    public function getStockStatusColor()
    {
        $colors = [
            'in_stock' => 'green',
            'out_of_stock' => 'red',
            'pre_order' => 'blue',
        ];

        return $colors[$this->stock_status] ?? 'gray';
    }
}
