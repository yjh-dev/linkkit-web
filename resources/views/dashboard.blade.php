@extends('layouts.app')

@section('title', '대시보드 - LinkKit')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-white py-12 px-4">
    <div class="container mx-auto max-w-6xl">

        <!-- 헤더 -->
        <div class="flex items-center justify-between mb-12">
            <div>
                <h1 class="text-4xl font-bold text-gray-900 mb-2">대시보드</h1>
                <p class="text-gray-600">안녕하세요, <span class="font-semibold text-linkkit-blue">{{ auth()->user()->name }}</span>님! 👋</p>
            </div>

            <div class="flex items-center gap-4">
                <a href="/choose-preset" class="bg-linkkit-blue hover:bg-blue-600 text-white px-6 py-3 rounded-xl font-semibold transition-all shadow-md hover:shadow-lg flex items-center gap-2">
                    <span>➕</span>
                    <span>새 페이지 만들기</span>
                </a>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-gray-500 hover:text-gray-700 transition-colors">
                        로그아웃
                    </button>
                </form>
            </div>
        </div>

        <!-- 통계 카드 -->
        <div class="grid md:grid-cols-3 gap-6 mb-12">
            <div class="bg-white rounded-2xl p-6 shadow-md">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <span class="text-2xl">📄</span>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">총 페이지</p>
                        <p class="text-3xl font-bold text-gray-900">{{ auth()->user()->linkPages->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-md">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                        <span class="text-2xl">🔗</span>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">총 링크</p>
                        <p class="text-3xl font-bold text-gray-900">
                            {{ auth()->user()->linkPages->sum(function($page) { return $page->links->count(); }) }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-md">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                        <span class="text-2xl">👆</span>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">총 클릭</p>
                        <p class="text-3xl font-bold text-gray-900">
                            {{ auth()->user()->linkPages->sum(function($page) {
                                return $page->links->sum('clicks');
                            }) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- 페이지 목록 -->
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">내 페이지</h2>

            @if(auth()->user()->linkPages->count() > 0)
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach(auth()->user()->linkPages as $page)
                        <div class="border-2 border-gray-200 rounded-2xl p-6 hover:border-linkkit-blue transition-all group">
                            <!-- 프리셋 뱃지 -->
                            <div class="flex items-center justify-between mb-4">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold
                                    @if($page->preset === 'basic')
                                        bg-blue-100 text-blue-700
                                    @elseif($page->preset === 'minimal')
                                        bg-gray-100 text-gray-700
                                    @elseif($page->preset === 'dark')
                                        bg-gray-900 text-white
                                    @endif">
                                    {{ ucfirst($page->preset) }}
                                </span>
                                <span class="text-xs text-gray-500">
                                    {{ $page->created_at->diffForHumans() }}
                                </span>
                            </div>

                            <!-- 페이지 정보 -->
                            <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $page->name }}</h3>
                            <p class="text-sm text-gray-600 mb-4 line-clamp-2">
                                {{ $page->bio ?? '소개 없음' }}
                            </p>

                            <!-- 통계 -->
                            <div class="flex items-center gap-4 text-sm text-gray-500 mb-4">
                                <span>🔗 {{ $page->links->count() }}개</span>
                                <span>👆 {{ $page->links->sum('clicks') }}회</span>
                            </div>

                            <!-- 버튼 -->
                            <div class="flex gap-2">
                                <a href="{{ route('linkpage.show', $page->uuid) }}"
                                   target="_blank"
                                   class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 py-2 px-4 rounded-xl text-center text-sm font-medium transition-all">
                                    보기
                                </a>
                                <a href="{{ route('linkpage.edit', $page->uuid) }}"
                                   class="flex-1 bg-linkkit-blue hover:bg-blue-600 text-white py-2 px-4 rounded-xl text-center text-sm font-medium transition-all">
                                    수정
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-4xl">📄</span>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">아직 페이지가 없어요</h3>
                    <p class="text-gray-500 mb-6">첫 링크 페이지를 만들어보세요!</p>
                    <a href="/choose-preset" class="inline-flex items-center gap-2 bg-linkkit-blue hover:bg-blue-600 text-white px-6 py-3 rounded-xl font-semibold transition-all">
                        <span>✨</span>
                        <span>페이지 만들기</span>
                    </a>
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
