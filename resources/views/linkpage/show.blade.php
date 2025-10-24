@extends('layouts.app')

@section('title', $linkPage->name . ' - LinkKit')

@section('content')



    {{-- ✨ 사용자 커스텀 배경 --}}
    <div class="min-h-screen py-12 px-4"
        style="background: linear-gradient(135deg, {{ $linkPage->background_color . '40' }}, {{ $linkPage->background_secondary_color ? $linkPage->background_secondary_color . '40' : '#ffffff40' }});">

        <div class="container mx-auto max-w-2xl">

            <!-- 메인 카드 -->
            <div
                class="relative  rounded-3xl shadow-2xl overflow-hidden
            @if ($linkPage->preset === 'basic') bg-white
            @elseif($linkPage->preset === 'minimal')
                bg-white border border-gray-200
            @elseif($linkPage->preset === 'dark')
                bg-gray-800 border border-gray-700 @endif">


                {{-- ✨ 수정 버튼 (우측 상단) --}}
                @if (auth()->check() && $linkPage->isOwnedBy(auth()->user()))
                    {{-- 로그인 사용자가 소유자인 경우 --}}
                    <div class="absolute top-4 right-4 z-10">
                        <a href="{{ route('linkpage.edit', $linkPage->uuid) }}"
                            class="group flex items-center justify-center w-12 h-12 rounded-full shadow-lg transition-all hover:scale-110
               @if ($linkPage->preset === 'basic') bg-white hover:bg-linkkit-blue text-gray-600 hover:text-white
               @elseif($linkPage->preset === 'minimal')
                   bg-white hover:bg-gray-800 text-gray-600 hover:text-white border border-gray-300
               @elseif($linkPage->preset === 'dark')
                   bg-gray-700 hover:bg-yellow-400 text-gray-300 hover:text-gray-900 @endif">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </a>
                    </div>
                @else
                    {{-- 비회원 페이지인 경우 --}}
                    <div class="absolute top-4 right-4 z-10">
                        <a href="{{ route('linkpage.edit.form', $linkPage->uuid) }}"
                            class="group flex items-center justify-center w-12 h-12 rounded-full shadow-lg transition-all hover:scale-110
               @if ($linkPage->preset === 'basic') bg-white hover:bg-linkkit-blue text-gray-600 hover:text-white
               @elseif($linkPage->preset === 'minimal')
                   bg-white hover:bg-gray-800 text-gray-600 hover:text-white border border-gray-300
               @elseif($linkPage->preset === 'dark')
                   bg-gray-700 hover:bg-yellow-400 text-gray-300 hover:text-gray-900 @endif">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </a>
                    </div>
                @endif


                <div
                    style="
                @if ($linkPage->background_type === 'gradient' && $linkPage->background_secondary_color) background: linear-gradient(135deg, {{ $linkPage->background_color }}, {{ $linkPage->background_secondary_color }});

                @else background-color: {{ $linkPage->background_color }}; @endif">
                    <!-- 프로필 영역 -->
                    <div class="px-8 py-12 text-center">

                        <!-- 프로필 이미지 -->
                        <div class="mb-6">
                            @if ($linkPage->profile_image)
                                <img src="{{ asset('storage/' . $linkPage->profile_image) }}" alt="{{ $linkPage->name }}"
                                    class="w-32 h-32 mx-auto rounded-full object-cover shadow-2xl"
                                    style="border: 4px solid {{ $linkPage->color }}40;">
                            @else
                                <div class="w-32 h-32 mx-auto rounded-full flex items-center justify-center shadow-2xl"
                                    style="background: linear-gradient(to bottom right, {{ $linkPage->color }}20, {{ $linkPage->color }}10); border: 4px solid {{ $linkPage->color }}40;">
                                    <span class="text-5xl">👤</span>
                                </div>
                            @endif
                        </div>

                        <!-- 이름 -->
                        <h1 class="text-3xl font-bold mb-4" style="color: {{ $linkPage->color }};">
                            {{ $linkPage->name }}
                        </h1>

                        <!-- 소개 -->
                        @if ($linkPage->bio)
                            <p
                                class="text-base leading-relaxed max-w-lg mx-auto
                        @if ($linkPage->preset === 'basic') text-gray-600
                        @elseif($linkPage->preset === 'minimal')
                            text-gray-600
                        @elseif($linkPage->preset === 'dark')
                            text-gray-400 @endif">
                                {{ $linkPage->bio }}
                            </p>
                        @endif
                    </div>

                    <!-- 링크 목록 -->
                    <div class="px-8 py-10">
                        @if ($linkPage->links->count() > 0)
                            <div class="space-y-4">
                                @foreach ($linkPage->links as $link)
                                    <a href="{{ $link->url }}" target="_blank" rel="noopener noreferrer"
                                        onclick="trackClick({{ $link->id }})"
                                        class="group block rounded-2xl p-5 transition-all transform hover:-translate-y-1
                                @if ($linkPage->preset === 'basic') bg-white border-2
                                @elseif($linkPage->preset === 'minimal') bg-white border
                                @elseif($linkPage->preset === 'dark') bg-gray-700 border-2 @endif"
                                        style="border-color: {{ $linkPage->color }}40;"
                                        onmouseover="this.style.borderColor='{{ $linkPage->color }}'"
                                        onmouseout="this.style.borderColor='{{ $linkPage->color }}40'">

                                        <div class="flex items-center justify-between gap-4">
                                            <div class="flex-1 min-w-0">
                                                <h3 class="font-bold text-base mb-2 transition-colors"
                                                    style="color:
                                            @if ($linkPage->preset === 'dark') #FFFFFF
                                            @else
                                                {{ $linkPage->color }} @endif
                                            ;">
                                                    {{ $link->title }}
                                                </h3>
                                                <p
                                                    class="text-xs truncate
                                            @if ($linkPage->preset === 'dark') text-gray-400
                                            @else text-gray-500 @endif">
                                                    {{ parse_url($link->url, PHP_URL_HOST) ?? $link->url }}
                                                </p>
                                            </div>
                                            <div class="flex-shrink-0">
                                                <svg class="w-5 h-5 transition-colors"
                                                    style="color: {{ $linkPage->color }};" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 5l7 7-7 7"></path>
                                                </svg>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-12">
                                <p
                                    class="
                            @if ($linkPage->preset === 'basic') text-gray-400
                            @elseif($linkPage->preset === 'minimal')
                                text-gray-400
                            @elseif($linkPage->preset === 'dark')
                                text-gray-500 @endif">
                                    등록된 링크가 없습니다.</p>
                            </div>
                        @endif
                    </div>

                    <!-- 하단 액션 영역 -->
                    <div
                        class="px-8 pb-10 pt-6 border-t
                @if ($linkPage->preset === 'basic') border-gray-100
                @elseif($linkPage->preset === 'minimal')
                    border-gray-200
                @elseif($linkPage->preset === 'dark')
                    border-gray-700 @endif">

                        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">

                            <!-- URL 복사 버튼 -->
                            <button onclick="copyToClipboard()"
                                class="flex items-center gap-2 px-6 py-3 rounded-xl transition-all font-medium
                            @if ($linkPage->preset === 'basic') bg-gray-100 hover:bg-gray-200 text-gray-700
                            @elseif($linkPage->preset === 'minimal')
                                bg-gray-100 hover:bg-gray-200 text-gray-700
                            @elseif($linkPage->preset === 'dark')
                                bg-gray-700 hover:bg-gray-600 text-gray-300 @endif">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z">
                                    </path>
                                </svg>
                                <span>링크 복사</span>
                            </button>

                            <!-- QR코드 보기 버튼 -->
                            <button onclick="toggleQRCode()"
                                class="flex items-center gap-2 px-6 py-3 rounded-xl transition-all font-medium shadow-md text-white"
                                style="background-color: {{ $linkPage->color }};"
                                onmouseover="this.style.backgroundColor='{{ $linkPage->color }}dd'"
                                onmouseout="this.style.backgroundColor='{{ $linkPage->color }}'">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z">
                                    </path>
                                </svg>
                                <span>QR코드</span>
                            </button>

                            <!-- 카카오톡 공유 버튼 -->
                            <button onclick="shareKakao()"
                                class="flex items-center gap-2 px-6 py-3 bg-yellow-400 hover:bg-yellow-500 rounded-xl transition-all font-medium text-gray-800">
                                <span class="text-lg">💬</span>
                                <span>카카오톡</span>
                            </button>
                        </div>

                        <!-- QR코드 모달 (숨김 상태) -->
                        <div id="qrModal" class="hidden mt-8 text-center">
                            <div
                                class="rounded-2xl p-8 inline-block
                        @if ($linkPage->preset === 'basic') bg-gray-50
                        @elseif($linkPage->preset === 'minimal')
                            bg-gray-100
                        @elseif($linkPage->preset === 'dark')
                            bg-gray-700 @endif">
                                <div id="qrcode" class="bg-white p-4 rounded-xl inline-block shadow-lg"></div>
                                <p
                                    class="text-sm mt-4
                            @if ($linkPage->preset === 'basic') text-gray-600
                            @elseif($linkPage->preset === 'minimal')
                                text-gray-600
                            @elseif($linkPage->preset === 'dark')
                                text-gray-300 @endif">
                                    QR코드를 스캔하여 접속하세요</p>
                            </div>
                        </div>
                    </div>

                    <!-- 푸터 -->
                    <div
                        class="px-8 py-6 text-center border-t
                @if ($linkPage->preset === 'basic') bg-gray-50 border-gray-100
                @elseif($linkPage->preset === 'minimal')
                    bg-gray-50 border-gray-200
                @elseif($linkPage->preset === 'dark')
                    bg-gray-900 border-gray-800 @endif">

                        <p
                            class="text-sm mb-3
                    @if ($linkPage->preset === 'basic') text-gray-500
                    @elseif($linkPage->preset === 'minimal')
                        text-gray-500
                    @elseif($linkPage->preset === 'dark')
                        text-gray-500 @endif">
                            나만의 링크 페이지를 만들어보세요</p>

                        <a href="/"
                            class="inline-flex items-center gap-2 font-semibold transition-colors
                        @if ($linkPage->preset === 'basic') text-linkkit-blue hover:text-blue-600
                        @elseif($linkPage->preset === 'minimal')
                            text-gray-700 hover:text-gray-900
                        @elseif($linkPage->preset === 'dark')
                            text-yellow-400 hover:text-yellow-500 @endif">
                            <span class="text-xl font-bold" style="color: {{ $linkPage->color }};">LinkKit</span>
                            <span>🔗</span>
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- QR Code 라이브러리 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <!-- 카카오 SDK -->
    <script src="https://t1.kakaocdn.net/kakao_js_sdk/2.7.2/kakao.min.js"></script>

    <script>
        const pageUrl = "{{ url('/u/' . $linkPage->uuid) }}";
        const pageName = "{{ $linkPage->name }}";
        const pageBio = "{{ $linkPage->bio ?? '나의 링크 모음' }}";
        const pageColor = "{{ $linkPage->color }}"; // ✨ 추가!
        let qrGenerated = false;


        // 링크 클릭 추적
        function trackClick(linkId) {
            fetch(`/track/${linkId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).catch(err => console.log('Track error:', err));
        }

        // URL 복사
        function copyToClipboard() {

            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(pageUrl)
                    .then(() => alert('✅ 링크가 복사되었습니다!\n\n' + pageUrl))
                    .catch(() => fallbackCopy(pageUrl));
            } else {
                fallbackCopy(pageUrl);
            }
        }

        function fallbackCopy(text) {
            const input = document.createElement('input');
            input.value = text;
            document.body.appendChild(input);
            input.select();
            document.execCommand('copy');
            document.body.removeChild(input);
            alert('✅ 링크가 복사되었습니다!\n\n' + text);
        }

        // QR코드 토글
        function toggleQRCode() {
            const modal = document.getElementById('qrModal');
            const qrcodeDiv = document.getElementById('qrcode');

            if (modal.classList.contains('hidden')) {
                modal.classList.remove('hidden');

                if (!qrGenerated) {
                    qrcodeDiv.innerHTML = '';

                    new QRCode(qrcodeDiv, {
                        text: pageUrl,
                        width: 200,
                        height: 200,
                        colorDark: pageColor, // ✨ 사용자 색상 적용!
                        colorLight: "#ffffff",
                        correctLevel: QRCode.CorrectLevel.H
                    });
                    qrGenerated = true;
                }
            } else {
                modal.classList.add('hidden');
            }
        }
        // 카카오톡 공유
        function shareKakao() {
            if (!Kakao.isInitialized()) {
                alert('🚧 카카오톡 공유 기능은 준비 중입니다.\n\n지금은 "링크 복사" 버튼을 이용해주세요!');
                return;
            }

            Kakao.Share.sendDefault({
                objectType: 'feed',
                content: {
                    title: pageName,
                    description: pageBio,
                    imageUrl: 'https://your-domain.com/og-image.png',
                    link: {
                        mobileWebUrl: pageUrl,
                        webUrl: pageUrl,
                    },
                },
                buttons: [{
                    title: '링크 보기',
                    link: {
                        mobileWebUrl: pageUrl,
                        webUrl: pageUrl,
                    },
                }, ],
            });
        }

        // 페이지 로드 시 애니메이션
        document.addEventListener('DOMContentLoaded', function() {
            const links = document.querySelectorAll('.group');
            links.forEach((link, index) => {
                link.style.opacity = '0';
                link.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    link.style.transition = 'all 0.5s ease';
                    link.style.opacity = '1';
                    link.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>
@endsection
