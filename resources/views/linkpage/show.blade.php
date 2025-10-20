@extends('layouts.app')

@section('title', $linkPage->name . ' - LinkKit')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-white py-12 px-4">
    <div class="container mx-auto max-w-2xl">

        <!-- 메인 카드 -->
        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden">

            <!-- 프로필 영역 -->
            <div class="bg-gradient-to-br from-linkkit-blue to-blue-600 px-8 py-12 text-center">
                <!-- 프로필 이미지 -->
                <div class="mb-6">
                    @if($linkPage->profile_image)
                        <img
                            src="{{ asset('storage/' . $linkPage->profile_image) }}"
                            alt="{{ $linkPage->name }}"
                            class="w-32 h-32 mx-auto rounded-full object-cover shadow-2xl ring-4 ring-white"
                        >
                    @else
                        <div class="w-32 h-32 mx-auto bg-white rounded-full flex items-center justify-center shadow-2xl ring-4 ring-white">
                            <span class="text-5xl text-blue-300">👤</span>
                        </div>
                    @endif
                </div>

                <!-- 이름 -->
                <h1 class="text-3xl font-bold text-white mb-4">
                    {{ $linkPage->name }}
                </h1>

                <!-- 소개 -->
                @if($linkPage->bio)
                    <p class="text-blue-50 text-base leading-relaxed max-w-lg mx-auto">
                        {{ $linkPage->bio }}
                    </p>
                @endif
            </div>

            <!-- 링크 목록 -->
            <div class="px-8 py-10">
                @if($linkPage->links->count() > 0)
                    <div class="space-y-4">
                        @foreach($linkPage->links as $link)
                        <a
                        href="{{ $link->url }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        onclick="trackClick({{ $link->id }})"
                        class="group block bg-white border-2 border-gray-200 rounded-2xl p-6 hover:border-linkkit-blue hover:shadow-lg transition-all transform hover:-translate-y-1"
                    >
                        <div class="flex items-center justify-between gap-4">
                            <div class="flex-1 min-w-0 pr-4">
                                <h3 class="font-bold text-gray-800 text-lg mb-2 group-hover:text-linkkit-blue transition-colors">
                                    {{ $link->title }}
                                </h3>
                                <p class="text-sm text-gray-500 truncate">
                                    {{ parse_url($link->url, PHP_URL_HOST) ?? $link->url }}
                                </p>
                            </div>
                            <div class="flex-shrink-0">
                                <svg class="w-6 h-6 text-gray-400 group-hover:text-linkkit-blue transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                        </div>
                    </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <p class="text-gray-400">등록된 링크가 없습니다.</p>
                    </div>
                @endif
            </div>

            <!-- 하단 액션 영역 -->
            <div class="px-8 pb-10 pt-6 border-t border-gray-100">
                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">

                    <!-- URL 복사 버튼 -->
                    <button
                        onclick="copyToClipboard()"
                        class="flex items-center gap-2 px-6 py-3 bg-gray-100 hover:bg-gray-200 rounded-xl transition-all font-medium text-gray-700"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                        <span>링크 복사</span>
                    </button>

                    <!-- QR코드 보기 버튼 -->
                    <button
                        onclick="toggleQRCode()"
                        class="flex items-center gap-2 px-6 py-3 bg-linkkit-blue hover:bg-blue-600 text-white rounded-xl transition-all font-medium shadow-md"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                        </svg>
                        <span>QR코드</span>
                    </button>

                    <!-- 카카오톡 공유 버튼 -->
                    <button
                        onclick="shareKakao()"
                        class="flex items-center gap-2 px-6 py-3 bg-yellow-400 hover:bg-yellow-500 rounded-xl transition-all font-medium text-gray-800"
                    >
                        <span class="text-lg">💬</span>
                        <span>카카오톡</span>
                    </button>
                </div>

                <!-- QR코드 모달 (숨김 상태) -->
                <div id="qrModal" class="hidden mt-8 text-center">
                    <div class="bg-gray-50 rounded-2xl p-8 inline-block">
                        <div id="qrcode" class="bg-white p-4 rounded-xl inline-block shadow-lg"></div>
                        <p class="text-sm text-gray-600 mt-4">QR코드를 스캔하여 접속하세요</p>
                    </div>
                </div>
            </div>

            <!-- 푸터 -->
            <div class="bg-gray-50 px-8 py-6 text-center border-t border-gray-100">
                <p class="text-sm text-gray-500 mb-3">나만의 링크 페이지를 만들어보세요</p>
                <a
                    href="/"
                    class="inline-flex items-center gap-2 text-linkkit-blue hover:text-blue-600 font-semibold transition-colors"
                >
                    <span class="text-xl font-bold">LinkKit</span>
                    <span>🔗</span>
                </a>
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
        navigator.clipboard.writeText(pageUrl).then(() => {
            alert('✅ 링크가 복사되었습니다!\n\n' + pageUrl);
        }).catch(() => {
            // 폴백: 임시 input 사용
            const input = document.createElement('input');
            input.value = pageUrl;
            document.body.appendChild(input);
            input.select();
            document.execCommand('copy');
            document.body.removeChild(input);
            alert('✅ 링크가 복사되었습니다!\n\n' + pageUrl);
        });
    }

    // QR코드 토글
    function toggleQRCode() {
        const modal = document.getElementById('qrModal');
        const qrcodeDiv = document.getElementById('qrcode');

        if (modal.classList.contains('hidden')) {
            modal.classList.remove('hidden');

            // QR코드 생성 (최초 1회만)
            if (!qrGenerated) {
                qrcodeDiv.innerHTML = ''; // 초기화
                new QRCode(qrcodeDiv, {
                    text: pageUrl,
                    width: 200,
                    height: 200,
                    colorDark: "#2B7FFF",
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
        // 카카오 SDK 초기화 (JavaScript 키 필요)
        if (!Kakao.isInitialized()) {
            // TODO: 카카오 개발자 센터에서 JavaScript 키 발급 필요
            alert('🚧 카카오톡 공유 기능은 준비 중입니다.\n\n지금은 "링크 복사" 버튼을 이용해주세요!');
            return;
        }

        Kakao.Share.sendDefault({
            objectType: 'feed',
            content: {
                title: pageName,
                description: pageBio,
                imageUrl: 'https://your-domain.com/og-image.png', // TODO: OG 이미지 설정
                link: {
                    mobileWebUrl: pageUrl,
                    webUrl: pageUrl,
                },
            },
            buttons: [
                {
                    title: '링크 보기',
                    link: {
                        mobileWebUrl: pageUrl,
                        webUrl: pageUrl,
                    },
                },
            ],
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
