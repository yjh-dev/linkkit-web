<?php

namespace App\Http\Controllers;

use App\Models\Link;
use App\Models\LinkPage;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LinkPageController extends Controller
{
    /**
     * 페이지 생성 폼
     */
    public function create()
    {
        $config = config('linkkit');


        return view('linkkit.create', compact('config'));
    }

    /**
     * 페이지 저장
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            // 추가
            'text_color' => 'nullable|string|max:7',
            'text_size' => 'nullable|string|max:20',
            'text_weight' => 'nullable|string|max:20',
            'cover_bg_color' => 'nullable|string|max:7',
            'background_video_url' => 'nullable|string|max:500',
            'background_video_file' => 'nullable|string|max:500',

            // 기본 정보
            'name' => 'required|string|max:100',
            'bio' => 'nullable|string|max:500',
            'password' => 'nullable|string|min:4|max:20',
            'profile_image' => 'nullable|image|max:5120', // 5MB

            // Phase 1: 비주얼
            'background_type' => 'required|in:solid,gradient,image,video,animated',
            'background_color' => 'required|string|max:7',
            'background_secondary_color' => 'nullable|string|max:7',
            'background_image' => 'nullable|image|max:10240', // 10MB
            'background_blur' => 'nullable|integer|min:0|max:100',
            'background_brightness' => 'nullable|integer|min:0|max:200',
            'background_overlay' => 'nullable|in:none,dots,stripes,grid,waves',
            'profile_layout' => 'required|in:large,small,banner',
            'cover_image' => 'nullable|image|max:5120',
            'badges' => 'nullable|array',
            'animation_entrance' => 'required|in:none,fade,slide,bounce,zoom',
            'animation_speed' => 'required|in:slow,normal,fast',

            // 링크
            'links' => 'required|array|min:1',
            'links.*.title' => 'required|string|max:100',
            'links.*.url' => 'required|url|max:500',
            'links.*.type' => 'nullable|in:button,product,image_card,embed,icon,text,contact,file',
            'links.*.thumbnail' => 'nullable|image|max:5120',
            'links.*.description' => 'nullable|string|max:500',
            'links.*.price' => 'nullable|numeric|min:0',
            'links.*.sale_price' => 'nullable|numeric|min:0',
            'links.*.button_style' => 'nullable|in:rounded,pill,sharp,soft',
            'links.*.button_size' => 'nullable|in:small,medium,large',
            'links.*.button_color' => 'nullable|string|max:7',
            'links.*.hover_effect' => 'nullable|in:none,scale,lift,glow,wiggle,pulse',


            'cover_image' => 'nullable|image|max:5120',
            'banner_radius' => 'nullable|integer|min:0|max:50',      // ✅ 추가
            'banner_height' => 'nullable|integer|min:80|max:250',    // ✅ 추가
        ]);

        // 프로필 이미지 업로드
        if ($request->hasFile('profile_image')) {
            $validated['profile_image'] = $request->file('profile_image')
                ->store('profiles', 'public');
        }

        // 배경 이미지 업로드
        if ($request->hasFile('background_image')) {
            $validated['background_image'] = $request->file('background_image')
                ->store('backgrounds', 'public');
        }

        // 커버 이미지 업로드
        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')
                ->store('covers', 'public');
        }

        if ($request->hasFile('background_video_file')) {
            $validated['background_video_file'] = $request->file('background_video_file')
                ->store('videos', 'public');
        }

        // 로그인 사용자면 user_id 설정
        if (auth()->check()) {
            $validated['user_id'] = auth()->id();
        }

        // UUID는 Model에서 자동 생성
        $linkPage = LinkPage::create($validated);

        // 링크 저장
        foreach ($validated['links'] as $index => $linkData) {
            $linkData['order'] = $index;
            $linkData['link_page_id'] = $linkPage->id;

            // 썸네일 업로드
            if (isset($linkData['thumbnail']) && $linkData['thumbnail'] instanceof \Illuminate\Http\UploadedFile) {
                $linkData['thumbnail'] = $linkData['thumbnail']->store('thumbnails', 'public');
            }

            Link::create($linkData);
        }

        return redirect()->route('linkpage.show', $linkPage->uuid)
            ->with('success', '링크페이지가 생성되었습니다!');
    }

    /**
     * 페이지 보기
     */
    public function show($uuid)
    {
        $linkPage = LinkPage::with('activeLinks')->where('uuid', $uuid)->firstOrFail();

        // 비공개 페이지 체크
        if (!$linkPage->is_public && !$this->canViewPage($linkPage)) {
            abort(403, '비공개 페이지입니다.');
        }

        // 성인 콘텐츠 경고
        if ($linkPage->adult_content && !session()->has('adult_verified_' . $linkPage->id)) {
            return view('linkkit.adult-warning', compact('linkPage'));
        }

        // 페이지뷰 기록 (Phase 4)
        $this->recordPageView($linkPage);

        return view('linkkit.show', compact('linkPage'));
    }

    /**
     * 페이지 수정 폼
     */
    public function edit($uuid, Request $request)
    {
        $linkPage = LinkPage::with('links')->where('uuid', $uuid)->firstOrFail();

        // 수정 권한 확인
        if (!$linkPage->canEdit(auth()->user(), $request->password)) {
            if ($linkPage->user_id) {
                // 회원 페이지
                return redirect()->route('login')
                    ->with('error', '로그인이 필요합니다.');
            } else {
                // 비회원 페이지 - 비밀번호 필요
                return view('linkkit.password', compact('linkPage'));
            }
        }

        $config = config('linkkit');

        return view('linkkit.edit', compact('linkPage', 'config'));
    }

    /**
     * 페이지 업데이트
     */
    public function update(Request $request, $uuid)
    {
        $linkPage = LinkPage::where('uuid', $uuid)->firstOrFail();

        // 수정 권한 확인
        if (!$linkPage->canEdit(auth()->user(), $request->password)) {
            return redirect()->back()
                ->with('error', '수정 권한이 없습니다.');
        }

        $validated = $request->validate([
            // 추가
            'text_color' => 'nullable|string|max:7',
            'text_size' => 'nullable|in:small,medium,large',
            'text_weight' => 'nullable|in:normal,medium,semibold,bold,extrabold',
            'cover_bg_color' => 'nullable|string|max:7',
            'background_video_url' => 'nullable|url|max:500',
            'background_video_file' => 'nullable|file|mimes:mp4,webm|max:51200',

            // 기본 정보
            'name' => 'required|string|max:100',
            'bio' => 'nullable|string|max:500',
            'profile_image' => 'nullable|image|max:5120',

            // Phase 1: 비주얼
            'background_type' => 'required|in:solid,gradient,image,video,animated',
            'background_color' => 'required|string|max:7',
            'background_secondary_color' => 'nullable|string|max:7',
            'background_image' => 'nullable|image|max:10240',
            'background_blur' => 'nullable|integer|min:0|max:100',
            'background_brightness' => 'nullable|integer|min:0|max:200',
            'background_overlay' => 'nullable|in:none,dots,stripes,grid,waves',
            'profile_layout' => 'required|in:large,small,banner',
            'cover_image' => 'nullable|image|max:5120',
            'badges' => 'nullable|array',
            'animation_entrance' => 'required|in:none,fade,slide,bounce,zoom',
            'animation_speed' => 'required|in:slow,normal,fast',

            // 링크
            'links' => 'required|array|min:1',
            'links.*.id' => 'nullable|exists:links,id',
            'links.*.title' => 'required|string|max:100',
            'links.*.url' => 'required|url|max:500',
            'links.*.type' => 'nullable|in:button,product,image_card,embed,icon,text,contact,file',
            'links.*.thumbnail' => 'nullable|image|max:5120',
            'links.*.description' => 'nullable|string|max:500',
            'links.*.price' => 'nullable|numeric|min:0',
            'links.*.sale_price' => 'nullable|numeric|min:0',
            'links.*.button_style' => 'nullable|in:rounded,pill,sharp,soft',
            'links.*.button_size' => 'nullable|in:small,medium,large',
            'links.*.button_color' => 'nullable|string|max:7',
            'links.*.hover_effect' => 'nullable|in:none,scale,lift,glow,wiggle,pulse',


            'cover_image' => 'nullable|image|max:5120',
            'banner_radius' => 'nullable|integer|min:0|max:50',      // ✅ 추가
            'banner_height' => 'nullable|integer|min:80|max:250',    // ✅ 추가
        ]);

        // 이미지 업로드 처리
        if ($request->hasFile('profile_image')) {
            // 기존 이미지 삭제
            if ($linkPage->profile_image) {
                Storage::disk('public')->delete($linkPage->profile_image);
            }
            $validated['profile_image'] = $request->file('profile_image')
                ->store('profiles', 'public');
        }

        if ($request->hasFile('background_image')) {
            if ($linkPage->background_image) {
                Storage::disk('public')->delete($linkPage->background_image);
            }
            $validated['background_image'] = $request->file('background_image')
                ->store('backgrounds', 'public');
        }

        if ($request->hasFile('cover_image')) {
            if ($linkPage->cover_image) {
                Storage::disk('public')->delete($linkPage->cover_image);
            }
            $validated['cover_image'] = $request->file('cover_image')
                ->store('covers', 'public');
        }

        $linkPage->update($validated);

        // 링크 업데이트
        $existingLinkIds = [];

        foreach ($validated['links'] as $index => $linkData) {
            $linkData['order'] = $index;

            // 썸네일 업로드
            if (isset($linkData['thumbnail']) && $linkData['thumbnail'] instanceof \Illuminate\Http\UploadedFile) {
                $linkData['thumbnail'] = $linkData['thumbnail']->store('thumbnails', 'public');
            }

            if (isset($linkData['id'])) {
                // 기존 링크 업데이트
                $link = Link::find($linkData['id']);
                if ($link && $link->link_page_id === $linkPage->id) {
                    $link->update($linkData);
                    $existingLinkIds[] = $link->id;
                }
            } else {
                // 새 링크 생성
                $linkData['link_page_id'] = $linkPage->id;
                $link = Link::create($linkData);
                $existingLinkIds[] = $link->id;
            }
        }

        // 제거된 링크 삭제
        Link::where('link_page_id', $linkPage->id)
            ->whereNotIn('id', $existingLinkIds)
            ->delete();

        return redirect()->route('linkpage.show', $linkPage->uuid)
            ->with('success', '페이지가 업데이트되었습니다!');
    }

    /**
     * 페이지 삭제
     */
    public function destroy($uuid, Request $request)
    {
        $linkPage = LinkPage::where('uuid', $uuid)->firstOrFail();

        // 삭제 권한 확인
        if (!$linkPage->canEdit(auth()->user(), $request->password)) {
            return redirect()->back()
                ->with('error', '삭제 권한이 없습니다.');
        }

        // 이미지 파일들 삭제
        if ($linkPage->profile_image) {
            Storage::disk('public')->delete($linkPage->profile_image);
        }
        if ($linkPage->background_image) {
            Storage::disk('public')->delete($linkPage->background_image);
        }
        if ($linkPage->cover_image) {
            Storage::disk('public')->delete($linkPage->cover_image);
        }

        $linkPage->delete();

        if (auth()->check()) {
            return redirect()->route('dashboard')
                ->with('success', '페이지가 삭제되었습니다.');
        }

        return redirect()->route('home')
            ->with('success', '페이지가 삭제되었습니다.');
    }

    /**
     * 링크 클릭 추적
     */
    public function trackClick($uuid, $linkId)
    {
        $linkPage = LinkPage::where('uuid', $uuid)->firstOrFail();
        $link = Link::where('id', $linkId)
            ->where('link_page_id', $linkPage->id)
            ->firstOrFail();

        // 활성 상태 확인
        if (!$link->isAvailable()) {
            abort(404);
        }

        // 비밀번호 보호 확인
        if ($link->password_protected) {
            $password = session('link_password_' . $link->id);
            if (!$link->checkPassword($password)) {
                return redirect()->route('linkpage.show', $uuid)
                    ->with('error', '비밀번호가 필요한 링크입니다.');
            }
        }

        // 클릭 수 증가
        $sessionKey = 'link_clicked_' . $link->id;
        $isUnique = !session()->has($sessionKey);

        $link->incrementClicks($isUnique);
        session()->put($sessionKey, true);

        // Phase 5: 상세 로그 기록
        $this->recordClickLog($link);

        return redirect($link->url);
    }

    /**
     * Helper: 페이지 조회 가능 여부
     */
    private function canViewPage($linkPage)
    {
        // 소유자는 항상 볼 수 있음
        if (auth()->check() && auth()->id() === $linkPage->user_id) {
            return true;
        }

        // 비밀번호 확인
        if ($linkPage->password) {
            $password = session('page_password_' . $linkPage->id);
            return $linkPage->checkPassword($password);
        }

        return false;
    }

    /**
     * Helper: 페이지뷰 기록 (Phase 4)
     */
    private function recordPageView($linkPage)
    {
        // TODO: Phase 4에서 구현
        // PageView::create([...]);
    }

    /**
     * Helper: 클릭 로그 기록 (Phase 5)
     */
    private function recordClickLog($link)
    {
        // TODO: Phase 5에서 구현
        // LinkClickLog::create([...]);
    }
}
