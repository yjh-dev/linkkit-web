<?php

return [

    // 프리셋별 기본 색상
    'preset_colors' => [
        'basic' => '#2B7FFF',
        'minimal' => '#374151',
        'dark' => '#FBBF24',
    ],

    // 프리셋별 기본 배경
    'preset_backgrounds' => [
        'basic' => [
            'type' => 'gradient',
            'color' => '#EFF6FF',           // 밝은 파랑
            'secondary_color' => '#FFFFFF', // 흰색
            'background_type' => 'gradient',
        ],
        'minimal' => [
            'type' => 'solid',
            'color' => '#F9FAFB',           // 밝은 회색
            'secondary_color' => null,
            'background_type' => 'solid',
        ],
        'dark' => [
            'type' => 'solid',
            'color' => '#111827',           // 검정
            'secondary_color' => null,
            'background_type' => 'solid',
        ],
    ],

    // 추천 색상 팔레트
    'color_palette' => [
        'blue' => [
            'name' => '파랑',
            'value' => '#2B7FFF',
            'emoji' => '💙',
        ],
        'red' => [
            'name' => '빨강',
            'value' => '#EF4444',
            'emoji' => '❤️',
        ],
        'green' => [
            'name' => '초록',
            'value' => '#10B981',
            'emoji' => '💚',
        ],
        'purple' => [
            'name' => '보라',
            'value' => '#8B5CF6',
            'emoji' => '💜',
        ],
        'pink' => [
            'name' => '핑크',
            'value' => '#EC4899',
            'emoji' => '💗',
        ],
        'orange' => [
            'name' => '주황',
            'value' => '#F97316',
            'emoji' => '🧡',
        ],
        'yellow' => [
            'name' => '노랑',
            'value' => '#FBBF24',
            'emoji' => '💛',
        ],
        'gray' => [
            'name' => '회색',
            'value' => '#6B7280',
            'emoji' => '🩶',
        ],
    ],

    // ✨ 추천 배경 프리셋
    'background_presets' => [
        'light_blue' => [
            'name' => '밝은 하늘',
            'type' => 'gradient',
            'color' => '#EFF6FF',
            'secondary_color' => '#FFFFFF',
            'emoji' => '☁️',
        ],
        'sunset' => [
            'name' => '석양',
            'type' => 'gradient',
            'color' => '#FEF3C7',
            'secondary_color' => '#FED7AA',
            'emoji' => '🌅',
        ],
        'ocean' => [
            'name' => '바다',
            'type' => 'gradient',
            'color' => '#DBEAFE',
            'secondary_color' => '#E0E7FF',
            'emoji' => '🌊',
        ],
        'forest' => [
            'name' => '숲',
            'type' => 'gradient',
            'color' => '#DCFCE7',
            'secondary_color' => '#F0FDF4',
            'emoji' => '🌲',
        ],
        'lavender' => [
            'name' => '라벤더',
            'type' => 'gradient',
            'color' => '#F3E8FF',
            'secondary_color' => '#FAE8FF',
            'emoji' => '💜',
        ],
        'white' => [
            'name' => '순백',
            'type' => 'solid',
            'color' => '#FFFFFF',
            'secondary_color' => null,
            'emoji' => '⚪',
        ],
        'light_gray' => [
            'name' => '밝은 회색',
            'type' => 'solid',
            'color' => '#F9FAFB',
            'secondary_color' => null,
            'emoji' => '🤍',
        ],
        'dark' => [
            'name' => '다크',
            'type' => 'solid',
            'color' => '#111827',
            'secondary_color' => null,
            'emoji' => '🖤',
        ],
    ],

];
