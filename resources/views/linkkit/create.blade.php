@extends('layouts.app')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

    <div class="container mx-auto px-4 py-8 max-w-7xl">
        <!-- 헤더 -->
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900">나만의 링크 페이지 만들기</h1>
        </div>

        <!-- 임시 저장 알림 -->
        <div id="draftNotification" class="hidden bg-blue-50 border-l-4 border-blue-400 p-4 rounded-lg mb-6 shadow-sm">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <span class="text-2xl">💾</span>
                    <div>
                        <p class="text-sm font-medium text-blue-900">저장된 작업이 있습니다</p>
                        <p class="text-xs text-blue-700" id="draftTime"></p>
                    </div>
                </div>
                <div class="flex gap-2 w-full sm:w-auto">
                    <button type="button" onclick="loadDraft()"
                        class="flex-1 sm:flex-none px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                        불러오기
                    </button>
                    <button type="button" onclick="dismissDraft()"
                        class="flex-1 sm:flex-none px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-300 transition">
                        삭제
                    </button>
                </div>
            </div>
        </div>

        <!-- 자동 저장 상태 표시 -->
        <div id="autoSaveStatus"
            class="hidden fixed top-4 right-4 bg-white border border-gray-200 px-4 py-2 rounded-lg shadow-lg z-50">
            <span class="text-sm text-gray-600">💾 저장 중...</span>
        </div>

        <form id="createForm" action="{{ route('linkpage.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- 좌측: 설정 폼 -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-lg p-4 md:p-6">
                        <!-- 탭 네비게이션 -->
                        <div class="flex border-b mb-6 overflow-x-auto scrollbar-hide">
                            <button type="button" class="tab-button active whitespace-nowrap" data-tab="basic">
                                📝 기본정보
                            </button>
                            <button type="button" class="tab-button whitespace-nowrap" data-tab="design">
                                🎨 디자인
                            </button>
                            <button type="button" class="tab-button whitespace-nowrap" data-tab="links">
                                🔗 링크관리
                            </button>
                        </div>

                        <!-- 탭 1: 기본 정보 (텍스트 스타일 추가) -->
                        <div id="basic" class="tab-content active">
                            <h2 class="text-xl md:text-2xl font-bold mb-6">기본 정보</h2>

                            <!-- 이름 -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    이름 <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="name" id="name" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="홍길동">

                                <!-- 이름 색상 (이슈 #2 해결) -->
                                <div class="mt-2">
                                    <label class="block text-xs text-gray-600 mb-1">이름 색상</label>
                                    <input type="color" id="name_color" name="name_color" value="#FFFFFF"
                                        class="w-20 h-10 rounded cursor-pointer border border-gray-300">
                                </div>
                            </div>

                            <!-- 소개 -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    소개
                                </label>
                                <textarea name="bio" id="bio" rows="3"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="자기소개를 입력하세요"></textarea>

                                <!-- 소개 색상 (이슈 #2 해결) -->
                                <div class="mt-2">
                                    <label class="block text-xs text-gray-600 mb-1">소개 색상</label>
                                    <input type="color" id="bio_color" name="bio_color" value="#FFFFFF"
                                        class="w-20 h-10 rounded cursor-pointer border border-gray-300">
                                </div>
                            </div>

                            <!-- 텍스트 크기 & 굵기 (이슈 #2 해결 - 기본정보로 이동) -->
                            <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                                <h3 class="text-sm font-bold text-gray-900 mb-4">📝 텍스트 스타일</h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs text-gray-600 mb-2">텍스트 크기</label>
                                        <select id="text_size" name="text_size"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                            <option value="small">작게</option>
                                            <option value="medium" selected>보통</option>
                                            <option value="large">크게</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-xs text-gray-600 mb-2">텍스트 두께</label>
                                        <select id="text_weight" name="text_weight"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                            <option value="300">가벼움 (Light)</option>
                                            <option value="400">보통 (Regular)</option>
                                            <option value="500">중간 (Medium)</option>
                                            <option value="600">세미볼드</option>
                                            <option value="700" selected>볼드</option>
                                            <option value="800">엑스트라볼드</option>
                                            <option value="900">블랙 (Black)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- 프로필 이미지 -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    프로필 이미지
                                </label>
                                <div class="flex flex-col sm:flex-row items-center gap-4">
                                    <div class="flex-shrink-0">
                                        <img id="profileImagePreview" src="{{ asset('images/linkkit-logo.png') }}"
                                            alt="미리보기"
                                            class="w-20 h-20 rounded-full object-cover border-2 border-gray-200">
                                    </div>
                                    <div class="flex-1 w-full">
                                        <input type="file" name="profile_image" id="profile_image" accept="image/*"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm">
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


                        <!-- 탭 2: 디자인 (텍스트 스타일 제거됨) -->
                        <div id="design" class="tab-content">
                            <h2 class="text-xl md:text-2xl font-bold mb-6">디자인 설정</h2>

                            <!-- 프로필 레이아웃 -->
                            <div class="mb-8">
                                <label class="block text-sm font-medium text-gray-700 mb-3">
                                    프로필 레이아웃
                                </label>
                                <div class="grid grid-cols-3 gap-3 md:gap-4">
                                    @foreach ($config['profile_layouts'] as $key => $layout)
                                        <label class="cursor-pointer">
                                            <input type="radio" name="profile_layout" value="{{ $key }}"
                                                {{ $key === 'large' ? 'checked' : '' }} class="hidden peer">
                                            <div
                                                class="p-3 md:p-4 border-2 border-gray-200 rounded-lg peer-checked:border-blue-600 peer-checked:bg-blue-50 hover:border-blue-300 transition">
                                                <div class="text-center mb-2">
                                                    <span class="text-2xl md:text-3xl">
                                                        @if ($key === 'large')
                                                            👤
                                                        @elseif($key === 'small')
                                                            💼
                                                        @else
                                                            🖼️
                                                        @endif
                                                    </span>
                                                </div>
                                                <p class="text-xs md:text-sm font-medium text-gray-900">
                                                    {{ $layout['name'] }}</p>
                                                <p class="text-xs text-gray-500 mt-1 hidden md:block">
                                                    {{ $layout['description'] }}</p>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- 배너 커스터마이징 -->
                            <div id="bannerCustomizationSection" style="display: none;"
                                class="mb-8 p-4 bg-purple-50 rounded-lg border border-purple-200">
                                <h3 class="text-sm font-bold text-gray-900 mb-4">🎨 배너 커스터마이징</h3>

                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            배너 모서리 둥글기: <span id="bannerRadiusValue" class="text-blue-600">24</span>px
                                        </label>
                                        <input type="range" name="banner_radius" id="banner_radius" min="0"
                                            max="50" value="24"
                                            class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer slider-thumb">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            배너 높이: <span id="bannerHeightValue" class="text-blue-600">128</span>px
                                        </label>
                                        <input type="range" name="banner_height" id="banner_height" min="80"
                                            max="250" value="128" step="10"
                                            class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer slider-thumb">
                                    </div>
                                </div>
                            </div>

                            <!-- 커버 이미지/배경 -->
                            <div class="mb-8" id="coverImageSection" style="display: none;">
                                <label class="block text-sm font-medium text-gray-700 mb-3">
                                    배너 배경
                                </label>

                                <div class="mb-4">
                                    <label class="block text-xs text-gray-600 mb-2">배너 배경색</label>
                                    <input type="color" id="cover_bg_color" name="cover_bg_color" value="#3B82F6"
                                        class="w-20 h-10 rounded cursor-pointer border border-gray-300">
                                </div>

                                <div>
                                    <label class="block text-xs text-gray-600 mb-2">배너 이미지 (선택)</label>
                                    <input type="file" name="cover_image" id="cover_image" accept="image/*"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm">
                                    <p class="text-xs text-gray-500 mt-1">권장 크기: 1200x400px, 최대 5MB</p>
                                </div>
                            </div>

                            <!-- 폰트 설정 -->
                            <div class="mb-8 p-4 bg-indigo-50 rounded-lg border border-indigo-200">
                                <h3 class="text-sm font-bold text-gray-900 mb-4">🔤 폰트 설정</h3>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        폰트 선택
                                    </label>
                                    <select name="font_family" id="font_family"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm md:text-base">
                                        <option value="Pretendard" selected>Pretendard (기본, 깔끔한 한글)</option>
                                        <option value="Noto Sans KR">Noto Sans KR (구글 고딕체)</option>
                                        <option value="Nanum Gothic">나눔고딕 (네이버)</option>
                                        <option value="Gowun Batang">고운바탕 (우아한 명조)</option>
                                        <option value="Roboto">Roboto (모던한 영문)</option>
                                        <option value="Open Sans">Open Sans (가독성 좋은 영문)</option>
                                        <option value="Montserrat">Montserrat (기하학적 영문)</option>
                                        <option value="Playfair Display">Playfair Display (우아한 세리프)</option>
                                        <option value="Inter">Inter (UI 전용 폰트)</option>
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
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm">
                                    <p class="text-xs text-gray-500 mt-1">지원 형식: .ttf, .woff, .woff2, .otf (최대 5MB)</p>
                                </div>

                                <!-- 폰트 미리보기 -->
                                <div class="mt-4 p-4 bg-white rounded-lg border border-gray-200">
                                    <p class="text-xs text-gray-500 mb-2">폰트 미리보기</p>
                                    <div id="fontPreview" style="transition: font-family 0.3s;">
                                        <p class="text-xl md:text-2xl font-bold mb-2">안녕하세요 Hello!</p>
                                        <p class="text-sm md:text-base">가나다라마바사 ABCDEFG</p>
                                        <p class="text-xs md:text-sm text-gray-600">1234567890 !@#$%</p>
                                    </div>
                                </div>
                            </div>

                            <!-- 배경 타입 -->
                            <div class="mb-8">
                                <label class="block text-sm font-medium text-gray-700 mb-3">
                                    배경 타입
                                </label>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                    @foreach ($config['background_types'] as $key => $label)
                                        <label class="cursor-pointer">
                                            <input type="radio" name="background_type" value="{{ $key }}"
                                                {{ $key === 'solid' ? 'checked' : '' }} class="hidden peer">
                                            <div
                                                class="p-3 border-2 border-gray-200 rounded-lg peer-checked:border-blue-600 peer-checked:bg-blue-50 text-center hover:border-blue-300 transition">
                                                <p class="font-medium text-sm">{{ $label }}</p>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- 배경 색상, 이미지, 영상 등... (기존 코드 유지) -->
                            <!-- ... 생략 (변경 없음) ... -->

                            <!-- 애니메이션 설정 -->
                            <div
                                class="mb-8 p-4 bg-gradient-to-br from-purple-50 to-pink-50 rounded-lg border border-purple-200">
                                <h3 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-2">
                                    <span>✨</span>
                                    <span>애니메이션 설정</span>
                                </h3>

                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            등장 애니메이션
                                        </label>
                                        <select name="animation_entrance" id="animation_entrance"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                                            @foreach ($config['animations']['entrance'] as $key => $label)
                                                <option value="{{ $key }}"
                                                    {{ $key === 'fade' ? 'selected' : '' }}>
                                                    {{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            애니메이션 속도
                                        </label>
                                        <select name="animation_speed" id="animation_speed"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                                            @foreach ($config['animations']['speed'] as $key => $label)
                                                <option value="{{ $key }}"
                                                    {{ $key === 'normal' ? 'selected' : '' }}>
                                                    {{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 탭 3: 링크 관리 (링크 버튼 스타일 추가 - 이슈 #1 해결) -->
                        <div id="links" class="tab-content">
                            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 gap-3">
                                <div>
                                    <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                                        <span class="text-2xl">🔗</span>
                                        <span>링크 관리</span>
                                    </h2>
                                    <p class="text-sm text-gray-500 mt-1">최소 1개 이상의 링크를 추가하세요</p>
                                </div>
                                <button type="button" onclick="addLink()"
                                    class="w-full sm:w-auto px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition font-medium flex items-center justify-center gap-2">
                                    <span class="text-xl">➕</span>
                                    <span>링크 추가</span>
                                </button>
                            </div>

                            <!-- 이슈 #1 해결: 링크 버튼 스타일 (링크관리 탭으로 이동) -->
                            <div class="mb-6 p-4 bg-green-50 rounded-lg border border-green-200">
                                <h3 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-2">
                                    <span>🎨</span>
                                    <span>링크 버튼 스타일 (모든 링크에 적용)</span>
                                </h3>

                                <div class="space-y-4">
                                    <!-- 버튼 모양 -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            버튼 모양
                                        </label>
                                        <div class="grid grid-cols-3 gap-3">
                                            <label class="cursor-pointer">
                                                <input type="radio" name="global_button_style" value="rounded" checked
                                                    class="hidden peer">
                                                <div
                                                    class="p-3 border-2 border-gray-200 rounded-lg peer-checked:border-green-600 peer-checked:bg-green-50 text-center transition">
                                                    <div class="w-full h-8 bg-gray-300 rounded-lg mb-1"></div>
                                                    <p class="text-xs font-medium">둥근 모서리</p>
                                                </div>
                                            </label>
                                            <label class="cursor-pointer">
                                                <input type="radio" name="global_button_style" value="square"
                                                    class="hidden peer">
                                                <div
                                                    class="p-3 border-2 border-gray-200 rounded-lg peer-checked:border-green-600 peer-checked:bg-green-50 text-center transition">
                                                    <div class="w-full h-8 bg-gray-300 mb-1"></div>
                                                    <p class="text-xs font-medium">사각형</p>
                                                </div>
                                            </label>
                                            <label class="cursor-pointer">
                                                <input type="radio" name="global_button_style" value="pill"
                                                    class="hidden peer">
                                                <div
                                                    class="p-3 border-2 border-gray-200 rounded-lg peer-checked:border-green-600 peer-checked:bg-green-50 text-center transition">
                                                    <div class="w-full h-8 bg-gray-300 rounded-full mb-1"></div>
                                                    <p class="text-xs font-medium">완전 둥글게</p>
                                                </div>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- 이슈 #3 해결: 발광 효과 강화 -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            호버 효과
                                        </label>
                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                            <label class="cursor-pointer">
                                                <input type="radio" name="global_hover_effect" value="none"
                                                    class="hidden peer">
                                                <div
                                                    class="p-3 border-2 border-gray-200 rounded-lg peer-checked:border-green-600 peer-checked:bg-green-50 text-center transition">
                                                    <p class="text-xs font-medium">없음</p>
                                                </div>
                                            </label>
                                            <label class="cursor-pointer">
                                                <input type="radio" name="global_hover_effect" value="scale" checked
                                                    class="hidden peer">
                                                <div
                                                    class="p-3 border-2 border-gray-200 rounded-lg peer-checked:border-green-600 peer-checked:bg-green-50 text-center transition">
                                                    <p class="text-xs font-medium">확대</p>
                                                </div>
                                            </label>
                                            <label class="cursor-pointer">
                                                <input type="radio" name="global_hover_effect" value="lift"
                                                    class="hidden peer">
                                                <div
                                                    class="p-3 border-2 border-gray-200 rounded-lg peer-checked:border-green-600 peer-checked:bg-green-50 text-center transition">
                                                    <p class="text-xs font-medium">떠오름</p>
                                                </div>
                                            </label>
                                            <label class="cursor-pointer">
                                                <input type="radio" name="global_hover_effect" value="glow"
                                                    class="hidden peer">
                                                <div
                                                    class="p-3 border-2 border-gray-200 rounded-lg peer-checked:border-green-600 peer-checked:bg-green-50 text-center transition">
                                                    <p class="text-xs font-medium">✨ 발광</p>
                                                </div>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- 이슈 #4 해결: 버튼 색상 선택 (소셜 제외) -->
                                    <div id="buttonColorSection">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            기본 버튼 색상
                                        </label>
                                        <p class="text-xs text-gray-500 mb-3">소셜 링크는 자동으로 브랜드 컬러가 적용됩니다</p>

                                        <div class="grid grid-cols-5 gap-2 mb-3">
                                            <label class="color-option">
                                                <input type="radio" name="button_color" value="#FFFFFF" checked
                                                    class="hidden">
                                                <div class="w-full h-12 rounded-lg border-2 border-gray-200 transition-all hover:scale-105 cursor-pointer"
                                                    style="background-color: #FFFFFF"></div>
                                            </label>
                                            <label class="color-option">
                                                <input type="radio" name="button_color" value="#3B82F6"
                                                    class="hidden">
                                                <div class="w-full h-12 rounded-lg border-2 border-gray-200 transition-all hover:scale-105 cursor-pointer"
                                                    style="background-color: #3B82F6"></div>
                                            </label>
                                            <label class="color-option">
                                                <input type="radio" name="button_color" value="#10B981"
                                                    class="hidden">
                                                <div class="w-full h-12 rounded-lg border-2 border-gray-200 transition-all hover:scale-105 cursor-pointer"
                                                    style="background-color: #10B981"></div>
                                            </label>
                                            <label class="color-option">
                                                <input type="radio" name="button_color" value="#F59E0B"
                                                    class="hidden">
                                                <div class="w-full h-12 rounded-lg border-2 border-gray-200 transition-all hover:scale-105 cursor-pointer"
                                                    style="background-color: #F59E0B"></div>
                                            </label>
                                            <label class="color-option">
                                                <input type="radio" name="button_color" value="#EF4444"
                                                    class="hidden">
                                                <div class="w-full h-12 rounded-lg border-2 border-gray-200 transition-all hover:scale-105 cursor-pointer"
                                                    style="background-color: #EF4444"></div>
                                            </label>
                                        </div>

                                        <div class="p-3 bg-white rounded-lg border border-gray-200">
                                            <label class="flex flex-col md:flex-row items-start md:items-center gap-2">
                                                <span class="text-sm font-medium text-gray-700">커스텀 색상:</span>
                                                <input type="color" id="customButtonColor" value="#FFFFFF"
                                                    class="w-16 h-10 rounded cursor-pointer border border-gray-300">
                                                <span class="text-xs text-gray-500">원하는 색상을 직접 선택하세요</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 드래그 안내 -->
                            <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                <p class="text-xs text-blue-800 flex items-center gap-2">
                                    <span>💡</span>
                                    <span><strong>드래그 앤 드롭:</strong> 각 링크 왼쪽의 <strong class="text-blue-600">⋮⋮</strong> 핸들을
                                        잡고 드래그하여 순서를 변경하세요!</span>
                                </p>
                            </div>

                            <!-- 링크 컨테이너 -->
                            <div id="linksContainer" class="space-y-4">
                                <!-- JavaScript로 동적 생성 -->
                            </div>
                        </div>
                    </div>

                    <!-- 제출 버튼 -->
                    <div class="mt-6 flex flex-col sm:flex-row gap-4">
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

    <!-- 모바일 미리보기 플로팅 버튼 -->
    <button type="button" id="mobilePreviewBtn" class="mobile-preview-btn">
        👁️
    </button>

    <!-- 모바일 미리보기 모달 (이슈 #1: 높이 고정 + X버튼 개선) -->
    <div id="mobilePreviewModal" class="preview-modal hidden">
        <div class="relative w-full h-full max-w-sm mx-auto flex flex-col">
            <!-- X 버튼을 모달 내부 상단에 배치 -->
            <div class="flex justify-end p-4">
                <button type="button" id="closePreview"
                    class="w-10 h-10 flex items-center justify-center bg-white/90 backdrop-blur-sm text-gray-800 text-2xl rounded-full hover:bg-white transition shadow-lg">
                    ✕
                </button>
            </div>

            <!-- 미리보기 영역 (높이 고정) -->
            <div class="flex-1 px-4 pb-4 overflow-hidden">
                <div class="phone-mockup-mobile w-full h-full bg-white rounded-2xl shadow-2xl overflow-hidden">
                    <div id="mobilePreview" class="w-full h-full overflow-y-auto"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.draftCheckInterval = null;

        function autoSave() {
            const nameInput = document.getElementById('name');
            const bioInput = document.getElementById('bio');

            if (!nameInput || !nameInput.value.trim()) {
                return;
            }

            const formData = {
                name: nameInput.value,
                bio: bioInput ? bioInput.value : '',
                background_type: document.querySelector('input[name="background_type"]:checked')?.value || 'solid',
                background_color: document.querySelector('input[name="background_color"]:checked')?.value || '#2B7FFF',
                profile_layout: document.querySelector('input[name="profile_layout"]:checked')?.value || 'large',
                text_color: document.getElementById('text_color')?.value || '#1f2937',
                text_size: document.getElementById('text_size')?.value || 'medium',
                font_family: document.getElementById('font_family')?.value || 'Pretendard',
                links: window.links || [],
                timestamp: new Date().toISOString()
            };

            localStorage.setItem('linkkit_draft', JSON.stringify(formData));
            // showAutoSaveStatus();
        }

        function showAutoSaveStatus() {
            const status = document.getElementById('autoSaveStatus');
            if (!status) return;

            status.classList.remove('hidden');
            status.innerHTML = '<span class="text-sm text-green-600">✅ 자동 저장됨</span>';

            setTimeout(() => {
                status.classList.add('hidden');
            }, 2000);
        }

        function showToast(message, duration = 2000) {
            let toast = document.getElementById('toast');
            if (!toast) {
                toast = document.createElement('div');
                toast.id = 'toast';
                toast.className = 'toast';
                document.body.appendChild(toast);
            }

            toast.textContent = message;
            toast.classList.add('show');

            setTimeout(() => {
                toast.classList.remove('show');
            }, duration);
        }

        function loadDraft() {
            const draft = localStorage.getItem('linkkit_draft');
            if (!draft) {
                alert('저장된 작업이 없습니다.');
                return;
            }

            try {
                const data = JSON.parse(draft);

                document.getElementById('name').value = data.name || '';
                if (document.getElementById('bio')) {
                    document.getElementById('bio').value = data.bio || '';
                }

                const bgTypeRadio = document.querySelector(
                    `input[name="background_type"][value="${data.background_type}"]`);
                if (bgTypeRadio) {
                    bgTypeRadio.checked = true;
                    bgTypeRadio.dispatchEvent(new Event('change'));
                }

                const bgColorRadio = document.querySelector(
                    `input[name="background_color"][value="${data.background_color}"]`);
                if (bgColorRadio) {
                    bgColorRadio.checked = true;
                }

                const layoutRadio = document.querySelector(`input[name="profile_layout"][value="${data.profile_layout}"]`);
                if (layoutRadio) {
                    layoutRadio.checked = true;
                    layoutRadio.dispatchEvent(new Event('change'));
                }

                if (document.getElementById('text_color')) {
                    document.getElementById('text_color').value = data.text_color || '#1f2937';
                }
                if (document.getElementById('text_size')) {
                    document.getElementById('text_size').value = data.text_size || 'medium';
                }
                if (document.getElementById('font_family')) {
                    document.getElementById('font_family').value = data.font_family || 'Pretendard';
                }

                if (data.links && Array.isArray(data.links)) {
                    window.links = data.links;
                    window.linkIdCounter = Math.max(...data.links.map(l => l.id), 0) + 1;
                    renderLinks();
                }

                if (typeof updatePreview === 'function') {
                    updatePreview();
                }

                document.getElementById('draftNotification').classList.add('hidden');
                showToast('✅ 저장된 작업을 불러왔습니다!');
            } catch (error) {
                console.error('Draft load error:', error);
                alert('저장된 작업을 불러오는데 실패했습니다.');
            }
        }

        function dismissDraft() {
            if (confirm('저장된 작업을 삭제하시겠습니까?')) {
                localStorage.removeItem('linkkit_draft');
                document.getElementById('draftNotification').classList.add('hidden');
                showToast('🗑️ 저장된 작업이 삭제되었습니다');
            }
        }

        function checkDraft() {
            const draft = localStorage.getItem('linkkit_draft');
            if (!draft) return;

            try {
                const data = JSON.parse(draft);
                const savedTime = new Date(data.timestamp);
                const now = new Date();
                const minutesAgo = Math.floor((now - savedTime) / 60000);

                const notification = document.getElementById('draftNotification');
                const timeDisplay = document.getElementById('draftTime');

                if (notification && timeDisplay) {
                    let timeText = '';
                    if (minutesAgo < 1) {
                        timeText = '방금 전 저장됨';
                    } else if (minutesAgo < 60) {
                        timeText = `${minutesAgo}분 전 저장됨`;
                    } else {
                        const hoursAgo = Math.floor(minutesAgo / 60);
                        timeText = `${hoursAgo}시간 전 저장됨`;
                    }

                    timeDisplay.textContent = timeText;
                    notification.classList.remove('hidden');
                }
            } catch (error) {
                console.error('Draft check error:', error);
                localStorage.removeItem('linkkit_draft');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            console.log('✅ Create page loaded');

            checkDraft();
            window.draftCheckInterval = setInterval(autoSave, 3000);

            const form = document.getElementById('createForm');
            if (form) {
                form.addEventListener('submit', function() {
                    localStorage.removeItem('linkkit_draft');
                    clearInterval(window.draftCheckInterval);
                });
            }

            window.addEventListener('beforeunload', function(e) {
                const draft = localStorage.getItem('linkkit_draft');
                const nameInput = document.getElementById('name');

                if (draft && nameInput && nameInput.value.trim()) {
                    e.preventDefault();
                    e.returnValue = '작업 중인 내용이 있습니다. 나가시겠습니까?';
                    return e.returnValue;
                }
            });
        });

        window.addEventListener('unload', function() {
            if (window.draftCheckInterval) {
                clearInterval(window.draftCheckInterval);
            }
        });
    </script>

    <script src="{{ asset('js/linkkit-create.js') }}"></script>
    <div id="toast" class="toast"></div>
@endsection
