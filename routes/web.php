<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LinkPageController;
use App\Http\Controllers\SocialAuthController;

// 랜딩 페이지
Route::get('/', function () {
    return view('landing');
});

// 프리셋 선택 페이지 (랜딩 다음)
Route::get('/choose-preset', function () {
    return view('choose-preset');
})->name('choose.preset');

// 링크 생성 페이지
Route::get('/create', function (Request $request) {
    $preset = $request->query('preset', 'basic'); // 기본값 basic

    // 유효한 프리셋인지 확인
    if (!in_array($preset, ['basic', 'minimal', 'dark'])) {
        $preset = 'basic';
    }

    return view('create', compact('preset'));
});
// 링크 페이지 저장
Route::post('/create', [LinkPageController::class, 'store'])->name('linkpage.store');

// 개인 링크 페이지
Route::get('/u/{uuid}', [LinkPageController::class, 'show'])->name('linkpage.show');

// 링크 클릭 추적
Route::post('/track/{linkId}', [LinkPageController::class, 'trackClick'])->name('link.track');



// 비밀번호 확인 페이지
Route::get('/edit/{uuid}', [LinkPageController::class, 'editForm'])->name('linkpage.edit.form');

// 비밀번호 확인 처리
Route::post('/verify/{uuid}', [LinkPageController::class, 'verifyPassword'])->name('linkpage.verify');

// 수정 페이지
Route::get('/edit/{uuid}/page', [LinkPageController::class, 'edit'])->name('linkpage.edit');

// 수정 처리
Route::put('/update/{uuid}', [LinkPageController::class, 'update'])->name('linkpage.update');


// ✨ 소셜 로그인 라우트 추가!
Route::get('/auth/kakao', [SocialAuthController::class, 'redirectToKakao'])->name('auth.kakao');
Route::get('/auth/kakao/callback', [SocialAuthController::class, 'handleKakaoCallback']);

// ✨ 로그아웃
Route::post('/logout', [SocialAuthController::class, 'logout'])->name('logout');

// ✨ 대시보드 (로그인 필요)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
