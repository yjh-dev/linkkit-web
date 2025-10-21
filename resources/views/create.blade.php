@extends('layouts.app')

@section('title', '링크 페이지 만들기 - LinkKit')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-blue-50 to-white py-12 px-4">
        <div class="container mx-auto">
            <!-- 헤더 -->
            <div class="text-center mb-16">
                <a href="/" class="inline-flex items-center gap-2 group">
                    <span class="text-4xl font-bold text-linkkit-blue">
                        LinkKit
                    </span>
                    <span class="text-2xl group-hover:rotate-12 transition-transform">🔗</span>
                </a>
                <p class="text-gray-600 mt-4 text-lg">1분이면 당신만의 링크 페이지가 완성돼요</p>

                <!-- ✨ 선택한 프리셋 표시 ✨ -->
                <div
                    class="mt-6 inline-flex items-center gap-2 bg-white px-4 py-2 rounded-full shadow-md border-2 border-blue-100">
                    <span class="text-sm font-semibold text-gray-600">선택한 디자인:</span>
                    @if ($preset === 'basic')
                        <span class="bg-linkkit-blue text-white px-3 py-1 rounded-full text-sm font-bold">Basic 💙</span>
                    @elseif($preset === 'minimal')
                        <span class="bg-gray-600 text-white px-3 py-1 rounded-full text-sm font-bold">Minimal ⚪</span>
                    @elseif($preset === 'dark')
                        <span class="bg-gray-900 text-white px-3 py-1 rounded-full text-sm font-bold">Dark 🌙</span>
                    @endif
                    <a href="/choose-preset" class="text-xs text-gray-500 hover:text-gray-700 ml-2">변경</a>
                </div>
            </div>>

            <!-- 메인 컨텐츠 -->
            <div class="grid lg:grid-cols-5 gap-12 max-w-7xl mx-auto">

                <!-- 좌측: 입력 폼 (3/5) -->
                <div class="lg:col-span-3">
                    <div class="bg-white rounded-3xl shadow-xl p-10 lg:p-12">
                        <div class="flex items-center gap-3 mb-10">
                            <div class="w-12 h-12 bg-linkkit-blue rounded-xl flex items-center justify-center shadow-md">
                                <span class="text-white text-xl">✏️</span>
                            </div>
                            <h2 class="text-2xl font-bold text-gray-800">정보 입력</h2>
                        </div>

                        <form id="linkPageForm" action="{{ route('linkpage.store') }}" method="POST"
                            enctype="multipart/form-data" class="space-y-10">

                            <!-- ✨ 프리셋 hidden input 추가! ✨ -->
                            <input type="hidden" name="preset" value="{{ $preset }}">
                            @csrf

                            <!-- 프로필 이미지 -->
                            <div class="text-center">
                                <label class="block text-sm font-semibold text-gray-700 mb-6">
                                    프로필 이미지
                                </label>
                                <div class="flex flex-col items-center gap-5">
                                    <div id="profilePreview"
                                        class="w-32 h-32 bg-gradient-to-br from-blue-100 to-blue-50 rounded-full flex items-center justify-center overflow-hidden shadow-lg ring-4 ring-blue-50 hover:ring-linkkit-blue/30 transition-all cursor-pointer">
                                        <span class="text-blue-300 text-5xl">👤</span>
                                    </div>
                                    <label class="cursor-pointer group">
                                        <div
                                            class="bg-linkkit-blue hover:bg-blue-600 text-white px-6 py-3 rounded-xl transition-all shadow-md hover:shadow-lg font-medium">
                                            📸 이미지 선택
                                        </div>
                                        <input type="file" name="profile_image" id="profile_image" accept="image/*"
                                            class="hidden">
                                    </label>
                                    <p class="text-xs text-gray-500">최대 2MB • JPG, PNG</p>
                                </div>
                            </div>

                            <!-- 구분선 -->
                            <div class="border-t-2 border-gray-100"></div>

                            <!-- 이름 입력 -->
                            <div>
                                <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-4">
                                    <span class="text-lg">👤</span>
                                    <span>이름</span>
                                    <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="name" id="name" required maxlength="255"
                                    placeholder="홍길동"
                                    class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-linkkit-blue focus:border-linkkit-blue transition-all text-lg">
                            </div>

                            <!-- 소개 입력 -->
                            <div>
                                <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-4">
                                    <span class="text-lg">💬</span>
                                    <span>소개</span>
                                </label>
                                <textarea name="bio" id="bio" rows="4" maxlength="500" placeholder="안녕하세요! 저는..."
                                    class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-linkkit-blue focus:border-linkkit-blue transition-all resize-none text-base"></textarea>
                                <div class="flex justify-between items-center mt-3">
                                    <p class="text-xs text-gray-500">간단한 자기소개를 작성해주세요</p>
                                    <span id="bioCount" class="text-xs text-gray-400">0/500</span>
                                </div>
                            </div>

                            <!-- ✨ 여기에 비밀번호 필드 추가! ✨ -->
                            <div>
                                <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-4">
                                    <span class="text-lg">🔐</span>
                                    <span>비밀번호</span>
                                    <span class="text-red-500">*</span>
                                </label>
                                <input type="password" name="password" id="password" required minlength="4" maxlength="20"
                                    placeholder="페이지 수정용 비밀번호 (최소 4자)"
                                    class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-linkkit-blue focus:border-linkkit-blue transition-all text-lg">
                                <p class="text-xs text-gray-500 mt-3">
                                    ⚠️ 이 비밀번호로 나중에 페이지를 수정할 수 있어요. 꼭 기억해주세요!
                                </p>
                            </div>

                            <!-- 구분선 -->
                            <div class="border-t-2 border-gray-100"></div>
                            <!-- 구분선 -->
                            <div class="border-t-2 border-gray-100"></div>

                            <!-- 링크 목록 -->
                            <div>
                                <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-6">
                                    <span class="text-lg">🔗</span>
                                    <span>링크 추가</span>
                                    <span class="text-red-500">*</span>
                                </label>

                                <div id="linksContainer" class="space-y-5 mb-6">
                                    <!-- 링크 아이템 1 -->
                                    <div
                                        class="link-item bg-blue-50/50 border-2 border-blue-100 rounded-2xl p-6 hover:border-linkkit-blue transition-all">
                                        <div class="flex items-start gap-4">
                                            <div
                                                class="w-10 h-10 bg-linkkit-blue rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm">
                                                <span class="text-white font-bold">1</span>
                                            </div>
                                            <div class="flex-1 space-y-4">
                                                <input type="text" name="links[0][title]" placeholder="링크 제목 (예: 인스타그램)"
                                                    required
                                                    class="link-title w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-linkkit-blue focus:border-linkkit-blue transition-all bg-white">
                                                <input type="url" name="links[0][url]"
                                                    placeholder="https://instagram.com/username" required
                                                    class="link-url w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-linkkit-blue focus:border-linkkit-blue transition-all bg-white">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <button type="button" id="addLinkBtn"
                                    class="w-full border-2 border-dashed border-gray-300 rounded-2xl py-5 text-gray-600 hover:border-linkkit-blue hover:text-linkkit-blue hover:bg-blue-50 transition-all flex items-center justify-center gap-2 font-medium group">
                                    <span class="text-xl group-hover:scale-110 transition-transform">➕</span>
                                    <span>링크 추가하기</span>
                                </button>
                            </div>

                            <!-- 제출 버튼 -->
                            <div class="pt-6">
                                <button type="submit"
                                    class="w-full bg-linkkit-blue hover:bg-blue-600 text-white py-5 rounded-2xl font-bold text-lg transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                                    <span>✨</span>
                                    <span>링크 페이지 생성하기</span>
                                    <span>🚀</span>
                                </button>

                                <p class="text-center text-sm text-gray-500 mt-5">
                                    완전 무료 • 로그인 불필요 • 1분 완성
                                </p>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- 우측: 실시간 미리보기 (2/5) -->
                <!-- 우측: 실시간 미리보기 (2/5) -->
                <div class="lg:col-span-2">
                    <div class="lg:sticky lg:top-8">
                        <div class="text-center mb-8">
                            <div
                                class="inline-flex items-center gap-2 bg-white px-5 py-3 rounded-full shadow-md border-2
                @if ($preset === 'basic') border-blue-100
                @elseif($preset === 'minimal') border-gray-200
                @elseif($preset === 'dark') border-gray-800 @endif">
                                <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                                <p class="text-sm font-semibold text-gray-700">실시간 미리보기</p>
                            </div>
                        </div>

                        <!-- 스마트폰 프레임 -->
                        <div class="mx-auto max-w-sm">
                            <div class="relative">
                                <!-- 그림자 효과 (프리셋별) -->
                                <div
                                    class="absolute inset-0 rounded-[3.5rem] blur-3xl
                    @if ($preset === 'basic') bg-linkkit-blue/20
                    @elseif($preset === 'minimal') bg-gray-300/20
                    @elseif($preset === 'dark') bg-gray-900/40 @endif">
                                </div>

                                <!-- 실제 폰 프레임 -->
                                <div class="relative bg-gray-900 rounded-[3rem] p-3 shadow-2xl">
                                    <div
                                        class="absolute top-0 left-1/2 -translate-x-1/2 w-40 h-7 bg-gray-900 rounded-b-3xl z-10">
                                    </div>

                                    <!-- 스크린 -->
                                    <div class="rounded-[2.5rem] overflow-hidden
                        @if ($preset === 'basic') bg-white
                        @elseif($preset === 'minimal') bg-gray-50
                        @elseif($preset === 'dark') bg-gray-900 @endif"
                                        style="height: 650px;">

                                        <div class="h-full overflow-y-auto">
                                            <div class="px-8 py-12">

                                                <!-- 프로필 영역 (프리셋별 스타일) -->
                                                <div class="text-center mb-10">
                                                    <!-- 프로필 이미지 -->
                                                    <div id="previewProfileImage"
                                                        class="w-28 h-28 mx-auto rounded-full flex items-center justify-center overflow-hidden mb-5 shadow-lg
                                        @if ($preset === 'basic') bg-gradient-to-br from-blue-100 to-blue-50 ring-4 ring-blue-50
                                        @elseif($preset === 'minimal')
                                            bg-gray-200 ring-4 ring-gray-100
                                        @elseif($preset === 'dark')
                                            bg-yellow-400 ring-4 ring-gray-800 @endif">

                                                        <span
                                                            class="text-5xl
                                            @if ($preset === 'basic') text-blue-300
                                            @elseif($preset === 'minimal') text-gray-400
                                            @elseif($preset === 'dark') text-gray-900 @endif">👤</span>
                                                    </div>

                                                    <!-- 이름 -->
                                                    <h2 id="previewName"
                                                        class="text-2xl font-bold mb-3 min-h-[2rem] px-4
                                        @if ($preset === 'basic') text-gray-800
                                        @elseif($preset === 'minimal') text-gray-900
                                        @elseif($preset === 'dark') text-white @endif">
                                                        이름을 입력하세요
                                                    </h2>

                                                    <!-- 소개 -->
                                                    <p id="previewBio"
                                                        class="text-sm leading-relaxed min-h-[1.5rem] px-4
                                        @if ($preset === 'basic') text-gray-600
                                        @elseif($preset === 'minimal') text-gray-600
                                        @elseif($preset === 'dark') text-gray-400 @endif">
                                                        소개를 입력하세요
                                                    </p>
                                                </div>

                                                <!-- 링크 목록 -->
                                                <div id="previewLinks" class="space-y-4">
                                                    @if ($preset === 'basic')
                                                        <!-- Basic 스타일 -->
                                                        <div
                                                            class="bg-blue-50 rounded-2xl p-8 text-center border-2 border-dashed border-blue-200">
                                                            <p class="text-blue-400 text-sm leading-relaxed">
                                                                ✨ 링크를 추가하면<br>여기에 표시돼요
                                                            </p>
                                                        </div>
                                                    @elseif($preset === 'minimal')
                                                        <!-- Minimal 스타일 -->
                                                        <div
                                                            class="bg-white rounded-xl p-8 text-center border border-dashed border-gray-300">
                                                            <p class="text-gray-400 text-sm leading-relaxed">
                                                                ✨ 링크를 추가하면<br>여기에 표시돼요
                                                            </p>
                                                        </div>
                                                    @elseif($preset === 'dark')
                                                        <!-- Dark 스타일 -->
                                                        <div
                                                            class="bg-gray-800 rounded-2xl p-8 text-center border-2 border-dashed border-gray-700">
                                                            <p class="text-gray-500 text-sm leading-relaxed">
                                                                ✨ 링크를 추가하면<br>여기에 표시돼요
                                                            </p>
                                                        </div>
                                                    @endif
                                                </div>

                                                <!-- 푸터 -->
                                                <div
                                                    class="text-center mt-16 pt-10 border-t
                                    @if ($preset === 'basic') border-gray-100
                                    @elseif($preset === 'minimal') border-gray-200
                                    @elseif($preset === 'dark') border-gray-800 @endif">
                                                    <p
                                                        class="text-xs
                                        @if ($preset === 'basic') text-gray-400
                                        @elseif($preset === 'minimal') text-gray-400
                                        @elseif($preset === 'dark') text-gray-600 @endif">
                                                        Made with</p>
                                                    <p
                                                        class="text-sm font-bold mt-1
                                        @if ($preset === 'basic') text-linkkit-blue
                                        @elseif($preset === 'minimal') text-gray-700
                                        @elseif($preset === 'dark') text-yellow-400 @endif">
                                                        LinkKit 🔗</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* 스크롤바 커스터마이징 */
        .overflow-y-auto::-webkit-scrollbar {
            width: 6px;
        }

        .overflow-y-auto::-webkit-scrollbar-track {
            background: transparent;
        }

        .overflow-y-auto::-webkit-scrollbar-thumb {
            background: #e5e7eb;
            border-radius: 10px;
        }

        .overflow-y-auto::-webkit-scrollbar-thumb:hover {
            background: #2B7FFF;
        }
    </style>

    <script>
        let linkIndex = 1;

        // 이름 실시간 반영
        document.getElementById('name').addEventListener('input', function(e) {
            const value = e.target.value.trim() || '이름을 입력하세요';
            document.getElementById('previewName').textContent = value;
        });

        // 소개 실시간 반영 + 글자수 카운트
        document.getElementById('bio').addEventListener('input', function(e) {
            const value = e.target.value;
            const displayValue = value.trim() || '소개를 입력하세요';
            document.getElementById('previewBio').textContent = displayValue;
            document.getElementById('bioCount').textContent = `${value.length}/500`;
        });

        // 프로필 이미지 미리보기
        document.getElementById('profile_image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // 파일 크기 체크 (2MB)
                if (file.size > 2 * 1024 * 1024) {
                    alert('⚠️ 이미지 크기는 2MB 이하여야 합니다.');
                    this.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(event) {
                    const imgHTML = `<img src="${event.target.result}" class="w-full h-full object-cover">`;
                    document.getElementById('profilePreview').innerHTML = imgHTML;
                    document.getElementById('previewProfileImage').innerHTML = imgHTML;
                };
                reader.readAsDataURL(file);
            }
        });

        // 링크 추가 버튼
        document.getElementById('addLinkBtn').addEventListener('click', function() {
            const container = document.getElementById('linksContainer');
            const currentCount = container.querySelectorAll('.link-item').length;

            // 최대 10개 제한
            if (currentCount >= 10) {
                alert('⚠️ 링크는 최대 10개까지 추가할 수 있습니다.');
                return;
            }

            const newLink = document.createElement('div');
            newLink.className =
                'link-item bg-blue-50/50 border-2 border-blue-100 rounded-2xl p-6 hover:border-linkkit-blue transition-all';
            newLink.innerHTML = `
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 bg-linkkit-blue rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm">
                    <span class="text-white font-bold">${currentCount + 1}</span>
                </div>
                <div class="flex-1 space-y-4">
                    <input
                        type="text"
                        name="links[${linkIndex}][title]"
                        placeholder="링크 제목 (예: 유튜브)"
                        required
                        class="link-title w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-linkkit-blue focus:border-linkkit-blue transition-all bg-white"
                    >
                    <input
                        type="url"
                        name="links[${linkIndex}][url]"
                        placeholder="https://youtube.com/@channel"
                        required
                        class="link-url w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-linkkit-blue focus:border-linkkit-blue transition-all bg-white"
                    >
                </div>
                <button type="button" class="remove-link text-gray-400 hover:text-red-500 transition-colors p-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        `;

            container.appendChild(newLink);
            linkIndex++;

            updateLinkNumbers();
            updatePreviewLinks();
            attachLinkListeners();
        });

        // 링크 삭제
        document.addEventListener('click', function(e) {
            if (e.target.closest('.remove-link')) {
                const linkItem = e.target.closest('.link-item');
                const container = document.getElementById('linksContainer');

                // 최소 1개는 유지
                if (container.querySelectorAll('.link-item').length <= 1) {
                    alert('⚠️ 최소 1개의 링크는 필요합니다.');
                    return;
                }

                linkItem.remove();
                updateLinkNumbers();
                updatePreviewLinks();
            }
        });

        // 링크 번호 업데이트
        function updateLinkNumbers() {
            document.querySelectorAll('.link-item').forEach((item, index) => {
                const numberSpan = item.querySelector('.bg-linkkit-blue span');
                if (numberSpan) {
                    numberSpan.textContent = index + 1;
                }
            });
        }

        // 링크 입력 감지
        function attachLinkListeners() {
            document.querySelectorAll('.link-title, .link-url').forEach(input => {
                input.removeEventListener('input', updatePreviewLinks);
                input.addEventListener('input', updatePreviewLinks);
            });
        }

        // 미리보기 링크 업데이트
        // 미리보기 링크 업데이트
        function updatePreviewLinks() {
            const previewContainer = document.getElementById('previewLinks');
            const linkItems = document.querySelectorAll('.link-item');
            const preset = '{{ $preset }}'; // 선택된 프리셋

            if (linkItems.length === 0) {
                // 프리셋별 빈 상태 메시지
                if (preset === 'basic') {
                    previewContainer.innerHTML = `
                <div class="bg-blue-50 rounded-2xl p-8 text-center border-2 border-dashed border-blue-200">
                    <p class="text-blue-400 text-sm leading-relaxed">
                        ✨ 링크를 추가하면<br>여기에 표시돼요
                    </p>
                </div>
            `;
                } else if (preset === 'minimal') {
                    previewContainer.innerHTML = `
                <div class="bg-white rounded-xl p-8 text-center border border-dashed border-gray-300">
                    <p class="text-gray-400 text-sm leading-relaxed">
                        ✨ 링크를 추가하면<br>여기에 표시돼요
                    </p>
                </div>
            `;
                } else if (preset === 'dark') {
                    previewContainer.innerHTML = `
                <div class="bg-gray-800 rounded-2xl p-8 text-center border-2 border-dashed border-gray-700">
                    <p class="text-gray-500 text-sm leading-relaxed">
                        ✨ 링크를 추가하면<br>여기에 표시돼요
                    </p>
                </div>
            `;
                }
                return;
            }

            let html = '';
            linkItems.forEach((item, index) => {
                const title = item.querySelector('.link-title').value.trim() || `링크 ${index + 1}`;
                const url = item.querySelector('.link-url').value.trim();

                // 프리셋별 링크 카드 스타일
                if (preset === 'basic') {
                    html += `
                <div class="group bg-white border-2 border-gray-200 rounded-2xl p-5 hover:border-linkkit-blue hover:shadow-md transition-all cursor-pointer">
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-gray-800 text-base mb-2 truncate">${title}</p>
                            ${url ? `<p class="text-xs text-gray-500 truncate">${url}</p>` : '<p class="text-xs text-blue-400">URL을 입력해주세요</p>'}
                        </div>
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-linkkit-blue transition-colors flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                </div>
            `;
                } else if (preset === 'minimal') {
                    html += `
                <div class="group bg-white border border-gray-300 rounded-xl p-4 hover:border-gray-600 transition-all cursor-pointer">
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-gray-900 text-sm mb-1 truncate">${title}</p>
                            ${url ? `<p class="text-xs text-gray-500 truncate">${url}</p>` : '<p class="text-xs text-gray-400">URL을 입력해주세요</p>'}
                        </div>
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-gray-700 transition-colors flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                </div>
            `;
                } else if (preset === 'dark') {
                    html += `
                <div class="group bg-gray-800 border-2 border-gray-700 rounded-2xl p-5 hover:border-yellow-400 transition-all cursor-pointer">
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-white text-base mb-2 truncate">${title}</p>
                            ${url ? `<p class="text-xs text-gray-400 truncate">${url}</p>` : '<p class="text-xs text-yellow-400">URL을 입력해주세요</p>'}
                        </div>
                        <svg class="w-5 h-5 text-gray-500 group-hover:text-yellow-400 transition-colors flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                </div>
            `;
                }
            });

            previewContainer.innerHTML = html;
        }

        // 폼 제출 전 검증
        document.getElementById('linkPageForm').addEventListener('submit', function(e) {
            const name = document.getElementById('name').value.trim();
            const links = document.querySelectorAll('.link-item');

            if (!name) {
                e.preventDefault();
                alert('⚠️ 이름을 입력해주세요.');
                document.getElementById('name').focus();
                return;
            }

            if (links.length === 0) {
                e.preventDefault();
                alert('⚠️ 최소 1개의 링크를 추가해주세요.');
                return;
            }

            // 로딩 표시
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<span class="animate-spin">⏳</span> <span>생성 중...</span>';
            submitBtn.disabled = true;
        });

        // 초기 실행
        attachLinkListeners();
        updatePreviewLinks();
    </script>
@endsection
