<?php

namespace App\Http\Controllers;

use App\Models\Link;
use App\Models\LinkPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class LinkPageController extends Controller
{
    // 링크 페이지 저장
    public function store(Request $request)
    {
        // 유효성 검증
        $validated = $request->validate([
            'name' => 'required|max:255',
            'bio' => 'nullable|max:500',
            'password' => 'nullable|min:4|max:20',  // nullable로 변경! (로그인 사용자는 비밀번호 선택)
            'preset' => 'required|in:basic,minimal,dark',
            'profile_image' => 'nullable|image|max:2048',
            'links' => 'required|array|min:1',
            'links.*.title' => 'required|max:255',
            'links.*.url' => 'required|url|max:500',
        ]);
        // 프로필 이미지 저장
        $profileImagePath = null;
        if ($request->hasFile('profile_image')) {
            $profileImagePath = $request->file('profile_image')->store('profiles', 'public');
        }


        // ✨ 링크 페이지 생성 (로그인 사용자 자동 연결)
        $linkPage = LinkPage::create([
            'user_id' => auth()->id(),  // 로그인 되어 있으면 자동 연결!
            'name' => $validated['name'],
            'bio' => $validated['bio'] ?? null,
            'password' => $validated['password'] ? Hash::make($validated['password']) : null,  // 비밀번호 선택적
            'preset' => $validated['preset'],
            'profile_image' => $profileImagePath,
        ]);

        // 링크들 저장
        foreach ($validated['links'] as $index => $linkData) {
            Link::create([
                'link_page_id' => $linkPage->id,
                'title' => $linkData['title'],
                'url' => $linkData['url'],
                'order' => $index,
            ]);
        }

        // 생성된 페이지로 리다이렉트
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
        // 세션 확인
        if (!session('verified_' . $uuid)) {
            return redirect()->route('linkpage.edit.form', $uuid);
        }

        $linkPage = LinkPage::where('uuid', $uuid)->firstOrFail();

        // 유효성 검증
        $validated = $request->validate([
            'name' => 'required|max:255',
            'bio' => 'nullable|max:500',
            'profile_image' => 'nullable|image|max:2048',
            'preset' => 'required|in:basic,minimal,dark',
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
            'password' => Hash::make($validated['password']),
            'preset' => $validated['preset'],
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
}
