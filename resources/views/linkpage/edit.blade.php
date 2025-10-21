@extends('layouts.app')

@section('title', '페이지 수정 - LinkKit')

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
            <p class="text-gray-600 mt-4 text-lg">페이지 수정하기</p>
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

                    <form id="linkPageForm" action="{{ route('linkpage.update', $linkPage->uuid) }}" method="POST" enctype="multipart/form-data" class="space-y-10">
                        @csrf
                        @method('PUT')

                        <!-- 프로필 이미지 -->
                        <div class="text-center">
                            <label class="block text-sm font-semibold text-gray-700 mb-6">
                                프로필 이미지
                            </label>
                            <div class="flex flex-col items-center gap-5">
                                <div id="profilePreview" class="w-32 h-32 bg-gradient-to-br from-blue-100 to-blue-50 rounded-full flex items-center justify-center overflow-hidden shadow-lg ring-4 ring-blue-50 hover:ring-linkkit-blue/30 transition-all cursor-pointer">
                                    @if($linkPage->profile_image)
                                        <img src="{{ asset('storage/' . $linkPage->profile_image) }}" class="w-full h-full object-cover">
                                    @else
                                        <span class="text-blue-300 text-5xl">👤</span>
                                    @endif
                                </div>
                                <label class="cursor-pointer group">
                                    <div class="bg-linkkit-blue hover:bg-blue-600 text-white px-6 py-3 rounded-xl transition-all shadow-md hover:shadow-lg font-medium">
                                        📸 이미지 변경
                                    </div>
                                    <input
                                        type="file"
                                        name="profile_image"
                                        id="profile_image"
                                        accept="image/*"
                                        class="hidden"
                                    >
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
                            <input
                                type="text"
                                name="name"
                                id="name"
                                required
                                maxlength="255"
                                value="{{ $linkPage->name }}"
                                placeholder="홍길동"
                                class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-linkkit-blue focus:border-linkkit-blue transition-all text-lg"
                            >
                        </div>

                        <!-- 소개 입력 -->
                        <div>
                            <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-4">
                                <span class="text-lg">💬</span>
                                <span>소개</span>
                            </label>
                            <textarea
                                name="bio"
                                id="bio"
                                rows="4"
                                maxlength="500"
                                placeholder="안녕하세요! 저는..."
                                class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-linkkit-blue focus:border-linkkit-blue transition-all resize-none text-base"
                            >{{ $linkPage->bio }}</textarea>
                            <div class="flex justify-between items-center mt-3">
                                <p class="text-xs text-gray-500">간단한 자기소개를 작성해주세요</p>
                                <span id="bioCount" class="text-xs text-gray-400">{{ strlen($linkPage->bio ?? '') }}/500</span>
                            </div>
                        </div>

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
                                @foreach($linkPage->links as $index => $link)
                                <!-- 링크 아이템 -->
                                <div class="link-item bg-blue-50/50 border-2 border-blue-100 rounded-2xl p-6 hover:border-linkkit-blue transition-all">
                                    <div class="flex items-start gap-4">
                                        <div class="w-10 h-10 bg-linkkit-blue rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm">
                                            <span class="text-white font-bold">{{ $index + 1 }}</span>
                                        </div>
                                        <div class="flex-1 space-y-4">
                                            <input
                                                type="text"
                                                name="links[{{ $index }}][title]"
                                                value="{{ $link->title }}"
                                                placeholder="링크 제목 (예: 인스타그램)"
                                                required
                                                class="link-title w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-linkkit-blue focus:border-linkkit-blue transition-all bg-white"
                                            >
                                            <input
                                                type="url"
                                                name="links[{{ $index }}][url]"
                                                value="{{ $link->url }}"
                                                placeholder="https://instagram.com/username"
                                                required
                                                class="link-url w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-linkkit-blue focus:border-linkkit-blue transition-all bg-white"
                                            >
                                        </div>
                                        @if($index > 0)
                                        <button type="button" class="remove-link text-gray-400 hover:text-red-500 transition-colors p-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            <button
                                type="button"
                                id="addLinkBtn"
                                class="w-full border-2 border-dashed border-gray-300 rounded-2xl py-5 text-gray-600 hover:border-linkkit-blue hover:text-linkkit-blue hover:bg-blue-50 transition-all flex items-center justify-center gap-2 font-medium group"
                            >
                                <span class="text-xl group-hover:scale-110 transition-transform">➕</span>
                                <span>링크 추가하기</span>
                            </button>
                        </div>

                        <!-- 제출 버튼 -->
                        <div class="pt-6">
                            <button
                                type="submit"
                                class="w-full bg-linkkit-blue hover:bg-blue-600 text-white py-5 rounded-2xl font-bold text-lg transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex items-center justify-center gap-2"
                            >
                                <span>💾</span>
                                <span>수정 완료</span>
                            </button>

                            <div class="flex justify-center gap-4 mt-5">
                                <a
                                    href="{{ route('linkpage.show', $linkPage->uuid) }}"
                                    class="text-gray-500 hover:text-gray-700 text-sm transition-colors"
                                >
                                    취소
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- 우측: 실시간 미리보기 (2/5) -->
            <div class="lg:col-span-2">
                <div class="lg:sticky lg:top-8">
                    <div class="text-center mb-8">
                        <div class="inline-flex items-center gap-2 bg-white px-5 py-3 rounded-full shadow-md border-2 border-blue-100">
                            <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                            <p class="text-sm font-semibold text-gray-700">실시간 미리보기</p>
                        </div>
                    </div>

                    <!-- 스마트폰 프레임 -->
                    <div class="mx-auto max-w-sm">
                        <div class="relative">
                            <div class="absolute inset-0 bg-linkkit-blue/20 rounded-[3.5rem] blur-3xl"></div>

                            <div class="relative bg-gray-900 rounded-[3rem] p-3 shadow-2xl">
                                <div class="absolute top-0 left-1/2 -translate-x-1/2 w-40 h-7 bg-gray-900 rounded-b-3xl z-10"></div>

                                <div class="bg-white rounded-[2.5rem] overflow-hidden" style="height: 650px;">
                                    <div class="h-full overflow-y-auto">
                                        <div class="px-8 py-12">
                                            <div class="text-center mb-10">
                                                <div id="previewProfileImage" class="w-28 h-28 mx-auto bg-gradient-to-br from-blue-100 to-blue-50 rounded-full flex items-center justify-center overflow-hidden mb-5 shadow-lg ring-4 ring-blue-50">
                                                    @if($linkPage->profile_image)
                                                        <img src="{{ asset('storage/' . $linkPage->profile_image) }}" class="w-full h-full object-cover">
                                                    @else
                                                        <span class="text-blue-300 text-5xl">👤</span>
                                                    @endif
                                                </div>
                                                <h2 id="previewName" class="text-2xl font-bold text-gray-800 mb-3 min-h-[2rem] px-4">
                                                    {{ $linkPage->name }}
                                                </h2>
                                                <p id="previewBio" class="text-gray-600 text-sm leading-relaxed min-h-[1.5rem] px-4">
                                                    {{ $linkPage->bio ?? '소개를 입력하세요' }}
                                                </p>
                                            </div>

                                            <div id="previewLinks" class="space-y-4">
                                                @foreach($linkPage->links as $link)
                                                <div class="bg-white border-2 border-gray-200 rounded-2xl p-5 hover:border-linkkit-blue transition-all cursor-pointer">
                                                    <div class="flex items-center justify-between gap-4">
                                                        <div class="flex-1 min-w-0">
                                                            <p class="font-bold text-gray-800 text-base mb-2">{{ $link->title }}</p>
                                                            <p class="text-xs text-gray-500 truncate">{{ $link->url }}</p>
                                                        </div>
                                                        <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                        </svg>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>

                                            <div class="text-center mt-16 pt-10 border-t border-gray-100">
                                                <p class="text-xs text-gray-400">Made with</p>
                                                <p class="text-sm font-bold text-linkkit-blue mt-1">LinkKit 🔗</p>
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

<script>
    let linkIndex = {{ count($linkPage->links) }};

    // create.blade.php와 동일한 JavaScript 코드
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

        if (currentCount >= 10) {
            alert('⚠️ 링크는 최대 10개까지 추가할 수 있습니다.');
            return;
        }

        const newLink = document.createElement('div');
        newLink.className = 'link-item bg-blue-50/50 border-2 border-blue-100 rounded-2xl p-6 hover:border-linkkit-blue transition-all';
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

            if (container.querySelectorAll('.link-item').length <= 1) {
                alert('⚠️ 최소 1개의 링크는 필요합니다.');
                return;
            }

            linkItem.remove();
            updateLinkNumbers();
            updatePreviewLinks();
        }
    });

    function updateLinkNumbers() {
        document.querySelectorAll('.link-item').forEach((item, index) => {
            const numberSpan = item.querySelector('.bg-linkkit-blue span');
            if (numberSpan) {
                numberSpan.textContent = index + 1;
            }
        });
    }

    function attachLinkListeners() {
        document.querySelectorAll('.link-title, .link-url').forEach(input => {
            input.removeEventListener('input', updatePreviewLinks);
            input.addEventListener('input', updatePreviewLinks);
        });
    }

    function updatePreviewLinks() {
        const previewContainer = document.getElementById('previewLinks');
        const linkItems = document.querySelectorAll('.link-item');

        if (linkItems.length === 0) {
            previewContainer.innerHTML = `
                <div class="bg-blue-50 rounded-2xl p-8 text-center border-2 border-dashed border-blue-200">
                    <p class="text-blue-400 text-sm leading-relaxed">✨ 링크를 추가하면<br>여기에 표시돼요</p>
                </div>
            `;
            return;
        }

        let html = '';
        linkItems.forEach((item, index) => {
            const title = item.querySelector('.link-title').value.trim() || `링크 ${index + 1}`;
            const url = item.querySelector('.link-url').value.trim();

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
        });

        previewContainer.innerHTML = html;
    }

    // 초기 실행
    attachLinkListeners();
    updatePreviewLinks();
</script>
@endsection
