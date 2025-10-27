@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-7xl mx-auto">
            <!-- 헤더 -->
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-gray-900 mb-2">내 링크페이지 만들기</h1>
                <p class="text-gray-600">모든 링크를 하나로 모아보세요</p>
            </div>

            <form id="createForm" action="{{ route('linkpage.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- 좌측: 설정 폼 -->
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-2xl shadow-lg p-6">
                            <!-- 탭 네비게이션 (수정!) -->
                            <div class="flex border-b mb-6 overflow-x-auto">
                                <button type="button" class="tab-button active" data-tab="basic">
                                    📝 기본정보
                                </button>
                                <button type="button" class="tab-button" data-tab="design">
                                    🎨 디자인
                                </button>
                                <button type="button" class="tab-button" data-tab="links">
                                    🔗 링크관리
                                </button>
                            </div>

                            <!-- 탭 1: 기본 정보 -->
                            <div class="tab-content active" data-content="basic">
                                <h2 class="text-2xl font-bold mb-6">기본 정보</h2>

                                <!-- 이름 -->
                                <div class="mb-6">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        이름 <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="name" id="name" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        placeholder="홍길동">
                                </div>

                                <!-- 소개 -->
                                <div class="mb-6">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        소개
                                    </label>
                                    <textarea name="bio" id="bio" rows="3"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        placeholder="자기소개를 입력하세요"></textarea>
                                </div>

                                <!-- 프로필 이미지 -->
                                <div class="mb-6">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        프로필 이미지
                                    </label>
                                    <div class="flex items-center gap-4">
                                        <div class="flex-shrink-0">
                                            <img id="profileImagePreview" src="{{ asset('images/linkkit-logo.png') }}"
                                                alt="미리보기"
                                                class="w-20 h-20 rounded-full object-cover border-2 border-gray-200">
                                        </div>
                                        <div class="flex-1">
                                            <input type="file" name="profile_image" id="profile_image" accept="image/*"
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                                            <p class="text-xs text-gray-500 mt-1">권장 크기: 400x400px, 최대 5MB</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- 비밀번호 (비회원용) -->
                                @guest
                                    <div class="mb-6">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            수정 비밀번호 (선택)
                                        </label>
                                        <input type="password" name="password" id="password"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg"
                                            placeholder="나중에 수정하려면 비밀번호를 설정하세요">
                                        <p class="text-sm text-gray-500 mt-1">비밀번호를 설정하지 않으면 수정할 수 없습니다</p>
                                    </div>
                                @endguest
                            </div>

                            <!-- 탭 2: 디자인 -->
                            <div class="tab-content" data-content="design">
                                <h2 class="text-2xl font-bold mb-6">디자인 설정</h2>

                                <!-- 프로필 레이아웃 -->
                                <div class="mb-8">
                                    <label class="block text-sm font-medium text-gray-700 mb-3">
                                        프로필 레이아웃
                                    </label>
                                    <div class="grid grid-cols-3 gap-4">
                                        @foreach ($config['profile_layouts'] as $key => $layout)
                                            <label class="cursor-pointer">
                                                <input type="radio" name="profile_layout" value="{{ $key }}"
                                                    {{ $key === 'large' ? 'checked' : '' }} class="hidden peer">
                                                <div
                                                    class="p-4 border-2 border-gray-200 rounded-lg peer-checked:border-blue-600 peer-checked:bg-blue-50 hover:border-blue-300 transition">
                                                    <div class="text-center mb-2">
                                                        <span class="text-3xl">
                                                            @if ($key === 'large')
                                                                👤
                                                            @elseif($key === 'small')
                                                                💼
                                                            @else
                                                                🖼️
                                                            @endif
                                                        </span>
                                                    </div>
                                                    <p class="text-sm font-medium text-gray-900">{{ $layout['name'] }}</p>
                                                    <p class="text-xs text-gray-500 mt-1">{{ $layout['description'] }}</p>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>

                                <div id="bannerCustomizationSection" style="display: none;"
                                    class="mb-8 p-4 bg-purple-50 rounded-lg border border-purple-200">
                                    <h3 class="text-sm font-bold text-gray-900 mb-4">🎨 배너 커스터마이징</h3>

                                    <div class="space-y-4">
                                        <!-- 배너 라운드 (Radius) -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                배너 모서리 둥글기: <span id="bannerRadiusValue" class="text-blue-600">24</span>px
                                            </label>
                                            <input type="range" name="banner_radius" id="banner_radius" min="0"
                                                max="50" value="24"
                                                class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer slider-thumb">
                                            <div class="flex justify-between text-xs text-gray-500 mt-1">
                                                <span>직각 (0)</span>
                                                <span>약간 둥글게 (24)</span>
                                                <span>매우 둥글게 (50)</span>
                                            </div>
                                        </div>

                                        <!-- 배너 높이 -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                배너 높이: <span id="bannerHeightValue" class="text-blue-600">128</span>px
                                            </label>
                                            <input type="range" name="banner_height" id="banner_height" min="80"
                                                max="250" value="128" step="10"
                                                class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer slider-thumb">
                                            <div class="flex justify-between text-xs text-gray-500 mt-1">
                                                <span>낮게 (80px)</span>
                                                <span>보통 (128px)</span>
                                                <span>높게 (250px)</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>



                                <!-- 커버 이미지/배경 (수정!) -->
                                <div class="mb-8" id="coverImageSection" style="display: none;">
                                    <label class="block text-sm font-medium text-gray-700 mb-3">
                                        배너 배경
                                    </label>

                                    <!-- 배너 배경색 (신규!) -->
                                    <div class="mb-4">
                                        <label class="block text-xs text-gray-600 mb-2">배너 배경색 (이미지 없을 때)</label>
                                        <input type="color" id="cover_bg_color" name="cover_bg_color" value="#3B82F6"
                                            class="w-20 h-10 rounded cursor-pointer border border-gray-300">
                                    </div>

                                    <!-- 배너 이미지 -->
                                    <div>
                                        <label class="block text-xs text-gray-600 mb-2">배너 이미지 (선택)</label>
                                        <input type="file" name="cover_image" id="cover_image" accept="image/*"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                                        <p class="text-xs text-gray-500 mt-1">권장 크기: 1200x400px, 최대 5MB</p>
                                    </div>
                                </div>

                                <!-- 텍스트 스타일 (신규!) -->
                                <div class="mb-8 p-4 bg-blue-50 rounded-lg border border-blue-200">
                                    <h3 class="text-sm font-bold text-gray-900 mb-4">📝 텍스트 스타일</h3>

                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <!-- 텍스트 색상 -->
                                        <div>
                                            <label class="block text-xs text-gray-600 mb-2">텍스트 색상</label>
                                            <input type="color" id="text_color" name="text_color" value="#FFFFFF"
                                                class="w-full h-10 rounded cursor-pointer border border-gray-300">
                                        </div>

                                        <!-- 텍스트 크기 -->
                                        <div>
                                            <label class="block text-xs text-gray-600 mb-2">텍스트 크기</label>
                                            <select id="text_size" name="text_size"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                                <option value="small">작게</option>
                                                <option value="medium" selected>보통</option>
                                                <option value="large">크게</option>
                                            </select>
                                        </div>

                                        <!-- 텍스트 두께 -->
                                        <div>
                                            <label class="block text-xs text-gray-600 mb-2">텍스트 두께</label>
                                            <select id="text_weight" name="text_weight"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                                <option value="normal">보통</option>
                                                <option value="medium">중간</option>
                                                <option value="semibold">세미볼드</option>
                                                <option value="bold" selected>볼드</option>
                                                <option value="extrabold">엑스트라볼드</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- 배경 타입 -->
                                <div class="mb-8">
                                    <label class="block text-sm font-medium text-gray-700 mb-3">
                                        배경 타입
                                    </label>
                                    <div class="grid grid-cols-3 gap-3">
                                        @foreach ($config['background_types'] as $key => $label)
                                            <label class="cursor-pointer">
                                                <input type="radio" name="background_type" value="{{ $key }}"
                                                    {{ $key === 'solid' ? 'checked' : '' }} class="hidden peer">
                                                <div
                                                    class="p-3 border-2 border-gray-200 rounded-lg peer-checked:border-blue-600 peer-checked:bg-blue-50 text-center hover:border-blue-300 transition">
                                                    <p class="font-medium">{{ $label }}</p>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- 배경 색상 -->
                                <div class="mb-6" id="backgroundColorSection">
                                    <label class="block text-sm font-medium text-gray-700 mb-3">
                                        배경 색상
                                    </label>
                                    <div class="grid grid-cols-4 gap-3 mb-4">
                                        @foreach ($config['colors'] as $key => $color)
                                            <label class="color-option">
                                                <input type="radio" name="background_color" value="{{ $color }}"
                                                    {{ $key === 'blue' ? 'checked' : '' }} class="hidden">
                                                <div class="w-full h-12 rounded-lg border-2 border-gray-200 transition-all hover:scale-105"
                                                    style="background-color: {{ $color }}"></div>
                                            </label>
                                        @endforeach
                                    </div>

                                    <!-- 커스텀 색상 선택 -->
                                    <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                                        <label class="flex items-center gap-3">
                                            <span class="text-sm font-medium text-gray-700">커스텀 색상:</span>
                                            <input type="color" id="customBackgroundColor" value="#2B7FFF"
                                                class="w-16 h-10 rounded cursor-pointer border border-gray-300">
                                            <span class="text-xs text-gray-500">원하는 색상을 직접 선택하세요</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- 그라데이션 보조 색상 -->
                                <div class="mb-6" id="gradientSecondarySection" style="display: none;">
                                    <label class="block text-sm font-medium text-gray-700 mb-3">
                                        그라데이션 보조 색상
                                    </label>
                                    <div class="flex items-center gap-3">
                                        <input type="color" name="background_secondary_color" value="#9333EA"
                                            class="w-16 h-10 rounded cursor-pointer border border-gray-300">
                                        <span class="text-sm text-gray-600">그라데이션의 끝 색상</span>
                                    </div>
                                </div>

                                <!-- 배경 이미지 업로드 -->
                                <div class="mb-6" id="backgroundImageSection" style="display: none;">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        배경 이미지
                                    </label>
                                    <input type="file" name="background_image" id="background_image" accept="image/*"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                                    <p class="text-xs text-gray-500 mt-1">권장 크기: 1080x1920px, 최대 10MB</p>
                                </div>

                                <!-- 배경 영상 (신규!) -->
                                <div class="mb-6" id="backgroundVideoSection" style="display: none;">
                                    <label class="block text-sm font-medium text-gray-700 mb-3">
                                        배경 영상
                                    </label>

                                    <!-- 영상 URL -->
                                    <div class="mb-3">
                                        <label class="block text-xs text-gray-600 mb-2">영상 URL</label>
                                        <input type="url" id="background_video_url" name="background_video_url"
                                            placeholder="https://example.com/video.mp4"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                                        <p class="text-xs text-gray-500 mt-1">MP4 형식의 직접 링크를 입력하세요</p>
                                    </div>

                                    <!-- 또는 -->
                                    <div class="text-center text-sm text-gray-500 my-3">또는</div>

                                    <!-- 영상 파일 업로드 -->
                                    <div>
                                        <label class="block text-xs text-gray-600 mb-2">영상 파일 업로드</label>
                                        <input type="file" id="background_video_file" name="background_video_file"
                                            accept="video/mp4,video/webm"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                                        <p class="text-xs text-gray-500 mt-1">MP4 또는 WebM 형식, 최대 50MB</p>
                                    </div>

                                    <div class="mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                        <p class="text-xs text-yellow-800">⚠️ 영상은 무음 자동재생되며, 모바일에서는 성능을 위해 이미지로 대체될 수 있습니다.
                                        </p>
                                    </div>
                                </div>

                                <!-- 배경 블러 -->
                                <div class="mb-6" id="backgroundBlurSection" style="display: none;">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        배경 블러: <span id="blurValue" class="text-blue-600">0</span>
                                    </label>
                                    <input type="range" name="background_blur" id="background_blur" min="0"
                                        max="100" value="0"
                                        class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                                </div>

                                <!-- 배경 밝기 -->
                                <div class="mb-6" id="backgroundBrightnessSection" style="display: none;">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        배경 밝기: <span id="brightnessValue" class="text-blue-600">100</span>%
                                    </label>
                                    <input type="range" name="background_brightness" id="background_brightness"
                                        min="0" max="200" value="100"
                                        class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                                </div>

                                <!-- 애니메이션 -->
                                <div class="mb-6">
                                    <label class="block text-sm font-medium text-gray-700 mb-3">
                                        등장 애니메이션
                                    </label>
                                    <select name="animation_entrance" id="animation_entrance"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        @foreach ($config['animations']['entrance'] as $key => $label)
                                            <option value="{{ $key }}" {{ $key === 'fade' ? 'selected' : '' }}>
                                                {{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-6">
                                    <label class="block text-sm font-medium text-gray-700 mb-3">
                                        애니메이션 속도
                                    </label>
                                    <select name="animation_speed" id="animation_speed"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        @foreach ($config['animations']['speed'] as $key => $label)
                                            <option value="{{ $key }}"
                                                {{ $key === 'normal' ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- 폰트 설정 -->
                                <div class="mb-8 p-4 bg-indigo-50 rounded-lg border border-indigo-200">
                                    <h3 class="text-sm font-bold text-gray-900 mb-4">🔤 폰트 설정</h3>

                                    <!-- Google Fonts 프리셋 -->
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            폰트 선택
                                        </label>
                                        <select name="font_family" id="font_family"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                            <option value="Pretendard" selected>Pretendard (기본, 한글)</option>
                                            <option value="Noto Sans KR">Noto Sans KR (깔끔한 고딕)</option>
                                            <option value="Nanum Gothic">나눔고딕</option>
                                            <option value="Nanum Myeongjo">나눔명조</option>
                                            <option value="Gowun Batang">고운바탕</option>
                                            <option value="Roboto">Roboto (영문)</option>
                                            <option value="Open Sans">Open Sans (영문)</option>
                                            <option value="Lato">Lato (영문)</option>
                                            <option value="Montserrat">Montserrat (영문)</option>
                                            <option value="Poppins">Poppins (영문)</option>
                                            <option value="custom">커스텀 폰트 (직접 업로드)</option>
                                        </select>
                                        <p class="text-xs text-gray-500 mt-1">페이지 전체에 적용됩니다</p>
                                    </div>

                                    <!-- 커스텀 폰트 업로드 -->
                                    <div id="customFontUpload" style="display: none;">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            커스텀 폰트 파일
                                        </label>
                                        <input type="file" name="custom_font" id="custom_font"
                                            accept=".ttf,.woff,.woff2,.otf"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                                        <p class="text-xs text-gray-500 mt-1">지원 형식: .ttf, .woff, .woff2, .otf (최대 5MB)</p>
                                    </div>

                                    <!-- 폰트 미리보기 -->
                                    <div class="mt-4 p-4 bg-white rounded-lg border border-gray-200">
                                        <p class="text-xs text-gray-500 mb-2">폰트 미리보기</p>
                                        <div id="fontPreview" style="transition: font-family 0.3s;">
                                            <p class="text-2xl font-bold mb-2">안녕하세요 Hello!</p>
                                            <p class="text-base">가나다라마바사 ABCDEFG</p>
                                            <p class="text-sm text-gray-600">1234567890 !@#$%</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 탭 3: 링크 관리 -->
                            <div class="tab-content" data-content="links">
                                <div class="flex justify-between items-center mb-6">
                                    <h2 class="text-2xl font-bold">링크 관리</h2>
                                    <button type="button" id="addLinkBtn"
                                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                        + 링크 추가
                                    </button>
                                </div>

                                <div id="linksContainer">
                                    <!-- 링크 아이템들이 여기에 동적으로 추가됩니다 -->
                                </div>

                                <p class="text-sm text-gray-500 mt-4 text-center">
                                    최소 1개 이상의 링크를 추가해주세요
                                </p>
                            </div>
                        </div>

                        <!-- 제출 버튼 -->
                        <div class="mt-6 flex gap-4">
                            <button type="submit"
                                class="flex-1 px-6 py-4 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 transition shadow-lg hover:shadow-xl">
                                🚀 페이지 생성하기
                            </button>
                        </div>
                    </div>

                    <!-- 우측: 실시간 미리보기 (데스크톱만) -->
                    <div class="lg:col-span-1 desktop-preview">
                        <div class="sticky top-8">
                            <h3 class="text-lg font-bold text-gray-900 mb-4 text-center">실시간 미리보기</h3>
                            <div class="phone-mockup mx-auto overflow-hidden">
                                <div id="preview" class="w-full h-full overflow-y-auto"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- 모바일 미리보기 플로팅 버튼 (신규!) -->
    <button type="button" id="mobilePreviewBtn" class="mobile-preview-btn">
        👁️
    </button>

    <!-- 모바일 미리보기 모달 (신규!) -->
    <div id="mobilePreviewModal" class="preview-modal hidden">
        <div class="relative w-full max-w-sm">
            <button type="button" id="closePreview"
                class="absolute -top-12 right-0 text-white text-2xl hover:text-gray-300">
                ✕
            </button>
            <div class="phone-mockup mx-auto overflow-hidden">
                <div id="mobilePreview" class="w-full h-full overflow-y-auto"></div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/linkkit-create.js') }}"></script>
@endsection
