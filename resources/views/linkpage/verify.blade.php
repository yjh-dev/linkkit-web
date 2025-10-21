@extends('layouts.app')

@section('title', '비밀번호 확인 - LinkKit')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-white flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">

        <!-- 카드 -->
        <div class="bg-white rounded-3xl shadow-2xl p-10">

            <!-- 아이콘 -->
            <div class="w-20 h-20 bg-linkkit-blue rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg">
                <span class="text-4xl">🔐</span>
            </div>

            <!-- 제목 -->
            <h1 class="text-3xl font-bold text-gray-900 text-center mb-3">
                비밀번호 확인
            </h1>
            <p class="text-gray-600 text-center mb-8">
                <span class="font-semibold">{{ $linkPage->name }}</span> 페이지를 수정하려면<br>
                생성 시 설정한 비밀번호를 입력하세요
            </p>

            <!-- 에러 메시지 -->
            @if($errors->any())
                <div class="bg-red-50 border-2 border-red-200 rounded-xl p-4 mb-6">
                    <p class="text-red-600 text-sm font-medium">
                        ⚠️ {{ $errors->first() }}
                    </p>
                </div>
            @endif

            <!-- 폼 -->
            <form action="{{ route('linkpage.verify', $linkPage->uuid) }}" method="POST">
                @csrf

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                        비밀번호
                    </label>
                    <input
                        type="password"
                        name="password"
                        required
                        autofocus
                        placeholder="비밀번호 입력"
                        class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-linkkit-blue focus:border-linkkit-blue transition-all text-lg"
                    >
                </div>

                <button
                    type="submit"
                    class="w-full bg-linkkit-blue hover:bg-blue-600 text-white py-4 rounded-xl font-bold text-lg transition-all shadow-md hover:shadow-lg"
                >
                    확인
                </button>
            </form>

            <!-- 취소 -->
            <div class="text-center mt-6">
                <a
                    href="{{ route('linkpage.show', $linkPage->uuid) }}"
                    class="text-gray-500 hover:text-gray-700 text-sm transition-colors"
                >
                    ← 돌아가기
                </a>
            </div>

        </div>

    </div>
</div>
@endsection
