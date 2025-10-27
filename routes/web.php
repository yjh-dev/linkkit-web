<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LinkPageController;
use App\Http\Controllers\SocialAuthController;
// routes/web.php

Route::get('/', function () {
    return view('welcome'); // 랜딩 페이지
})->name('home');

Route::get('/create', [LinkPageController::class, 'create'])->name('linkpage.create');
Route::post('/create', [LinkPageController::class, 'store'])->name('linkpage.store');
Route::get('/u/{uuid}', [LinkPageController::class, 'show'])->name('linkpage.show');
Route::get('/edit/{uuid}', [LinkPageController::class, 'edit'])->name('linkpage.edit');
Route::put('/edit/{uuid}', [LinkPageController::class, 'update'])->name('linkpage.update');
Route::delete('/delete/{uuid}', [LinkPageController::class, 'destroy'])->name('linkpage.destroy');
Route::get('/u/{uuid}/link/{link}', [LinkPageController::class, 'trackClick'])->name('linkpage.track');
