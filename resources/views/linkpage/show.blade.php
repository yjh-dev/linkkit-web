<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $linkPage->name }} - LinkKit</title>

    <!-- SEO Meta Tags (Phase 6) -->
    <meta name="description" content="{{ $linkPage->meta_description ?? $linkPage->bio }}">
    <meta name="keywords" content="{{ $linkPage->meta_keywords ?? '' }}">

    <!-- Open Graph Tags -->
    <meta property="og:title" content="{{ $linkPage->meta_title ?? $linkPage->name }}">
    <meta property="og:description" content="{{ $linkPage->meta_description ?? $linkPage->bio }}">
    <meta property="og:image" content="{{ $linkPage->og_image ?? asset('storage/' . $linkPage->profile_image) }}">
    <meta property="og:url" content="{{ $linkPage->getPublicUrl() }}">
    <meta property="og:type" content="profile">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $linkPage->meta_title ?? $linkPage->name }}">
    <meta name="twitter:description" content="{{ $linkPage->meta_description ?? $linkPage->bio }}">
    <meta name="twitter:image" content="{{ $linkPage->og_image ?? asset('storage/' . $linkPage->profile_image) }}">

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Pretendard:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * { font-family: 'Pretendard', sans-serif; }

        /* 애니메이션 */
        .animate-fade { animation: fadeIn 0.5s ease-in; }
        .animate-slide { animation: slideUp 0.5s ease-out; }
        .animate-bounce { animation: bounceIn 0.5s ease-out; }
        .animate-zoom { animation: zoomIn 0.5s ease-out; }

        .animate-slow { animation-duration: 0.8s; }
        .animate-normal { animation-duration: 0.5s; }
        .animate-fast { animation-duration: 0.3s; }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideUp {
            from { transform: translateY(30px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        @keyframes bounceIn {
            0% { transform: scale(0.8); opacity: 0; }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); opacity: 1; }
        }

        @keyframes zoomIn {
            from { transform: scale(0.9); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        /* 호버 효과 */
        .hover-scale { transition: transform 0.2s; }
        .hover-scale:hover { transform: scale(1.05); }

        .hover-lift { transition: transform 0.2s, box-shadow 0.2s; }
        .hover-lift:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }

        .hover-glow { transition: box-shadow 0.2s; }
        .hover-glow:hover {
            box-shadow: 0 0 20px rgba(43, 127, 255, 0.5);
        }

        .hover-wiggle { transition: transform 0.3s; }
        .hover-wiggle:hover {
            animation: wiggle 0.5s ease-in-out;
        }

        @keyframes wiggle {
            0%, 100% { transform: rotate(0deg); }
            25% { transform: rotate(-5deg); }
            75% { transform: rotate(5deg); }
        }

        .hover-pulse { transition: transform 0.2s; }
        .hover-pulse:hover {
            animation: pulse 1s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        /* 배경 오버레이 패턴 */
        .overlay-dots {
            background-image: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
            background-size: 20px 20px;
        }

        .overlay-stripes {
            background-image: repeating-linear-gradient(
                45deg,
                rgba(255,255,255,0.05),
                rgba(255,255,255,0.05) 10px,
                transparent 10px,
                transparent 20px
            );
        }

        .overlay-grid {
            background-image:
                linear-gradient(rgba(255,255,255,0.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.05) 1px, transparent 1px);
            background-size: 30px 30px;
        }
    </style>
</head>
<body class="min-h-screen">
    <!-- 메인 컨테이너 -->
    <div class="relative min-h-screen"
        style="{{ $linkPage->getBackgroundStyle() }}">

        <!-- 배경 오버레이 -->
        @if($linkPage->background_overlay && $linkPage->background_overlay !== 'none')
        <div class="absolute inset-0 overlay-{{ $linkPage->background_overlay }}"></div>
        @endif

        <!-- 컨텐츠 -->
        <div class="relative z-10 max-w-2xl mx-auto px-4 py-8 sm:py-12">
            <!-- 수정 버튼 (권한 있을 때) -->
            @if($linkPage->canEdit(auth()->user(), session('page_password_' . $linkPage->id)))
            <div class="text-right mb-4">
                <a href="{{ route('linkpage.edit', $linkPage->uuid) }}"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-white/90 backdrop-blur-sm rounded-full text-sm font-medium text-gray-700 hover:bg-white transition">
                    ⚙️ 수정
                </a>
            </div>
            @endif

            <!-- 프로필 섹션 -->
            @if($linkPage->profile_layout === 'large')
                <!-- 큰 이미지 스타일 -->
                <div class="text-center mb-10 animate-{{ $linkPage->animation_entrance }} animate-{{ $linkPage->animation_speed }}">
                    @if($linkPage->profile_image)
                    <img src="{{ asset('storage/' . $linkPage->profile_image) }}"
                        alt="{{ $linkPage->name }}"
                        class="w-28 h-28 rounded-full mx-auto mb-5 border-4 border-white shadow-2xl object-cover">
                    @endif

                    <h1 class="text-3xl sm:text-4xl font-bold text-white mb-3 drop-shadow-lg">
                        {{ $linkPage->name }}

                        <!-- 뱃지 -->
                        @if($linkPage->badges)
                            @foreach($linkPage->badges as $badge)
                                @php $badgeConfig = config('linkkit.badges.' . $badge) @endphp
                                @if($badgeConfig)
                                <span class="inline-flex items-center gap-1 px-3 py-1 text-sm rounded-full ml-2"
                                    style="background-color: {{ $badgeConfig['bg'] }}; color: {{ $badgeConfig['color'] }};">
                                    {{ $badgeConfig['icon'] }} {{ $badgeConfig['label'] }}
                                </span>
                                @endif
                            @endforeach
                        @endif
                    </h1>

                    @if($linkPage->bio)
                    <p class="text-white/90 text-lg max-w-md mx-auto drop-shadow">{{ $linkPage->bio }}</p>
                    @endif
                </div>

            @elseif($linkPage->profile_layout === 'small')
                <!-- 작은 이미지 (명함) 스타일 -->
                <div class="flex items-center gap-5 mb-10 animate-{{ $linkPage->animation_entrance }} animate-{{ $linkPage->animation_speed }}">
                    @if($linkPage->profile_image)
                    <img src="{{ asset('storage/' . $linkPage->profile_image) }}"
                        alt="{{ $linkPage->name }}"
                        class="w-20 h-20 rounded-full border-3 border-white shadow-xl object-cover flex-shrink-0">
                    @endif

                    <div class="flex-1">
                        <h1 class="text-2xl font-bold text-white mb-1 drop-shadow-lg">
                            {{ $linkPage->name }}
                        </h1>
                        @if($linkPage->bio)
                        <p class="text-white/90 drop-shadow">{{ $linkPage->bio }}</p>
                        @endif
                    </div>
                </div>

            @elseif($linkPage->profile_layout === 'banner')
                <!-- 배너 스타일 -->
                <div class="relative mb-10 -mx-4 sm:mx-0 sm:rounded-3xl overflow-hidden">
                    <!-- 커버 이미지 -->
                    <div class="h-40 sm:h-48 bg-gradient-to-r from-blue-600 to-purple-600"
                        @if($linkPage->cover_image)
                        style="background-image: url('{{ asset('storage/' . $linkPage->cover_image) }}'); background-size: cover; background-position: center;"
                        @endif>
                    </div>

                    <!-- 프로필 -->
                    <div class="relative px-6 pb-6 -mt-16 animate-{{ $linkPage->animation_entrance }} animate-{{ $linkPage->animation_speed }}">
                        @if($linkPage->profile_image)
                        <img src="{{ asset('storage/' . $linkPage->profile_image) }}"
                            alt="{{ $linkPage->name }}"
                            class="w-24 h-24 rounded-full border-4 border-white shadow-2xl mb-4 object-cover">
                        @endif

                        <h1 class="text-2xl font-bold text-white mb-2 drop-shadow-lg">
                            {{ $linkPage->name }}
                        </h1>
                        @if($linkPage->bio)
                        <p class="text-white/90 drop-shadow">{{ $linkPage->bio }}</p>
                        @endif
                    </div>
                </div>
            @endif

            <!-- 링크 목록 -->
            <div class="space-y-4 mb-10">
                @foreach($linkPage->activeLinks as $index => $link)
                <a href="{{ route('linkpage.track', [$linkPage->uuid, $link->id]) }}"
                    class="{{ $link->getButtonClass() }} hover-{{ $link->hover_effect ?? 'scale' }}
                           bg-white/95 backdrop-blur-sm text-gray-900 font-medium text-center block
                           shadow-lg transition-all animate-{{ $linkPage->animation_entrance }} animate-{{ $linkPage->animation_speed }}"
                    style="{{ $link->getButtonStyle() }} animation-delay: {{ $index * 0.1 }}s;"
                    target="_blank" rel="noopener">

                    @if($link->type === 'product')
                        <!-- 상품 카드 스타일 -->
                        <div class="flex items-center gap-4">
                            @if($link->thumbnail)
                            <img src="{{ asset('storage/' . $link->thumbnail) }}"
                                class="w-16 h-16 rounded-lg object-cover">
                            @endif
                            <div class="flex-1 text-left">
                                <div class="font-bold">{{ $link->title }}</div>
                                @if($link->price)
                                <div class="text-sm">
                                    @if($link->sale_price)
                                        <span class="line-through text-gray-500">{{ $link->getFormattedPrice() }}</span>
                                        <span class="text-red-600 font-bold ml-2">{{ $link->getFormattedSalePrice() }}</span>
                                    @else
                                        <span class="font-bold">{{ $link->getFormattedPrice() }}</span>
                                    @endif
                                </div>
                                @endif
                            </div>
                        </div>
                    @elseif($link->type === 'image_card')
                        <!-- 이미지 카드 -->
                        @if($link->thumbnail)
                        <img src="{{ asset('storage/' . $link->thumbnail) }}"
                            class="w-full h-48 object-cover rounded-t-lg mb-3">
                        @endif
                        <div class="font-bold">{{ $link->title }}</div>
                        @if($link->description)
                        <div class="text-sm text-gray-600 mt-1">{{ $link->description }}</div>
                        @endif
                    @else
                        <!-- 기본 버튼 -->
                        @if($link->icon)
                        <span class="mr-2">{{ $link->icon }}</span>
                        @endif
                        {{ $link->title }}
                    @endif
                </a>
                @endforeach
            </div>

            <!-- QR 코드 & 공유 -->
            <div class="text-center space-y-4">
                <div class="inline-block bg-white p-4 rounded-2xl shadow-lg">
                    <img src="{{ $linkPage->getQrCodeUrl() }}" alt="QR Code" class="w-32 h-32">
                </div>

                <div class="flex gap-3 justify-center flex-wrap">
                    <button onclick="shareLink()"
                        class="px-5 py-2 bg-white/90 backdrop-blur-sm rounded-full font-medium text-gray-700 hover:bg-white transition shadow-lg">
                        🔗 공유하기
                    </button>

                    <button onclick="downloadQR()"
                        class="px-5 py-2 bg-white/90 backdrop-blur-sm rounded-full font-medium text-gray-700 hover:bg-white transition shadow-lg">
                        📥 QR 다운로드
                    </button>
                </div>
            </div>

            <!-- LinkKit 브랜딩 -->
            @if($linkPage->show_branding)
            <div class="mt-10 text-center">
                <a href="{{ route('home') }}"
                    class="inline-block text-white/70 hover:text-white text-sm transition">
                    Made with ❤️ by <strong>LinkKit</strong>
                </a>
            </div>
            @endif
        </div>
    </div>

    <script>
        // 공유하기
        function shareLink() {
            const url = "{{ $linkPage->getPublicUrl() }}";
            const title = "{{ $linkPage->name }}";

            if (navigator.share) {
                navigator.share({
                    title: title,
                    url: url
                });
            } else {
                // 클립보드 복사
                navigator.clipboard.writeText(url).then(() => {
                    alert('링크가 복사되었습니다!');
                });
            }
        }

        // QR 다운로드
        function downloadQR() {
            const qrUrl = "{{ $linkPage->getQrCodeUrl() }}";
            const link = document.createElement('a');
            link.href = qrUrl;
            link.download = 'linkkit-qr.png';
            link.click();
        }
    </script>
</body>
</html>
