@extends('layouts.app')

@section('title', '페이지 수정 - LinkKit')

@section('content')
    @php
        $preset = $linkPage->preset ?? 'basic';
        $color = $linkPage->color ?? '#2B7FFF';
        $bgType = $linkPage->background_type ?? 'gradient';
        $bgColor = $linkPage->background_color ?? '#EFF6FF';
        $bgSecondaryColor = $linkPage->background_secondary_color ?? '#FFFFFF';
    @endphp

    <div class="min-h-screen bg-gradient-to-br from-blue-50 to-white py-12 px-4">
        <div class="container mx-auto">
            <!-- 헤더 -->
            <div class="text-center mb-16">
                <a href="/" class="inline-flex items-center gap-2 group">
                    <span class="text-4xl font-bold text-linkkit-blue">LinkKit</span>
                    <span class="text-2xl group-hover:rotate-12 transition-transform">🔗</span>
                </a>
                <p class="text-gray-600 mt-4 text-lg">페이지 수정하기</p>

                <!-- ✨ 현재 프리셋 표시 ✨ -->
                <div
                    class="mt-6 inline-flex items-center gap-2 bg-white px-4 py-2 rounded-full shadow-md border-2 border-blue-100">
                    <span class="text-sm font-semibold text-gray-600">현재 디자인:</span>
                    @if ($preset === 'basic')
                        <span class="bg-linkkit-blue text-white px-3 py-1 rounded-full text-sm font-bold">Basic 💙</span>
                    @elseif($preset === 'minimal')
                        <span class="bg-gray-600 text-white px-3 py-1 rounded-full text-sm font-bold">Minimal ⚪</span>
                    @elseif($preset === 'dark')
                        <span class="bg-gray-900 text-white px-3 py-1 rounded-full text-sm font-bold">Dark 🌙</span>
                    @endif
                </div>
            </div>

            <!-- 메인 컨텐츠 -->
            <div class="grid lg:grid-cols-5 gap-12 max-w-7xl mx-auto">

                <!-- 좌측: 입력 폼 (3/5) -->
                <div class="lg:col-span-3">
                    <div class="bg-white rounded-3xl shadow-xl p-10 lg:p-12">
                        <div class="flex items-center gap-3 mb-10">
                            <div class="w-12 h-12 bg-linkkit-blue rounded-xl flex items-center justify-center shadow-md">
                                <span class="text-white text-xl">✏️</span>
                            </div>
                            <h2 class="text-2xl font-bold text-gray-800">정보 수정</h2>
                        </div>

                        <form id="linkPageForm" action="{{ route('linkpage.update', $linkPage->uuid) }}" method="POST"
                            enctype="multipart/form-data" class="space-y-10">
                            @csrf
                            @method('PUT')

                            <!-- ✨ 프리셋 hidden input ✨ -->
                            <input type="hidden" name="preset" value="{{ $preset }}">

                            <!-- 프로필 이미지 -->
                            <div class="text-center">
                                <label class="block text-sm font-semibold text-gray-700 mb-6">프로필 이미지</label>
                                <div class="flex flex-col items-center gap-5">
                                    <div id="profilePreview"
                                        class="w-32 h-32 bg-gradient-to-br from-blue-100 to-blue-50 rounded-full flex items-center justify-center overflow-hidden shadow-lg ring-4 ring-blue-50 hover:ring-linkkit-blue/30 transition-all cursor-pointer">
                                        @if ($linkPage->profile_image)
                                            <img src="{{ asset('storage/' . $linkPage->profile_image) }}"
                                                class="w-full h-full object-cover">
                                        @else
                                            <span class="text-blue-300 text-5xl">👤</span>
                                        @endif
                                    </div>
                                    <label class="cursor-pointer group">
                                        <div
                                            class="bg-linkkit-blue hover:bg-blue-600 text-white px-6 py-3 rounded-xl transition-all shadow-md hover:shadow-lg font-medium">
                                            📸 이미지 변경
                                        </div>
                                        <input type="file" name="profile_image" id="profile_image" accept="image/*"
                                            class="hidden">
                                    </label>
                                    <p class="text-xs text-gray-500">최대 2MB • JPG, PNG</p>
                                </div>
                            </div>

                            {{-- ✨ 메인 컬러 선택 --}}
                            <div>
                                <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-4">
                                    <span class="text-lg">🎨</span><span>메인 컬러</span>
                                </label>

                                <!-- 추천 색상 팔레트 -->
                                <div class="grid grid-cols-4 gap-3 mb-4">
                                    @foreach (config('linkkit.color_palette') as $key => $palette)
                                        <label class="cursor-pointer">
                                            <input type="radio" name="color" value="{{ $palette['value'] }}"
                                                class="peer sr-only color-radio"
                                                {{ $palette['value'] === $color ? 'checked' : '' }}>
                                            <div class="flex flex-col items-center gap-2 p-3 rounded-xl border-2 border-gray-200 peer-checked:border-4 peer-checked:border-current transition-all hover:scale-105"
                                                style="border-color: {{ $palette['value'] }}20;">
                                                <div class="w-10 h-10 rounded-full shadow-md"
                                                    style="background-color: {{ $palette['value'] }}"></div>
                                                <span class="text-xs font-medium text-gray-600">{{ $palette['emoji'] }}
                                                    {{ $palette['name'] }}</span>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>

                                <!-- 커스텀 색상 선택 -->
                                <div class="flex items-center gap-3">
                                    <label class="text-sm text-gray-600 font-medium">또는 직접 선택:</label>
                                    <div class="flex items-center gap-2">
                                        <input type="color" id="customColor"
                                            class="w-12 h-12 rounded-lg border-2 border-gray-200 cursor-pointer"
                                            value="{{ $color }}">
                                        <input type="text" id="colorValue" name="color_display"
                                            value="{{ $color }}" readonly
                                            class="px-3 py-2 border-2 border-gray-200 rounded-lg text-sm font-mono text-gray-600 w-24">
                                    </div>
                                </div>

                                <p class="text-xs text-gray-500 mt-3">💡 선택한 색상이 버튼, 링크, 강조 요소에 적용돼요!</p>
                            </div>

                            {{-- ✨ 배경 선택 --}}
                            <div>
                                <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-4">
                                    <span class="text-lg">🖼️</span><span>배경 스타일</span>
                                </label>

                                <!-- 배경 프리셋 -->
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-4">
                                    @foreach (config('linkkit.background_presets') as $key => $bg)
                                        @php $isChecked = $bgType === $bg['type'] && $bgColor === $bg['color']; @endphp
                                        <label class="cursor-pointer">
                                            <input type="radio" name="background_preset" value="{{ $key }}"
                                                class="peer sr-only background-radio" data-type="{{ $bg['type'] }}"
                                                data-color="{{ $bg['color'] }}"
                                                data-secondary="{{ $bg['secondary_color'] }}"
                                                {{ $isChecked ? 'checked' : '' }}>
                                            <div class="relative overflow-hidden rounded-xl border-2 border-gray-200 peer-checked:border-4 peer-checked:ring-2 peer-checked:ring-offset-2 transition-all hover:scale-105 h-24"
                                                style="border-color: {{ $bg['color'] }};">
                                                @if ($bg['type'] === 'gradient')
                                                    <div class="absolute inset-0"
                                                        style="background: linear-gradient(135deg, {{ $bg['color'] }}, {{ $bg['secondary_color'] }});">
                                                    </div>
                                                @else
                                                    <div class="absolute inset-0"
                                                        style="background-color: {{ $bg['color'] }};"></div>
                                                @endif
                                                <div
                                                    class="absolute inset-0 flex flex-col items-center justify-center bg-black/5">
                                                    <span class="text-2xl mb-1">{{ $bg['emoji'] }}</span>
                                                    <span
                                                        class="text-xs font-semibold text-gray-700 drop-shadow">{{ $bg['name'] }}</span>
                                                </div>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>

                                <!-- Hidden inputs -->
                                <input type="hidden" name="background_type" value="{{ $bgType }}">
                                <input type="hidden" name="background_color" value="{{ $bgColor }}">
                                <input type="hidden" name="background_secondary_color" value="{{ $bgSecondaryColor }}">

                                <!-- 커스텀 배경 (고급) -->
                                <details class="mt-4">
                                    <summary class="cursor-pointer text-sm text-gray-600 hover:text-gray-900 font-medium">
                                        ⚙️ 배경 직접 만들기 (고급)</summary>
                                    <div class="mt-4 p-4 bg-gray-50 rounded-xl space-y-4">
                                        <!-- 배경 타입 선택 -->
                                        <div>
                                            <label class="text-xs font-semibold text-gray-600 mb-2 block">배경 타입</label>
                                            <div class="flex gap-2">
                                                <label class="flex-1">
                                                    <input type="radio" name="custom_bg_type" value="solid"
                                                        class="peer sr-only" {{ $bgType === 'solid' ? 'checked' : '' }}>
                                                    <div
                                                        class="p-3 border-2 border-gray-200 rounded-lg peer-checked:border-blue-500 peer-checked:bg-blue-50 text-center cursor-pointer">
                                                        <span class="text-sm font-medium">단색</span>
                                                    </div>
                                                </label>
                                                <label class="flex-1">
                                                    <input type="radio" name="custom_bg_type" value="gradient"
                                                        class="peer sr-only"
                                                        {{ $bgType === 'gradient' ? 'checked' : '' }}>
                                                    <div
                                                        class="p-3 border-2 border-gray-200 rounded-lg peer-checked:border-blue-500 peer-checked:bg-blue-50 text-center cursor-pointer">
                                                        <span class="text-sm font-medium">그라데이션</span>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>

                                        <!-- 색상 선택 -->
                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="text-xs font-semibold text-gray-600 mb-2 block">메인 색상</label>
                                                <div class="flex items-center gap-2">
                                                    <input type="color" id="customBackgroundColor"
                                                        value="{{ $bgColor }}"
                                                        class="w-full h-12 rounded-lg border-2 border-gray-200 cursor-pointer">
                                                </div>
                                            </div>
                                            <div id="secondaryColorWrapper">
                                                <label class="text-xs font-semibold text-gray-600 mb-2 block">보조 색상</label>
                                                <div class="flex items-center gap-2">
                                                    <input type="color" id="customBackgroundSecondary"
                                                        value="{{ $bgSecondaryColor }}"
                                                        class="w-full h-12 rounded-lg border-2 border-gray-200 cursor-pointer">
                                                </div>
                                            </div>
                                        </div>

                                        <button type="button" onclick="linkKitPreview.updateCustomBackground()"
                                            class="w-full bg-gray-700 hover:bg-gray-800 text-white py-2 rounded-lg text-sm font-medium transition-all">
                                            적용하기
                                        </button>
                                    </div>
                                </details>

                                <p class="text-xs text-gray-500 mt-3">💡 배경 스타일이 페이지 전체 분위기를 결정해요!</p>
                            </div>

                            <!-- 구분선 -->
                            <div class="border-t-2 border-gray-100"></div>

                            <!-- 이름 입력 -->
                            <div>
                                <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-4">
                                    <span class="text-lg">👤</span><span>이름</span><span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="name" id="name" required maxlength="255"
                                    value="{{ $linkPage->name }}" placeholder="홍길동"
                                    class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-linkkit-blue focus:border-linkkit-blue transition-all text-lg">
                            </div>

                            <!-- 소개 입력 -->
                            <div>
                                <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-4">
                                    <span class="text-lg">💬</span><span>소개</span>
                                </label>
                                <textarea name="bio" id="bio" rows="4" maxlength="500" placeholder="안녕하세요! 저는..."
                                    class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-linkkit-blue focus:border-linkkit-blue transition-all resize-none text-base">{{ $linkPage->bio }}</textarea>
                                <div class="flex justify-between items-center mt-3">
                                    <p class="text-xs text-gray-500">간단한 자기소개를 작성해주세요</p>
                                    <span id="bioCount"
                                        class="text-xs text-gray-400">{{ strlen($linkPage->bio ?? '') }}/500</span>
                                </div>
                            </div>

                            <!-- 구분선 -->
                            <div class="border-t-2 border-gray-100"></div>

                            <!-- 링크 목록 -->
                            <div>
                                <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-6">
                                    <span class="text-lg">🔗</span><span>링크 관리</span><span class="text-red-500">*</span>
                                </label>

                                <div id="linksContainer" class="space-y-5 mb-6">
                                    @foreach ($linkPage->links as $index => $link)
                                        <div
                                            class="link-item flex items-start gap-4 p-4 bg-gray-50 rounded-xl border-2 border-gray-200 hover:border-linkkit-blue transition-all">
                                            <div
                                                class="w-10 h-10 bg-linkkit-blue rounded-lg flex items-center justify-center flex-shrink-0">
                                                <span class="text-white font-bold">{{ $index + 1 }}</span>
                                            </div>
                                            <div class="flex-1 space-y-4">
                                                <input type="text" name="links[{{ $index }}][title]"
                                                    value="{{ $link->title }}" placeholder="링크 제목" required
                                                    class="link-title w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-linkkit-blue focus:border-linkkit-blue transition-all bg-white">
                                                <input type="url" name="links[{{ $index }}][url]"
                                                    value="{{ $link->url }}" placeholder="https://" required
                                                    class="link-url w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-linkkit-blue focus:border-linkkit-blue transition-all bg-white">
                                                <input type="hidden" name="links[{{ $index }}][id]"
                                                    value="{{ $link->id }}">
                                            </div>
                                            <button type="button"
                                                class="remove-link text-gray-400 hover:text-red-500 transition-colors p-2">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>

                                <button type="button" id="addLink"
                                    class="w-full border-2 border-dashed border-gray-300 rounded-2xl py-5 text-gray-600 hover:border-linkkit-blue hover:text-linkkit-blue hover:bg-blue-50 transition-all flex items-center justify-center gap-2 font-medium group">
                                    <span class="text-xl group-hover:scale-110 transition-transform">➕</span>
                                    <span>링크 추가하기</span>
                                </button>
                            </div>

                            <!-- 제출 버튼 -->
                            <div class="pt-6">
                                <button type="submit"
                                    class="w-full bg-linkkit-blue hover:bg-blue-600 text-white py-5 rounded-2xl font-bold text-lg transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                                    <span>✨</span><span>변경사항 저장하기</span><span>💾</span>
                                </button>

                                <div class="flex items-center justify-between mt-5">
                                    <a href="{{ route('linkpage.show', $linkPage->uuid) }}"
                                        class="text-sm text-gray-500 hover:text-gray-700 transition-colors">
                                        ← 페이지로 돌아가기
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- 우측: 실시간 미리보기 (2/5) - 모바일 숨김 -->
                <div class="lg:col-span-2 hidden lg:block">
                    <div class="lg:sticky lg:top-8">
                        <div class="text-center mb-8">
                            <div
                                class="inline-flex items-center gap-2 bg-white px-5 py-3 rounded-full shadow-md border-2 border-blue-100">
                                <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                                <p class="text-sm font-semibold text-gray-700">실시간 미리보기</p>
                            </div>
                        </div>

                        <!-- 스마트폰 프레임: 원래 위치 슬롯 -->
                        <div id="previewPanelHome" class="mx-auto max-w-sm">
                            <!-- ↓ 모달로 이동하는 패널 -->
                            <div id="previewPanel">
                                <div class="relative">
                                    <!-- 그림자 효과 -->
                                    <div class="absolute inset-0 rounded-[3.5rem] blur-3xl bg-linkkit-blue/20"></div>

                                    <!-- 실제 폰 프레임 -->
                                    <div class="relative bg-gray-900 rounded-[3rem] p-3 shadow-2xl">
                                        <div
                                            class="absolute top-0 left-1/2 -translate-x-1/2 w-40 h-7 bg-gray-900 rounded-b-3xl z-10">
                                        </div>

                                        <!-- 스크린 -->
                                        <div id="previewContainer" class="rounded-[2.5rem] overflow-hidden bg-white"
                                            style="height: min(80vh, 720px);">
                                            <div class="h-full overflow-y-auto">
                                                <div class="min-h-full flex flex-col">
                                                    <!-- 프로필 영역 -->
                                                    <div class="px-8 py-12 text-center">
                                                        <div class="mb-6">
                                                            <div id="previewProfile"
                                                                class="w-32 h-32 mx-auto rounded-full flex items-center justify-center overflow-hidden shadow-2xl"
                                                                style="border: 4px solid {{ $color }}40;">
                                                                @if ($linkPage->profile_image)
                                                                    <img src="{{ asset('storage/' . $linkPage->profile_image) }}"
                                                                        class="w-full h-full object-cover">
                                                                @else
                                                                    <span class="text-5xl">👤</span>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <h1 id="previewName" class="text-3xl font-bold mb-4"
                                                            style="color: {{ $color }};">
                                                            {{ $linkPage->name }}
                                                        </h1>

                                                        <p id="previewBio"
                                                            class="text-base leading-relaxed max-w-lg mx-auto text-gray-600">
                                                            {{ $linkPage->bio ?? '소개를 입력하세요' }}
                                                        </p>
                                                    </div>

                                                    <!-- 링크 목록 -->
                                                    <div class="px-8 pb-10 flex-1">
                                                        <div id="previewLinks" class="space-y-4">
                                                            <!-- JS로 동적 업데이트 -->
                                                        </div>
                                                    </div>

                                                    <!-- 푸터 -->
                                                    <div class="px-8 py-6 text-center border-t bg-gray-50 border-gray-100"
                                                        id="previewFooter">
                                                        <p class="text-sm mb-3 text-gray-500">나만의 링크 페이지를 만들어보세요</p>
                                                        <div class="inline-flex items-center gap-2">
                                                            <span class="text-xl font-bold"
                                                                style="color: {{ $color }};">LinkKit</span>
                                                            <span>🔗</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- /스크린 -->
                                    </div>
                                </div>
                            </div>
                            <!-- /previewPanel -->
                        </div>
                        <!-- /previewPanelHome -->
                    </div>
                </div>
                <!-- /우측 프리뷰 -->
            </div>
        </div>

        <!-- 모바일 전용: 실시간 미리보기 플로팅 버튼 -->
        <button id="openPreviewFab" type="button"
            class="lg:hidden fixed bottom-5 right-5 z-40 rounded-full shadow-lg border border-blue-200 bg-linkkit-blue text-white px-5 py-4 font-semibold"
            aria-controls="previewModal" aria-expanded="false">
            실시간 미리보기
        </button>

        <!-- 미리보기 모달 (모바일: 90vw) -->
        <div id="previewModal" class="hidden fixed inset-0 z-50 lg:hidden" role="dialog" aria-modal="true"
            aria-labelledby="previewModalTitle">
            <!-- 배경 -->
            <div class="absolute inset-0 bg-black/50"></div>

            <!-- 콘텐츠 중앙 정렬 -->
            <div class="relative mx-auto h-full w-full flex items-center justify-center p-0">
                <!-- 90vw 폭, 데스크톱 진입 시에도 max-w 안전 가드 -->
                <div class="w-[90vw] max-w-sm sm:max-w-md">
                    <!-- 모달 슬롯: 프리뷰 패널 이동 위치 -->
                    <div id="previewPanelModalSlot" class="w-full mx-auto"></div>
                </div>
            </div>

            <!-- 항상 보이는 닫기 버튼: fixed + safe-area -->
            <button id="closePreviewModal" type="button"
                class="fixed z-[60] rounded-full shadow-lg bg-white/95 px-4 py-2 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-blue-400"
                style="
          top: calc(env(safe-area-inset-top, 0px) + 12px);
          right: calc(env(safe-area-inset-right, 0px) + 12px);
        "
                aria-label="닫기">
                닫기
            </button>
        </div>
    </div>

    <style>
        /* 스크롤바 커스터마이징(미리보기 내부) */
        #previewContainer .overflow-y-auto::-webkit-scrollbar {
            width: 6px;
        }

        #previewContainer .overflow-y-auto::-webkit-scrollbar-track {
            background: transparent;
        }

        #previewContainer .overflow-y-auto::-webkit-scrollbar-thumb {
            background: #e5e7eb;
            border-radius: 10px;
        }

        #previewContainer .overflow-y-auto::-webkit-scrollbar-thumb:hover {
            background: #2B7FFF;
        }

        /* 구형 iOS Safari safe-area 대체 지원(선택) */
        @supports (padding: constant(safe-area-inset-top)) {
            #closePreviewModal {
                top: calc(constant(safe-area-inset-top) + 12px);
                right: calc(constant(safe-area-inset-right) + 12px);
            }
        }
    </style>

    <!-- ✨ LinkKit 공통 JS 모듈 로드 -->
    <script src="{{ asset('js/linkkit-preview.js') }}"></script>

    <script>
        // ✨ 페이지별 설정 (기존 DB 데이터 사용)
        const preset = '{{ $preset }}';

        // ✨ LinkKit Preview 인스턴스 생성 (기존 데이터로 초기화)
        const linkKitPreview = new LinkKitPreview({
            preset: preset,
            color: '{{ $color }}',
            bgType: '{{ $bgType }}',
            bgColor: '{{ $bgColor }}',
            bgSecondaryColor: '{{ $bgSecondaryColor }}',
            linkIndex: {{ $linkPage->links->count() + 1 }}
        });

        // 기본 입력 필드 리스너 설정
        linkKitPreview.setupBasicInfoListeners();

        // 폼 검증 설정
        linkKitPreview.setupFormValidation('linkPageForm');

        // ✨ 바이오 글자수 카운터 (페이지별 특수 기능)
        const bioTextarea = document.getElementById('bio');
        const bioCount = document.getElementById('bioCount');

        if (bioTextarea && bioCount) {
            bioTextarea.addEventListener('input', function() {
                bioCount.textContent = `${this.value.length}/500`;
            });
        }

        // ✨ 커스텀 배경 타입 변경 처리
        document.querySelectorAll('input[name="custom_bg_type"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const secondaryWrapper = document.getElementById('secondaryColorWrapper');
                if (this.value === 'solid') {
                    secondaryWrapper.style.opacity = '0.5';
                    secondaryWrapper.style.pointerEvents = 'none';
                } else {
                    secondaryWrapper.style.opacity = '1';
                    secondaryWrapper.style.pointerEvents = 'auto';
                }
            });
        });

        // ✨ 페이지 로드 시 초기 미리보기 렌더링
        linkKitPreview.updateAllPreviews();
    </script>
@endsection
