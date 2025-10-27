<?php

return [
    // 기존 색상 프리셋
    'colors' => [
        'blue' => '#2B7FFF',
        'purple' => '#9333EA',
        'pink' => '#EC4899',
        'green' => '#10B981',
        'orange' => '#F97316',
        'red' => '#EF4444',
        'yellow' => '#F59E0B',
        'gray' => '#6B7280',
    ],

    // 배경 타입
    'background_types' => [
        'solid' => '단색',
        'gradient' => '그라데이션',
        'image' => '이미지',
        'video' => '영상',
        'animated' => '애니메이션',
    ],

    // 배경 패턴 오버레이
    'background_overlays' => [
        'none' => '없음',
        'dots' => '도트',
        'stripes' => '스트라이프',
        'grid' => '그리드',
        'waves' => '웨이브',
    ],

    // 프로필 레이아웃
    'profile_layouts' => [
        'large' => [
            'name' => '큰 이미지 (인스타 스타일)',
            'description' => '프로필 이미지를 크게, 중앙 정렬',
            'preview' => '/images/layouts/large.png'
        ],
        'small' => [
            'name' => '작은 이미지 (명함 스타일)',
            'description' => '프로필 이미지를 작게, 가로 배치',
            'preview' => '/images/layouts/small.png'
        ],
        'banner' => [
            'name' => '배너 + 프로필 (트위터 스타일)',
            'description' => '상단 배너 위에 프로필 이미지 겹치기',
            'preview' => '/images/layouts/banner.png'
        ],
    ],

    // 뱃지 옵션
    'badges' => [
        'official' => [
            'label' => 'Official',
            'icon' => '✓',
            'color' => '#2B7FFF',
            'bg' => '#DBEAFE'
        ],
        'verified' => [
            'label' => 'Verified',
            'icon' => '✓',
            'color' => '#10B981',
            'bg' => '#D1FAE5'
        ],
        'new' => [
            'label' => 'New',
            'icon' => '🌟',
            'color' => '#F59E0B',
            'bg' => '#FEF3C7'
        ],
        'pro' => [
            'label' => 'PRO',
            'icon' => '⭐',
            'color' => '#9333EA',
            'bg' => '#F3E8FF'
        ],
    ],

    // 등장 애니메이션
    'animations' => [
        'entrance' => [
            'none' => '없음',
            'fade' => '페이드인',
            'slide' => '슬라이드',
            'bounce' => '바운스',
            'zoom' => '확대',
        ],
        'speed' => [
            'slow' => '느림 (0.8초)',
            'normal' => '보통 (0.5초)',
            'fast' => '빠름 (0.3초)',
        ],
    ],

    // 버튼 호버 효과
    'button_hover_effects' => [
        'none' => '없음',
        'scale' => '확대',
        'lift' => '올라오기',
        'glow' => '빛나기',
        'wiggle' => '흔들림',
        'pulse' => '맥박',
    ],

    // 버튼 스타일
    'button_styles' => [
        'rounded' => [
            'name' => '둥근 사각형',
            'class' => 'rounded-xl',
            'preview' => '/images/buttons/rounded.png'
        ],
        'pill' => [
            'name' => '알약형',
            'class' => 'rounded-full',
            'preview' => '/images/buttons/pill.png'
        ],
        'sharp' => [
            'name' => '날카로운 모서리',
            'class' => 'rounded-none',
            'preview' => '/images/buttons/sharp.png'
        ],
        'soft' => [
            'name' => '부드러운 모서리',
            'class' => 'rounded-lg',
            'preview' => '/images/buttons/soft.png'
        ],
    ],

    // 버튼 크기
    'button_sizes' => [
        'small' => [
            'name' => '작게',
            'class' => 'py-2 px-4 text-sm'
        ],
        'medium' => [
            'name' => '보통',
            'class' => 'py-3 px-6 text-base'
        ],
        'large' => [
            'name' => '크게',
            'class' => 'py-4 px-8 text-lg'
        ],
    ],
];
