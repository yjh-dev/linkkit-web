<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LinkPage;
use App\Models\Link;
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
            'profile_image' => 'nullable|image|max:2048', // 최대 2MB
            'links' => 'required|array|min:1',
            'links.*.title' => 'required|max:255',
            'links.*.url' => 'required|url|max:500',
        ]);

        // 프로필 이미지 저장
        $profileImagePath = null;
        if ($request->hasFile('profile_image')) {
            $profileImagePath = $request->file('profile_image')->store('profiles', 'public');
        }

        // 링크 페이지 생성 (UUID 자동 생성됨)
        $linkPage = LinkPage::create([
            'name' => $validated['name'],
            'bio' => $validated['bio'] ?? null,
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
}
