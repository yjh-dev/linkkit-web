<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    protected $fillable = [
        'link_page_id',
        'title',
        'url',
        'order',
        'clicks'
    ];

    // 이 링크가 속한 페이지 (관계 설정)
    public function linkPage()
    {
        return $this->belongsTo(LinkPage::class);
    }

    // 클릭수 증가 메서드
    public function incrementClicks()
    {
        $this->increment('clicks');
    }
}
