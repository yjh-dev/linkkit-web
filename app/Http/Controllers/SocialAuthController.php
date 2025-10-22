<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    // 카카오 로그인 페이지로 리다이렉트
    public function redirectToKakao()
    {
        return Socialite::driver('kakao')->redirect();
    }

    // 카카오 콜백 처리
    public function handleKakaoCallback()
    {
        try {


            // 카카오로부터 사용자 정보 받기
            $socialUser = Socialite::driver('kakao')->user();

            // dd($socialUser);
            // 사용자 찾기 또는 생성
            $user = User::findOrCreateSocialUser('kakao', $socialUser);


            // 로그인
            Auth::login($user, true); // true = remember me

            // 대시보드로 리다이렉트
            return redirect()->intended('/dashboard');

        } catch (\Exception $e) {
            Log::error('카카오 로그인 콜백 처리 오류: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect('/')->with('error', '카카오 로그인에 실패했습니다. 다시 시도해주세요.');
        }
    }

    // 로그아웃
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', '로그아웃되었습니다.');
    }
}
