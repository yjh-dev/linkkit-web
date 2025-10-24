<?php

namespace App\Http\Controllers;

use App\Models\Link;
use App\Models\LinkPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class LinkPageController extends Controller
{
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|max:255',
            'bio' => 'nullable|max:500',
            'password' => auth()->check() ? 'nullable|min:4|max:20' : 'required|min:4|max:20',
            'preset' => 'required|in:basic,minimal,dark',
            'color' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
            'background_type' => 'required|in:solid,gradient',  // ✨ 추가!
            'background_color' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',  // ✨ 추가!
            'background_secondary_color' => 'nullable|regex:/^#[0-9A-Fa-f]{6}$/',  // ✨ 추가!
            'profile_image' => 'nullable|image|max:2048',
            'links' => 'required|array|min:1',
            'links.*.title' => 'required|max:255',
            'links.*.url' => 'required|url|max:500',
        ];

        $validated = $request->validate($rules);

        // 프로필 이미지 저장
        $profileImagePath = null;
        if ($request->hasFile('profile_image')) {
            $profileImagePath = $request->file('profile_image')->store('profiles', 'public');
        }

        // 링크 페이지 생성
        $linkPage = LinkPage::create([
            'user_id' => auth()->id(),
            'name' => $validated['name'],
            'bio' => $validated['bio'] ?? null,
            'password' => $validated['password'] ? Hash::make($validated['password']) : null,
            'preset' => $validated['preset'],
            'color' => $validated['color'],
            'background_type' => $validated['background_type'],  // ✨ 추가!
            'background_color' => $validated['background_color'],  // ✨ 추가!
            'background_secondary_color' => $validated['background_secondary_color'],  // ✨ 추가!
            'profile_image' => $profileImagePath,
        ]);

        // 링크들 저장 (기존과 동일)
        foreach ($validated['links'] as $index => $linkData) {
            Link::create([
                'link_page_id' => $linkPage->id,
                'title' => $linkData['title'],
                'url' => $linkData['url'],
                'order' => $index,
            ]);
        }

        return redirect()->route('linkpage.show', $linkPage->uuid);
    }

    // 링크 페이지 조회
    public function show($uuid)
    {
        $linkPage = LinkPage::where('uuid', $uuid)
            ->with('links')
            ->firstOrFail();

        return view('linkpage.show', compact('linkPage'));
    }

    // 링크 클릭 추적
    public function trackClick($linkId)
    {
        $link = Link::findOrFail($linkId);
        $link->incrementClicks();

        return response()->json(['success' => true]);
    }


    // 비밀번호 확인 페이지 (수정 전)
    public function editForm($uuid)
    {
        $linkPage = LinkPage::where('uuid', $uuid)->firstOrFail();
        return view('linkpage.verify', compact('linkPage'));
    }

    // 비밀번호 확인 처리
    public function verifyPassword(Request $request, $uuid)
    {
        $request->validate([
            'password' => 'required'
        ]);

        $linkPage = LinkPage::where('uuid', $uuid)->firstOrFail();

        // 비밀번호 확인
        if (!$linkPage->checkPassword($request->password)) {
            return back()->withErrors(['password' => '비밀번호가 일치하지 않습니다.']);
        }

        // 비밀번호가 맞으면 세션에 저장
        session(['verified_' . $uuid => true]);

        return redirect()->route('linkpage.edit', $uuid);
    }

    // 수정 페이지
    public function edit($uuid)
    {
        $linkPage = LinkPage::where('uuid', $uuid)
            ->with('links')
            ->firstOrFail();

        // ✨ 로그인 사용자가 소유자면 바로 접근 가능
        if ($linkPage->isOwnedBy(auth()->user())) {
            return view('linkpage.edit', compact('linkPage'));
        }

        // 비회원이거나 다른 사람 페이지면 세션 확인
        if (!session('verified_' . $uuid)) {
            return redirect()->route('linkpage.edit.form', $uuid)
                ->withErrors(['password' => '먼저 비밀번호를 입력해주세요.']);
        }

        return view('linkpage.edit', compact('linkPage'));
    }

    // 수정 처리
    public function update(Request $request, $uuid)
    {
        // 모든 세션


        // 세션 확인
        if (!Auth::check() ) {
            return redirect()->route('linkpage.edit.form', $uuid);
        }

        $linkPage = LinkPage::where('uuid', $uuid)->firstOrFail();

        // 유효성 검증
        $validated = $request->validate([
            'name' => 'required|max:255',
            'bio' => 'nullable|max:500',
            'preset' => 'required|in:basic,minimal,dark',
            'color' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
            'background_type' => 'required|in:solid,gradient',  // ✨ 추가!
            'background_color' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',  // ✨ 추가!
            'background_secondary_color' => 'nullable|regex:/^#[0-9A-Fa-f]{6}$/',  // ✨ 추가!
            'profile_image' => 'nullable|image|max:2048',
            'links' => 'required|array|min:1',
            'links.*.title' => 'required|max:255',
            'links.*.url' => 'required|url|max:500',
        ]);

        // 프로필 이미지 처리
        if ($request->hasFile('profile_image')) {
            // 기존 이미지 삭제
            if ($linkPage->profile_image) {
                Storage::disk('public')->delete($linkPage->profile_image);
            }
            $validated['profile_image'] = $request->file('profile_image')->store('profiles', 'public');
        }

        // 페이지 정보 업데이트
        $linkPage->update([
            'name' => $validated['name'],
            'bio' => $validated['bio'] ?? null,
            'preset' => $validated['preset'],
            'color' => $validated['color'],
            'background_type' => $validated['background_type'],  // ✨ 추가!
            'background_color' => $validated['background_color'],  // ✨ 추가!
            'background_secondary_color' => $validated['background_secondary_color'],  // ✨ 추가!
            'profile_image' => $validated['profile_image'] ?? $linkPage->profile_image,
        ]);

        // 기존 링크 모두 삭제
        $linkPage->links()->delete();

        // 새 링크들 저장
        foreach ($validated['links'] as $index => $linkData) {
            Link::create([
                'link_page_id' => $linkPage->id,
                'title' => $linkData['title'],
                'url' => $linkData['url'],
                'order' => $index,
            ]);
        }

        // 세션 정리
        session()->forget('verified_' . $uuid);

        return redirect()->route('linkpage.show', $uuid)
            ->with('success', '페이지가 수정되었습니다!');
    }


    // 페이지 삭제
    public function destroy($uuid)
    {
        $linkPage = LinkPage::where('uuid', $uuid)->firstOrFail();

        // 권한 확인 (로그인 사용자가 소유자이거나, 세션 인증된 경우)
        if (!$linkPage->isOwnedBy(auth()->user()) && !session('verified_' . $uuid)) {
            return redirect()->route('linkpage.edit.form', $uuid)
                ->withErrors(['error' => '삭제 권한이 없습니다.']);
        }

        // 프로필 이미지 삭제
        if ($linkPage->profile_image) {
            Storage::disk('public')->delete($linkPage->profile_image);
        }

        // 페이지 삭제 (cascade로 링크들도 자동 삭제)
        $linkPage->delete();

        // 로그인 사용자면 대시보드로, 아니면 홈으로
        if (auth()->check()) {
            return redirect()->route('dashboard')->with('success', '페이지가 삭제되었습니다.');
        }

        return redirect('/')->with('success', '페이지가 삭제되었습니다.');
    }
}
