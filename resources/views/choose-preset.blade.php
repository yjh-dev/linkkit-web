@extends('layouts.app')

@section('title', '프리셋 선택 - LinkKit')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-white py-12 px-4">
    <div class="container mx-auto max-w-6xl">

        <!-- 헤더 -->
        <div class="text-center mb-16">
            <a href="/" class="inline-flex items-center gap-2 group mb-8">
                <span class="text-4xl font-bold text-linkkit-blue">LinkKit</span>
                <span class="text-2xl group-hover:rotate-12 transition-transform">🔗</span>
            </a>
            <h1 class="text-4xl font-bold text-gray-900 mb-4">
                원하는 디자인을 선택하세요
            </h1>
            <p class="text-xl text-gray-600">
                나중에 언제든 변경할 수 있어요
            </p>
        </div>

        <!-- 프리셋 카드들 -->
        <div class="grid md:grid-cols-3 gap-8 mb-12">

            <!-- Basic 프리셋 -->
            <a href="/create?preset=basic" class="group block">
                <div class="bg-white rounded-3xl shadow-xl overflow-hidden hover:shadow-2xl transition-all transform hover:-translate-y-2">
                    <!-- 미리보기 -->
                    <div class="bg-gradient-to-br from-linkkit-blue to-blue-600 p-8 h-90 flex items-center justify-center">
                        <div class="bg-white rounded-2xl p-6 w-48 shadow-2xl">
                            <div class="w-16 h-16 bg-gradient-to-br from-linkkit-blue to-blue-600 rounded-full mx-auto mb-4"></div>
                            <div class="h-4 bg-gray-200 rounded mb-2"></div>
                            <div class="h-3 bg-gray-100 rounded mb-4"></div>
                            <div class="space-y-2">
                                <div class="h-10 bg-gray-50 border-2 border-gray-200 rounded-xl"></div>
                                <div class="h-10 bg-gray-50 border-2 border-gray-200 rounded-xl"></div>
                                <div class="h-10 bg-gray-50 border-2 border-gray-200 rounded-xl"></div>
                            </div>
                        </div>
                    </div>

                    <!-- 정보 -->
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-2xl font-bold text-gray-900">Basic</h3>
                            <span class="bg-linkkit-blue text-white px-3 py-1 rounded-full text-sm font-semibold">인기</span>
                        </div>
                        <p class="text-gray-600 mb-4 min-h-20">
                            밝고 친근한 파란색 디자인. 가장 많이 사용되는 스타일이에요.
                        </p>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">✨ 추천</span>
                            <span class="text-linkkit-blue font-semibold group-hover:gap-2 flex items-center gap-1 transition-all">
                                선택하기
                                <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </span>
                        </div>
                    </div>
                </div>
            </a>

            <!-- Minimal 프리셋 -->
            <a href="/create?preset=minimal" class="group block">
                <div class="bg-white rounded-3xl shadow-xl overflow-hidden hover:shadow-2xl transition-all transform hover:-translate-y-2">
                    <!-- 미리보기 -->
                    <div class="bg-gray-50 p-8 h-90 flex items-center justify-center">
                        <div class="bg-white rounded-2xl p-6 w-48 border-2 border-gray-200 shadow-lg">
                            <div class="w-16 h-16 bg-gray-200 rounded-full mx-auto mb-4"></div>
                            <div class="h-4 bg-gray-800 rounded mb-2"></div>
                            <div class="h-3 bg-gray-400 rounded mb-4"></div>
                            <div class="space-y-2">
                                <div class="h-10 border border-gray-300 rounded-lg"></div>
                                <div class="h-10 border border-gray-300 rounded-lg"></div>
                                <div class="h-10 border border-gray-300 rounded-lg"></div>
                            </div>
                        </div>
                    </div>

                    <!-- 정보 -->
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-2xl font-bold text-gray-900">Minimal</h3>
                        </div>
                        <p class="text-gray-600 mb-4 min-h-20">
                            심플하고 깔끔한 디자인. 전문가스러운 인상을 줘요.
                        </p>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">⚪ 심플</span>
                            <span class="text-gray-700 font-semibold group-hover:gap-2 flex items-center gap-1 transition-all">
                                선택하기
                                <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </span>
                        </div>
                    </div>
                </div>
            </a>

            <!-- Dark 프리셋 -->
            <a href="/create?preset=dark" class="group block">
                <div class="bg-white rounded-3xl shadow-xl overflow-hidden hover:shadow-2xl transition-all transform hover:-translate-y-2">
                    <!-- 미리보기 -->
                    <div class="bg-gray-900 p-8 h-90 flex items-center justify-center">
                        <div class="bg-gray-800 rounded-2xl p-6 w-48 shadow-2xl border border-gray-700">
                            <div class="w-16 h-16 bg-yellow-400 rounded-full mx-auto mb-4"></div>
                            <div class="h-4 bg-white rounded mb-2"></div>
                            <div class="h-3 bg-gray-400 rounded mb-4"></div>
                            <div class="space-y-2">
                                <div class="h-10 bg-gray-700 border border-gray-600 rounded-xl"></div>
                                <div class="h-10 bg-gray-700 border border-gray-600 rounded-xl"></div>
                                <div class="h-10 bg-gray-700 border border-gray-600 rounded-xl"></div>
                            </div>
                        </div>
                    </div>

                    <!-- 정보 -->
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-2xl font-bold text-gray-900">Dark</h3>
                            <span class="bg-gray-900 text-white px-3 py-1 rounded-full text-sm font-semibold">NEW</span>
                        </div>
                        <p class="text-gray-600 mb-4 min-h-20">
                            다크모드 디자인. 눈이 편하고 세련된 느낌을 줘요.
                        </p>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">🌙 모던</span>
                            <span class="text-gray-700 font-semibold group-hover:gap-2 flex items-center gap-1 transition-all">
                                선택하기
                                <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </span>
                        </div>
                    </div>
                </div>
            </a>

        </div>

        <!-- 하단 안내 -->
        <div class="text-center">
            <p class="text-gray-500 text-sm mb-4">
                💡 선택한 프리셋은 나중에 수정 페이지에서 변경할 수 있어요
            </p>
            <a href="/" class="text-gray-400 hover:text-gray-600 text-sm transition-colors">
                ← 돌아가기
            </a>
        </div>

    </div>
</div>
@endsection
