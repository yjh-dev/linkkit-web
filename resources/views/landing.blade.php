@extends('layouts.app')

@section('title', 'LinkKit - 모든 링크를 하나로')

@section('content')

<!-- 히어로 섹션 -->
<section class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-blue-50 flex items-center justify-center px-4 py-20">
    <div class="container mx-auto max-w-6xl">
        <div class="grid lg:grid-cols-2 gap-16 items-center">

            <!-- 좌측: 텍스트 & CTA -->
            <div class="text-center lg:text-left">
                <div class="inline-flex items-center gap-2 bg-blue-100 px-4 py-2 rounded-full mb-8">
                    <span class="w-2 h-2 bg-linkkit-blue rounded-full animate-pulse"></span>
                    <span class="text-sm font-semibold text-linkkit-blue">완전 무료 • 로그인 불필요</span>
                </div>

                <h1 class="text-5xl lg:text-6xl font-bold text-gray-900 mb-6 leading-tight">
                    모든 링크를
                    <span class="text-linkkit-blue">하나로</span>
                </h1>

                <p class="text-xl text-gray-600 mb-10 leading-relaxed">
                    인스타그램, 유튜브, 블로그, 쇼핑몰까지<br>
                    여러 링크를 하나의 페이지에 모아보세요.<br>
                    <span class="font-semibold text-gray-800">단 1분이면 완성됩니다.</span>
                </p>

                <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start mb-8">
                    <a
                        href="/choose-preset"
                        class="group bg-linkkit-blue hover:bg-blue-600 text-white px-8 py-4 rounded-2xl font-bold text-lg transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-1 flex items-center justify-center gap-2"
                    >
                        <span>✨</span>
                        <span>무료로 시작하기</span>
                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>

                    <a
                        href="#how-it-works"
                        class="bg-white hover:bg-gray-50 text-gray-700 px-8 py-4 rounded-2xl font-semibold text-lg transition-all border-2 border-gray-200 flex items-center justify-center gap-2"
                    >
                        <span>📖</span>
                        <span>사용 방법 보기</span>
                    </a>
                </div>

                <div class="flex items-center gap-6 justify-center lg:justify-start text-sm text-gray-500">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <span>회원가입 없음</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <span>1분 완성</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <span>완전 무료</span>
                    </div>
                </div>
            </div>

            <!-- 우측: 모바일 미리보기 -->
            <div class="flex justify-center">
                <div class="relative">
                    <!-- 배경 효과 -->
                    <div class="absolute inset-0 bg-linkkit-blue/20 rounded-[3rem] blur-3xl"></div>

                    <!-- 스마트폰 프레임 -->
                    <div class="relative bg-gray-900 rounded-[3rem] p-3 shadow-2xl transform hover:scale-105 transition-transform">
                        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-40 h-7 bg-gray-900 rounded-b-3xl z-10"></div>

                        <div class="bg-white rounded-[2.5rem] overflow-hidden" style="height: 600px; width: 300px;">
                            <div class="h-full overflow-y-auto p-8">
                                <!-- 예시 프로필 -->
                                <div class="text-center mb-8">
                                    <div class="w-24 h-24 mx-auto bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center mb-4 shadow-lg">
                                        <span class="text-white text-4xl">✨</span>
                                    </div>
                                    <h2 class="text-xl font-bold text-gray-800 mb-2">당신의 이름</h2>
                                    <p class="text-sm text-gray-600">여기에 소개를 적어보세요</p>
                                </div>

                                <!-- 예시 링크들 -->
                                <div class="space-y-3">
                                    <div class="bg-white border-2 border-gray-200 rounded-2xl p-4 hover:border-linkkit-blue transition-all cursor-pointer">
                                        <p class="font-bold text-gray-800">📸 Instagram</p>
                                    </div>
                                    <div class="bg-white border-2 border-gray-200 rounded-2xl p-4 hover:border-linkkit-blue transition-all cursor-pointer">
                                        <p class="font-bold text-gray-800">🎵 YouTube</p>
                                    </div>
                                    <div class="bg-white border-2 border-gray-200 rounded-2xl p-4 hover:border-linkkit-blue transition-all cursor-pointer">
                                        <p class="font-bold text-gray-800">✍️ Blog</p>
                                    </div>
                                    <div class="bg-white border-2 border-gray-200 rounded-2xl p-4 hover:border-linkkit-blue transition-all cursor-pointer">
                                        <p class="font-bold text-gray-800">🛍️ Shop</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- 주요 기능 섹션 -->
<section class="py-20 px-4 bg-white">
    <div class="container mx-auto max-w-6xl">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-900 mb-4">
                왜 LinkKit인가요?
            </h2>
            <p class="text-xl text-gray-600">
                복잡한 링크 관리, 이제 간단하게 해결하세요
            </p>
        </div>

        <div class="grid md:grid-cols-3 gap-10">
            <!-- 기능 1 -->
            <div class="text-center p-8 rounded-3xl hover:bg-blue-50 transition-all group">
                <div class="w-16 h-16 bg-linkkit-blue rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform shadow-lg">
                    <span class="text-3xl">⚡</span>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-4">1분 만에 완성</h3>
                <p class="text-gray-600 leading-relaxed">
                    회원가입도, 복잡한 설정도 필요 없어요. 이름, 소개, 링크만 입력하면 바로 완성!
                </p>
            </div>

            <!-- 기능 2 -->
            <div class="text-center p-8 rounded-3xl hover:bg-blue-50 transition-all group">
                <div class="w-16 h-16 bg-linkkit-blue rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform shadow-lg">
                    <span class="text-3xl">📱</span>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-4">모바일 최적화</h3>
                <p class="text-gray-600 leading-relaxed">
                    스마트폰에서도 완벽하게 작동해요. 인스타그램 프로필 링크로 사용하기 딱 좋아요.
                </p>
            </div>

            <!-- 기능 3 -->
            <div class="text-center p-8 rounded-3xl hover:bg-blue-50 transition-all group">
                <div class="w-16 h-16 bg-linkkit-blue rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform shadow-lg">
                    <span class="text-3xl">🔗</span>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-4">무제한 링크</h3>
                <p class="text-gray-600 leading-relaxed">
                    SNS, 블로그, 쇼핑몰, 유튜브... 원하는 만큼 링크를 추가하고 자유롭게 관리하세요.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- 사용 방법 섹션 -->
<section id="how-it-works" class="py-20 px-4 bg-gradient-to-br from-blue-50 to-white">
    <div class="container mx-auto max-w-5xl">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-900 mb-4">
                3단계면 끝!
            </h2>
            <p class="text-xl text-gray-600">
                누구나 쉽게 만들 수 있어요
            </p>
        </div>

        <div class="space-y-12">
            <!-- Step 1 -->
            <div class="flex flex-col md:flex-row items-center gap-8 bg-white rounded-3xl p-10 shadow-lg hover:shadow-xl transition-all">
                <div class="flex-shrink-0 w-20 h-20 bg-linkkit-blue rounded-2xl flex items-center justify-center shadow-lg">
                    <span class="text-3xl font-bold text-white">1</span>
                </div>
                <div class="flex-1 text-center md:text-left">
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">정보 입력</h3>
                    <p class="text-gray-600 text-lg">
                        이름, 소개, 프로필 사진을 입력하세요. 실시간으로 미리보기가 제공돼요!
                    </p>
                </div>
            </div>

            <!-- Step 2 -->
            <div class="flex flex-col md:flex-row items-center gap-8 bg-white rounded-3xl p-10 shadow-lg hover:shadow-xl transition-all">
                <div class="flex-shrink-0 w-20 h-20 bg-linkkit-blue rounded-2xl flex items-center justify-center shadow-lg">
                    <span class="text-3xl font-bold text-white">2</span>
                </div>
                <div class="flex-1 text-center md:text-left">
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">링크 추가</h3>
                    <p class="text-gray-600 text-lg">
                        인스타그램, 유튜브, 블로그 등 원하는 링크를 자유롭게 추가하세요.
                    </p>
                </div>
            </div>

            <!-- Step 3 -->
            <div class="flex flex-col md:flex-row items-center gap-8 bg-white rounded-3xl p-10 shadow-lg hover:shadow-xl transition-all">
                <div class="flex-shrink-0 w-20 h-20 bg-linkkit-blue rounded-2xl flex items-center justify-center shadow-lg">
                    <span class="text-3xl font-bold text-white">3</span>
                </div>
                <div class="flex-1 text-center md:text-left">
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">공유하기</h3>
                    <p class="text-gray-600 text-lg">
                        생성된 링크를 SNS 프로필에 등록하고, QR코드로 공유하세요!
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 최종 CTA 섹션 -->
<section class="py-20 px-4 bg-gradient-to-br from-linkkit-blue to-blue-600">
    <div class="container mx-auto max-w-4xl text-center">
        <h2 class="text-4xl md:text-5xl font-bold text-white mb-6">
            지금 바로 시작하세요
        </h2>
        <p class="text-xl text-blue-100 mb-10">
            회원가입 없이, 단 1분이면 당신만의 링크 페이지가 완성됩니다
        </p>

        <a
            href="/choose-preset"
            class="inline-flex items-center gap-3 bg-white text-linkkit-blue px-10 py-5 rounded-2xl font-bold text-xl hover:bg-gray-100 transition-all shadow-2xl hover:shadow-3xl transform hover:-translate-y-1"
        >
            <span>✨</span>
            <span>무료로 만들기</span>
            <span>🚀</span>
        </a>

        <p class="text-blue-100 mt-8 text-sm">
            신용카드 등록 불필요 • 언제든 무료
        </p>
    </div>
</section>

<!-- 푸터 -->
<footer class="bg-gray-900 text-gray-400 py-12 px-4">
    <div class="container mx-auto max-w-6xl">
        <div class="grid md:grid-cols-3 gap-10 mb-10">
            <!-- 브랜드 -->
            <div>
                <div class="flex items-center gap-2 mb-4">
                    <span class="text-2xl font-bold text-white">LinkKit</span>
                    <span class="text-2xl">🔗</span>
                </div>
                <p class="text-sm leading-relaxed">
                    모든 링크를 하나로 모으는<br>
                    가장 쉽고 빠른 방법
                </p>
            </div>

            <!-- 링크 -->
            <div>
                <h3 class="text-white font-semibold mb-4">바로가기</h3>
                <ul class="space-y-2 text-sm">
                    <li><a href="/choose-preset" class="hover:text-white transition-colors">링크 만들기</a></li>
                    <li><a href="#how-it-works" class="hover:text-white transition-colors">사용 방법</a></li>
                </ul>
            </div>

            <!-- 정보 -->
            <div>
                <h3 class="text-white font-semibold mb-4">정보</h3>
                <p class="text-sm">
                    © 2025 LinkKit<br>
                    All rights reserved.
                </p>
            </div>
        </div>

        <div class="border-t border-gray-800 pt-8 text-center text-sm">
            <p>Made with 💙 by LinkKit Team</p>
        </div>
    </div>
</footer>

@endsection
