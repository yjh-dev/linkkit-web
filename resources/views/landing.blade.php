@extends('layouts.app')

@section('title', 'LinkKit - 모든 링크를 하나로')

@section('content')
<div class="min-h-screen flex items-center justify-center">
    <div class="text-center">
        <h1 class="text-6xl font-bold text-linkkit-blue mb-4">
            LinkKit
        </h1>
        <p class="text-2xl text-gray-600 mb-8">
            모든 링크를 하나로.
        </p>
        <a href="/create" class="bg-linkkit-blue text-white px-8 py-4 rounded-xl text-lg font-semibold hover:bg-blue-600 transition">
            내 링크 만들기
        </a>
    </div>
</div>
@endsection
