<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LinkPageController;

// 랜딩 페이지
Route::get('/', function () {
    return view('landing');
});

// 링크 생성 페이지
Route::get('/create', function () {
    return view('create');
});

// 링크 페이지 저장
Route::post('/create', [LinkPageController::class, 'store'])->name('linkpage.store');

// 개인 링크 페이지
Route::get('/u/{uuid}', [LinkPageController::class, 'show'])->name('linkpage.show');

// 링크 클릭 추적
Route::post('/track/{linkId}', [LinkPageController::class, 'trackClick'])->name('link.track');
