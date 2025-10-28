// LinkKit 생성 페이지 JavaScript - 완전판

let links = [];
let linkIdCounter = 0;

// 이미지 데이터를 전역으로 관리
let imageData = {
    profile: null,
    cover: null,
    background: null,
    backgroundVideo: null
};

// SVG Placeholder (네트워크 에러 방지!)
const PLACEHOLDER_PROFILE = '/images/linkkit-logo.png';

/**
 * ✨ 안전한 이벤트 리스너 등록
 *
 * @param {string} elementId - 요소 ID (또는 selector가 제공되면 무시됨)
 * @param {string} eventType - 이벤트 타입 ('input', 'change', 'click' 등)
 * @param {Function} handler - 이벤트 핸들러 함수
 * @param {string} selector - (선택) CSS 셀렉터 (제공 시 elementId 대신 사용)
 */
function safeAddListener(elementId, eventType, handler, selector = null) {
    try {
        let element;

        // 셀렉터가 제공되면 querySelector 사용
        if (selector) {
            element = document.querySelector(selector);
        } else {
            // 아니면 ID로 찾기
            element = document.getElementById(elementId);
        }

        if (element) {
            element.addEventListener(eventType, handler);
            console.log(`✅ Event listener added: ${selector || elementId} (${eventType})`);
        } else {
            console.warn(`⚠️ Element not found: ${selector || elementId}`);
        }
    } catch (error) {
        console.error(`❌ Error adding listener to ${selector || elementId}:`, error);
    }
}

/**
 * ✨ 디바운스 함수 (연속 호출 방지)
 *
 * @param {Function} func - 실행할 함수
 * @param {number} wait - 대기 시간 (ms)
 * @returns {Function} 디바운스된 함수
 */
function debounce(func, wait = 300) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func.apply(this, args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// DOM 로드 완료 시
document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ DOM Loaded');

    try {
        initTabs();
        console.log('✅ Tabs initialized');

        initForm();
        console.log('✅ Form initialized');

        initLinks();
        console.log('✅ Links initialized');

        initMobilePreview();
        console.log('✅ Mobile preview initialized');

        // 초기 배경 섹션 표시
        const initialBackgroundType = document.querySelector('input[name="background_type"]:checked')?.value || 'solid';
        updateBackgroundSections(initialBackgroundType);
        console.log('✅ Background sections initialized:', initialBackgroundType);

        // 초기 프로필 레이아웃 체크
        const initialProfileLayout = document.querySelector('input[name="profile_layout"]:checked')?.value || 'large';
        toggleCoverSection(initialProfileLayout);
        console.log('✅ Cover section initialized:', initialProfileLayout);

        updatePreview();
        console.log('✅ Preview updated');

        // 첫 링크 추가
        addLink();
        console.log('✅ First link added');

        console.log('🎉 All initialization complete!');
    } catch (error) {
        console.error('❌ Initialization error:', error);
    }
});

/**
 * 탭 초기화
 */
function initTabs() {
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');

    console.log('📋 Found tabs:', tabButtons.length);

    if (tabButtons.length === 0) {
        console.error('❌ No tab buttons found!');
        return;
    }

    tabButtons.forEach((button, index) => {
        console.log(`  Tab ${index + 1}:`, button.dataset.tab);

        // 기존 이벤트 제거 (중복 방지)
        button.removeEventListener('click', handleTabClick);

        // 새 이벤트 추가
        button.addEventListener('click', handleTabClick, false);
    });
}

/**
 * 탭 클릭 핸들러
 */
function handleTabClick(e) {
    e.preventDefault();
    e.stopPropagation();

    const clickedButton = e.currentTarget;
    const tabName = clickedButton.dataset.tab;

    console.log('🖱️ Tab clicked:', tabName);

    // 모든 탭 비활성화
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('active');
    });

    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.remove('active');
    });

    // 선택된 탭 활성화
    clickedButton.classList.add('active');

    const targetContent = document.querySelector(`[data-content="${tabName}"]`);
    if (targetContent) {
        targetContent.classList.add('active');
        console.log('✅ Tab activated:', tabName);
    } else {
        console.error('❌ Tab content not found:', tabName);
    }
}

/**
 * 모바일 미리보기 초기화
 */
function initMobilePreview() {
    const previewButton = document.getElementById('mobilePreviewBtn');
    const previewModal = document.getElementById('mobilePreviewModal');
    const closeButton = document.getElementById('closePreview');

    if (!previewButton || !previewModal) {
        console.log('ℹ️ Mobile preview elements not found (desktop mode?)');
        return;
    }

    // 미리보기 열기
    previewButton.addEventListener('click', function() {
        previewModal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        updatePreview();
    });

    // 미리보기 닫기
    if (closeButton) {
        closeButton.addEventListener('click', function() {
            previewModal.classList.add('hidden');
            document.body.style.overflow = '';
        });
    }

    // 배경 클릭 시 닫기
    previewModal.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.add('hidden');
            document.body.style.overflow = '';
        }
    });
}

/**
 * 폼 입력 이벤트 초기화
 */
function initForm() {
    // 기본 정보 (디바운스 적용)
    const debouncedPreview = debounce(updatePreview, 300);
    safeAddListener('name', 'input', debouncedPreview);
    safeAddListener('bio', 'input', debouncedPreview);

    // 프로필 이미지
    safeAddListener('profile_image', 'change', handleProfileImage);

    // 프로필 레이아웃
    document.querySelectorAll('input[name="profile_layout"]').forEach(radio => {
        radio.addEventListener('change', function() {
            toggleCoverSection(this.value);
            updatePreview();
        });
    });

    // 커버 이미지
    safeAddListener('cover_image', 'change', handleCoverImage);

    // 커버 배경색
    safeAddListener('cover_bg_color', 'change', updatePreview);

    // 배너 radius (라운드)
    safeAddListener('banner_radius', 'input', function() {
        const value = this.value;
        const display = document.getElementById('bannerRadiusValue');
        if (display) display.textContent = value;
        debounce(updatePreview, 100)();
    });

    // 배너 높이
    safeAddListener('banner_height', 'input', function() {
        const value = this.value;
        const display = document.getElementById('bannerHeightValue');
        if (display) display.textContent = value;
        debounce(updatePreview, 100)();
    });

    // 폰트 선택
    safeAddListener('font_family', 'change', function() {
        const fontValue = this.value;
        console.log('🔤 Font changed to:', fontValue);

        // 커스텀 폰트 업로드 영역 표시/숨김
        const customUpload = document.getElementById('customFontUpload');
        if (customUpload) {
            customUpload.style.display = fontValue === 'custom' ? 'block' : 'none';
        }

        // 폰트 미리보기 박스에 즉시 적용
        // 자식요소에 폰트 적용
        const fontPreview = document.getElementById('fontPreview');
        if (fontPreview && fontValue !== 'custom') {

            fontPreview.style.fontFamily = fontValue + ', sans-serif';
            Array.from(fontPreview.children).forEach(child => {
                child.style.fontFamily = fontValue + ', sans-serif';
              });
        }

        // Google Font 로드 (Pretendard와 custom 제외)
        if (fontValue !== 'Pretendard' && fontValue !== 'custom') {
            loadGoogleFont(fontValue);
        }

        // 미리보기 업데이트
        updatePreview();
    });

    // 커스텀 폰트 업로드
    safeAddListener('custom_font', 'change', function(e) {
        handleCustomFont(e);
    });

    // 배경 타입
    document.querySelectorAll('input[name="background_type"]').forEach(radio => {
        radio.addEventListener('change', function() {
            updateBackgroundSections(this.value);
            updatePreview();
        });
    });

    // 배경 색상 - 프리셋
    document.querySelectorAll('input[name="background_color"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const customColorInput = document.getElementById('customBackgroundColor');
            if (customColorInput) {
                customColorInput.value = this.value;
            }
            updatePreview();
        });
    });

    // 배경 색상 - 커스텀 (디바운스 적용!)
    safeAddListener('customBackgroundColor', 'input', debounce(function() {
        // 모든 프리셋 체크 해제
        document.querySelectorAll('input[name="background_color"]').forEach(r => {
            r.checked = false;
        });

        // hidden input 생성 또는 업데이트
        let hiddenInput = document.getElementById('hiddenBackgroundColor');
        if (!hiddenInput) {
            hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.id = 'hiddenBackgroundColor';
            hiddenInput.name = 'background_color';
            document.getElementById('createForm').appendChild(hiddenInput);
        }
        hiddenInput.value = this.value;

        updatePreview();
    }, 150)); // 150ms 디바운스

    // 그라데이션 보조 색상
    safeAddListener('background_secondary_color', 'change', updatePreview, 'input[name="background_secondary_color"]');

    // 배경 이미지
    safeAddListener('background_image', 'change', handleBackgroundImage);

    // 배경 블러
    safeAddListener('background_blur', 'input', function() {
        const value = this.value;
        const display = document.getElementById('blurValue');
        if (display) display.textContent = value;
        debounce(updatePreview, 100)();
    });

    // 배경 밝기
    safeAddListener('background_brightness', 'input', function() {
        const value = this.value;
        const display = document.getElementById('brightnessValue');
        if (display) display.textContent = value;
        debounce(updatePreview, 100)();
    });

    // 배경 영상 URL
    safeAddListener('background_video_url', 'input', debounce(updatePreview, 500));

    // 배경 영상 파일
    safeAddListener('background_video_file', 'change', handleBackgroundVideo);

    // 텍스트 색상
    safeAddListener('text_color', 'input', debounce(updatePreview, 100));

    // 텍스트 크기
    safeAddListener('text_size', 'change', updatePreview);

    // 텍스트 두께
    safeAddListener('text_weight', 'change', updatePreview);

    // 애니메이션 입장
    safeAddListener('animation_entrance', 'change', updatePreview);

    // 애니메이션 속도
    safeAddListener('animation_speed', 'change', updatePreview);

    // 초기 폰트 로드
    const initialFont = document.getElementById('font_family')?.value;
    if (initialFont && initialFont !== 'Pretendard' && initialFont !== 'custom') {
        loadGoogleFont(initialFont);
    }
}

/**
 * 배경 섹션 표시/숨김
 */
function updateBackgroundSections(type) {
    // 모든 배경 관련 섹션
    const sections = {
        solid: document.getElementById('backgroundColorSection'),
        gradient: document.getElementById('gradientSecondarySection'),
        image: document.getElementById('backgroundImageSection'),
        video: document.getElementById('backgroundVideoSection')
    };

    // 이미지/영상 공통 섹션
    const blurSection = document.getElementById('backgroundBlurSection');
    const brightnessSection = document.getElementById('backgroundBrightnessSection');

    // 모두 숨김
    Object.values(sections).forEach(section => {
        if (section) section.style.display = 'none';
    });

    if (blurSection) blurSection.style.display = 'none';
    if (brightnessSection) brightnessSection.style.display = 'none';

    // 선택된 타입만 표시
    if (type === 'solid' && sections.solid) {
        sections.solid.style.display = 'block';
    } else if (type === 'gradient') {
        if (sections.solid) sections.solid.style.display = 'block';
        if (sections.gradient) sections.gradient.style.display = 'block';
    } else if (type === 'image' && sections.image) {
        sections.image.style.display = 'block';
        if (blurSection) blurSection.style.display = 'block';
        if (brightnessSection) brightnessSection.style.display = 'block';
    } else if (type === 'video' && sections.video) {
        sections.video.style.display = 'block';
    }
}

/**
 * 커버 섹션 표시/숨김
 */
function toggleCoverSection(layout) {
    const coverSection = document.getElementById('coverImageSection');
    const bannerCustomSection = document.getElementById('bannerCustomizationSection');

    if (layout === 'banner') {
        // 배너 레이아웃일 때
        if (coverSection) coverSection.style.display = 'block';
        if (bannerCustomSection) bannerCustomSection.style.display = 'block';
    } else {
        // 다른 레이아웃일 때
        if (coverSection) coverSection.style.display = 'none';
        if (bannerCustomSection) bannerCustomSection.style.display = 'none';
    }
}

/**
 * 프로필 이미지 처리
 */
function handleProfileImage(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(event) {
            imageData.profile = event.target.result;

            // 미리보기 이미지도 업데이트
            const previewImg = document.getElementById('profileImagePreview');
            if (previewImg) {
                previewImg.src = event.target.result;
            }

            updatePreview();
        };
        reader.readAsDataURL(file);
    }
}

/**
 * 커버 이미지 처리
 */
function handleCoverImage(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(event) {
            imageData.cover = event.target.result;
            updatePreview();
        };
        reader.readAsDataURL(file);
    }
}

/**
 * 배경 이미지 처리
 */
function handleBackgroundImage(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(event) {
            imageData.background = event.target.result;
            updatePreview();
        };
        reader.readAsDataURL(file);
    }
}

/**
 * 배경 영상 처리
 */
function handleBackgroundVideo(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(event) {
            imageData.backgroundVideo = event.target.result;
            updatePreview();
        };
        reader.readAsDataURL(file);
    }
}

/**
 * 링크 관리 초기화
 */
function initLinks() {
    const addLinkBtn = document.getElementById('addLinkBtn');
    if (addLinkBtn) {
        addLinkBtn.addEventListener('click', function(e) {
            e.preventDefault();
            addLink();
        });
    }
}

/**
 * 링크 추가
 */
function addLink() {
    const linkId = linkIdCounter++;
    const link = {
        id: linkId,
        type: 'link',           // 링크 타입 (link, social, card, product, contact)
        title: '',
        url: '',
        button_style: 'rounded',
        button_size: 'medium',
        hover_effect: 'scale',

        // 버튼 색상 (신규!)
        button_bg_color: '#FFFFFF',      // 버튼 배경색
        button_text_color: '#1F2937',    // 버튼 텍스트 색상

        // 소셜형
        icon: 'link',           // 아이콘 종류

        // 카드형
        thumbnail: null,        // 썸네일 이미지
        description: '',        // 설명

        // 판매형
        price: '',              // 원가
        sale_price: '',         // 할인가
        currency: 'KRW',        // 통화

        // 연락처형
        contact_type: 'phone'   // phone, email, kakao
    };

    links.push(link);
    renderLinks();
    updatePreview();
}

/**
 * 링크 업데이트
 */
function updateLink(linkId, field, value) {
    const link = links.find(l => l.id === linkId);
    if (link) {
        link[field] = value;

        // 타입 변경 시 해당 타입 필드만 표시
        if (field === 'type') {
            const allTypes = ['social', 'card', 'product', 'contact'];
            allTypes.forEach(type => {
                const fieldDiv = document.getElementById(`${type}_fields_${linkId}`);
                if (fieldDiv) {
                    if (type === value) {
                        fieldDiv.classList.remove('hidden');
                    } else {
                        fieldDiv.classList.add('hidden');
                    }
                }
            });
        }

        renderLinks();
        updatePreview();
    }
}

/**
 * 링크 썸네일 이미지 처리
 */
function handleLinkThumbnail(linkId, event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const link = links.find(l => l.id === linkId);
            if (link) {
                link.thumbnail = e.target.result;
                updatePreview();
            }
        };
        reader.readAsDataURL(file);
    }
}

/**
 * 링크 삭제
 */
function removeLink(linkId) {
    links = links.filter(l => l.id !== linkId);
    renderLinks();
    updatePreview();
}

/**
 * 링크 목록 렌더링
 */
function renderLinks() {
    const container = document.getElementById('linksContainer');
    if (!container) return;

    container.innerHTML = '';

    links.forEach((link, index) => {
        const linkItem = createLinkItem(link, index);
        container.appendChild(linkItem);
    });
}

/**
 * 링크 아이템 HTML 생성
 */
function createLinkItem(link, index) {
    const div = document.createElement('div');
    div.className = 'bg-gray-50 p-4 rounded-lg border border-gray-200 mb-3';

    // 소셜 아이콘 목록
    const socialIcons = {
        'link': '🔗',
        'instagram': '📷',
        'youtube': '🎥',
        'tiktok': '🎵',
        'twitter': '🐦',
        'facebook': '👥',
        'linkedin': '💼',
        'github': '💻',
        'email': '✉️',
        'phone': '📞'
    };

    // 연락처 타입
    const contactTypes = {
        'phone': '전화',
        'email': '이메일',
        'kakao': '카카오톡',
        'telegram': '텔레그램',
        'whatsapp': '왓츠앱'
    };

    div.innerHTML = `
        <div class="flex items-center justify-between mb-3">
            <span class="font-medium text-gray-700">링크 ${index + 1}</span>
            <button type="button" onclick="removeLink(${link.id})" class="text-red-600 hover:text-red-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </button>
        </div>

        <div class="space-y-3">
            <!-- 링크 타입 선택 -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">링크 타입</label>
                <div class="grid grid-cols-2 gap-2">
                    <label class="cursor-pointer">
                        <input type="radio" name="link_type_${link.id}" value="link"
                            ${link.type === 'link' ? 'checked' : ''}
                            onchange="updateLink(${link.id}, 'type', this.value)"
                            class="hidden peer">
                        <div class="px-3 py-2 border-2 border-gray-200 rounded-lg text-center peer-checked:border-blue-600 peer-checked:bg-blue-50 hover:border-blue-300 transition">
                            <span class="text-sm font-medium">🔗 링크형</span>
                        </div>
                    </label>

                    <label class="cursor-pointer">
                        <input type="radio" name="link_type_${link.id}" value="social"
                            ${link.type === 'social' ? 'checked' : ''}
                            onchange="updateLink(${link.id}, 'type', this.value)"
                            class="hidden peer">
                        <div class="px-3 py-2 border-2 border-gray-200 rounded-lg text-center peer-checked:border-blue-600 peer-checked:bg-blue-50 hover:border-blue-300 transition">
                            <span class="text-sm font-medium">📱 소셜형</span>
                        </div>
                    </label>

                    <label class="cursor-pointer">
                        <input type="radio" name="link_type_${link.id}" value="card"
                            ${link.type === 'card' ? 'checked' : ''}
                            onchange="updateLink(${link.id}, 'type', this.value)"
                            class="hidden peer">
                        <div class="px-3 py-2 border-2 border-gray-200 rounded-lg text-center peer-checked:border-blue-600 peer-checked:bg-blue-50 hover:border-blue-300 transition">
                            <span class="text-sm font-medium">🖼️ 카드형</span>
                        </div>
                    </label>

                    <label class="cursor-pointer">
                        <input type="radio" name="link_type_${link.id}" value="product"
                            ${link.type === 'product' ? 'checked' : ''}
                            onchange="updateLink(${link.id}, 'type', this.value)"
                            class="hidden peer">
                        <div class="px-3 py-2 border-2 border-gray-200 rounded-lg text-center peer-checked:border-blue-600 peer-checked:bg-blue-50 hover:border-blue-300 transition">
                            <span class="text-sm font-medium">🛍️ 판매형</span>
                        </div>
                    </label>

                    <label class="cursor-pointer col-span-2">
                        <input type="radio" name="link_type_${link.id}" value="contact"
                            ${link.type === 'contact' ? 'checked' : ''}
                            onchange="updateLink(${link.id}, 'type', this.value)"
                            class="hidden peer">
                        <div class="px-3 py-2 border-2 border-gray-200 rounded-lg text-center peer-checked:border-blue-600 peer-checked:bg-blue-50 hover:border-blue-300 transition">
                            <span class="text-sm font-medium">📞 연락처형</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- 기본 필드 (모든 타입 공통) -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">링크 제목 *</label>
                <input type="text" value="${link.title}"
                    onchange="updateLink(${link.id}, 'title', this.value)"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="예: 인스타그램">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">URL *</label>
                <input type="url" value="${link.url}"
                    onchange="updateLink(${link.id}, 'url', this.value)"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="https://instagram.com/yourname">
            </div>

            <!-- 소셜형 전용 필드 -->
            <div id="social_fields_${link.id}" class="${link.type === 'social' ? '' : 'hidden'}">
                <label class="block text-sm font-medium text-gray-700 mb-1">아이콘</label>
                <select onchange="updateLink(${link.id}, 'icon', this.value)"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                    ${Object.entries(socialIcons).map(([value, emoji]) =>
                        `<option value="${value}" ${link.icon === value ? 'selected' : ''}>${emoji} ${value}</option>`
                    ).join('')}
                </select>
            </div>

            <!-- 카드형 전용 필드 -->
            <div id="card_fields_${link.id}" class="${link.type === 'card' ? '' : 'hidden'}">
                <div class="space-y-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">썸네일 이미지</label>
                        <input type="file"
                            onchange="handleLinkThumbnail(${link.id}, event)"
                            accept="image/*"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">설명</label>
                        <textarea
                            onchange="updateLink(${link.id}, 'description', this.value)"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500"
                            rows="2"
                            placeholder="간단한 설명을 입력하세요">${link.description}</textarea>
                    </div>
                </div>
            </div>

            <!-- 판매형 전용 필드 -->
            <div id="product_fields_${link.id}" class="${link.type === 'product' ? '' : 'hidden'}">
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">원가</label>
                        <input type="number" value="${link.price}"
                            onchange="updateLink(${link.id}, 'price', this.value)"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500"
                            placeholder="29000">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">할인가</label>
                        <input type="number" value="${link.sale_price}"
                            onchange="updateLink(${link.id}, 'sale_price', this.value)"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500"
                            placeholder="19000">
                    </div>
                </div>
                <div class="mt-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">통화</label>
                    <select onchange="updateLink(${link.id}, 'currency', this.value)"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        <option value="KRW" ${link.currency === 'KRW' ? 'selected' : ''}>원 (KRW)</option>
                        <option value="USD" ${link.currency === 'USD' ? 'selected' : ''}>달러 (USD)</option>
                        <option value="EUR" ${link.currency === 'EUR' ? 'selected' : ''}>유로 (EUR)</option>
                    </select>
                </div>
            </div>

            <!-- 연락처형 전용 필드 -->
            <div id="contact_fields_${link.id}" class="${link.type === 'contact' ? '' : 'hidden'}">
                <label class="block text-sm font-medium text-gray-700 mb-1">연락처 타입</label>
                <select onchange="updateLink(${link.id}, 'contact_type', this.value)"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                    ${Object.entries(contactTypes).map(([value, label]) =>
                        `<option value="${value}" ${link.contact_type === value ? 'selected' : ''}>${label}</option>`
                    ).join('')}
                </select>
            </div>

            <!-- 버튼 스타일 옵션 -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">버튼 스타일</label>
                    <select onchange="updateLink(${link.id}, 'button_style', this.value)"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="rounded" ${link.button_style === 'rounded' ? 'selected' : ''}>둥근 모서리</option>
                        <option value="pill" ${link.button_style === 'pill' ? 'selected' : ''}>알약형</option>
                        <option value="sharp" ${link.button_style === 'sharp' ? 'selected' : ''}>직각</option>
                        <option value="soft" ${link.button_style === 'soft' ? 'selected' : ''}>부드러운</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">버튼 크기</label>
                    <select onchange="updateLink(${link.id}, 'button_size', this.value)"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="small" ${link.button_size === 'small' ? 'selected' : ''}>작게</option>
                        <option value="medium" ${link.button_size === 'medium' ? 'selected' : ''}>보통</option>
                        <option value="large" ${link.button_size === 'large' ? 'selected' : ''}>크게</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">호버 효과</label>
                <select onchange="updateLink(${link.id}, 'hover_effect', this.value)"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="none" ${link.hover_effect === 'none' ? 'selected' : ''}>없음</option>
                    <option value="scale" ${link.hover_effect === 'scale' ? 'selected' : ''}>확대</option>
                    <option value="lift" ${link.hover_effect === 'lift' ? 'selected' : ''}>들어올림</option>
                    <option value="glow" ${link.hover_effect === 'glow' ? 'selected' : ''}>빛남</option>
                </select>
            </div>

            <!-- 버튼 색상 설정 (신규!) -->
            <div class="p-3 bg-blue-50 rounded-lg border border-blue-200">
                <h4 class="text-xs font-bold text-gray-900 mb-3">🎨 버튼 색상</h4>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">배경색</label>
                        <input type="color"
                            value="${link.button_bg_color}"
                            onchange="updateLink(${link.id}, 'button_bg_color', this.value)"
                            class="w-full h-10 rounded cursor-pointer border border-gray-300">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">텍스트 색상</label>
                        <input type="color"
                            value="${link.button_text_color}"
                            onchange="updateLink(${link.id}, 'button_text_color', this.value)"
                            class="w-full h-10 rounded cursor-pointer border border-gray-300">
                    </div>
                </div>
            </div>

            <!-- Hidden inputs for form submission -->
            <input type="hidden" name="links[${index}][type]" value="${link.type}">
            <input type="hidden" name="links[${index}][title]" value="${link.title}">
            <input type="hidden" name="links[${index}][url]" value="${link.url}">
            <input type="hidden" name="links[${index}][button_style]" value="${link.button_style}">
            <input type="hidden" name="links[${index}][button_size]" value="${link.button_size}">
            <input type="hidden" name="links[${index}][hover_effect]" value="${link.hover_effect}">
            <input type="hidden" name="links[${index}][button_bg_color]" value="${link.button_bg_color}">
            <input type="hidden" name="links[${index}][button_text_color]" value="${link.button_text_color}">
            <input type="hidden" name="links[${index}][icon]" value="${link.icon}">
            <input type="hidden" name="links[${index}][description]" value="${link.description}">
            <input type="hidden" name="links[${index}][price]" value="${link.price}">
            <input type="hidden" name="links[${index}][sale_price]" value="${link.sale_price}">
            <input type="hidden" name="links[${index}][currency]" value="${link.currency}">
            <input type="hidden" name="links[${index}][contact_type]" value="${link.contact_type}">
        </div>
    `;

    return div;
}

/**
 * 실시간 미리보기 업데이트
 */
function updatePreview() {
    const preview = document.getElementById('preview');
    const mobilePreview = document.getElementById('mobilePreview');

    if (!preview) return;

    try {
        // 데이터 수집
        const name = document.getElementById('name')?.value || '이름을 입력하세요';
        const bio = document.getElementById('bio')?.value || '';
        const profileLayout = document.querySelector('input[name="profile_layout"]:checked')?.value || 'large';
        const backgroundType = document.querySelector('input[name="background_type"]:checked')?.value || 'solid';

        // 배경 색상
        let backgroundColor = '#2B7FFF';
        const checkedColorRadio = document.querySelector('input[name="background_color"]:checked');
        const hiddenColorInput = document.getElementById('hiddenBackgroundColor');

        if (checkedColorRadio) {
            backgroundColor = checkedColorRadio.value;
        } else if (hiddenColorInput && hiddenColorInput.value) {
            backgroundColor = hiddenColorInput.value;
        }

        const backgroundSecondaryColor = document.querySelector('input[name="background_secondary_color"]')?.value || '#9333EA';
        const backgroundBlur = document.getElementById('background_blur')?.value || 0;
        const backgroundBrightness = document.getElementById('background_brightness')?.value || 100;
        const backgroundVideoUrl = document.getElementById('background_video_url')?.value || '';

        // 텍스트 스타일
        const textColor = document.getElementById('text_color')?.value || '#FFFFFF';
        const textSize = document.getElementById('text_size')?.value || 'medium';
        const textWeight = document.getElementById('text_weight')?.value || 'bold';

        // 애니메이션
        const animationEntrance = document.getElementById('animation_entrance')?.value || 'fade';
        const animationSpeed = document.getElementById('animation_speed')?.value || 'normal';

        // 배경 스타일
        let backgroundStyle = '';
        let backgroundHTML = '';

        if (backgroundType === 'solid') {
            backgroundStyle = `background-color: ${backgroundColor};`;
        } else if (backgroundType === 'gradient') {
            backgroundStyle = `background: linear-gradient(135deg, ${backgroundColor} 0%, ${backgroundSecondaryColor} 100%);`;
        } else if (backgroundType === 'image' && imageData.background) {
            backgroundStyle = `background-image: url('${imageData.background}'); background-size: cover; background-position: center;`;
            const brightness = backgroundBrightness / 100;
            const blur = backgroundBlur / 10;
            if (blur > 0 || brightness !== 1) {
                backgroundStyle += `filter: blur(${blur}px) brightness(${brightness});`;
            }
        } else if (backgroundType === 'video') {
            const videoSrc = imageData.backgroundVideo || backgroundVideoUrl;
            if (videoSrc) {
                backgroundHTML = `
                    <video autoplay muted loop playsinline class="absolute inset-0 w-full h-full object-cover">
                        <source src="${videoSrc}" type="video/mp4">
                    </video>
                    <div class="absolute inset-0 bg-black bg-opacity-30"></div>
                `;
            } else {
                backgroundStyle = `background-color: ${backgroundColor};`;
            }
        }

        // 텍스트 스타일 클래스
        const textSizeClass = textSize === 'small' ? 'text-sm' : textSize === 'large' ? 'text-3xl' : 'text-2xl';
        const textWeightClass = `font-${textWeight}`;

        // 프로필 이미지
        const profileImageSrc = imageData.profile || PLACEHOLDER_PROFILE;

        // 커버 배경
        const coverBgColor = document.getElementById('cover_bg_color')?.value || '#3B82F6';
        const coverImageSrc = imageData.cover;

        // 배너 커스터마이징
        const bannerRadius = document.getElementById('banner_radius')?.value || 24;
        const bannerHeight = document.getElementById('banner_height')?.value || 128;

        // 폰트 패밀리
        const fontFamily = getCurrentFont();

        // Google Font 로드 (커스텀이 아닌 경우)
        if (fontFamily && fontFamily !== 'Pretendard' && fontFamily !== 'CustomFont') {
            loadGoogleFont(fontFamily);
        }

        // 폰트 스타일 (공백 있는 폰트는 따옴표 추가)
        const fontFamilyStyle = fontFamily.includes(' ') ? `'${fontFamily}'` : fontFamily;
        const fontStyle = `font-family: ${fontFamilyStyle}, sans-serif;`;

        console.log('🔤 Current Font:', fontFamily);
        console.log('📝 Font Style:', fontStyle);

        // 프로필 HTML
        let profileHTML = '';
        if (profileLayout === 'large') {
            profileHTML = `
                <div class="text-center mb-8 animate-${animationEntrance} animate-${animationSpeed}">
                    <img src="${profileImageSrc}"
                        alt="${name}"
                        onerror="this.src='${PLACEHOLDER_PROFILE}'"
                        class="w-24 h-24 rounded-full mx-auto mb-4 border-4 border-white shadow-lg object-cover">
                    <h1 class="${textSizeClass} ${textWeightClass} mb-2 drop-shadow-lg" style="color: ${textColor}; ${fontStyle}">${name}</h1>
                    ${bio ? `<p class="opacity-90" style="color: ${textColor}; ${fontStyle}">${bio}</p>` : ''}
                </div>
            `;
        } else if (profileLayout === 'small') {
            profileHTML = `
                <div class="flex items-center gap-4 mb-8 animate-${animationEntrance} animate-${animationSpeed}">
                    <img src="${profileImageSrc}"
                        alt="${name}"
                        onerror="this.src='${PLACEHOLDER_PROFILE}'"
                        class="w-16 h-16 rounded-full border-2 border-white shadow-lg object-cover flex-shrink-0">
                    <div>
                        <h1 class="${textSizeClass} ${textWeightClass}" style="color: ${textColor}; ${fontStyle}">${name}</h1>
                        ${bio ? `<p class="text-sm opacity-90" style="color: ${textColor}; ${fontStyle}">${bio}</p>` : ''}
                    </div>
                </div>
            `;
        } else if (profileLayout === 'banner') {
            const coverStyle = coverImageSrc
                ? `background-image: url('${coverImageSrc}'); background-size: cover; background-position: center;`
                : `background-color: ${coverBgColor};`;

            profileHTML = `
                <div class="relative mb-8 -mx-6">
                    <div style="${coverStyle} height: ${bannerHeight}px; border-radius: ${bannerRadius}px ${bannerRadius}px 0 0;"></div>
                    <div class="relative px-6 -mt-12 animate-${animationEntrance} animate-${animationSpeed}">
                        <img src="${profileImageSrc}"
                            alt="${name}"
                            onerror="this.src='${PLACEHOLDER_PROFILE}'"
                            class="w-20 h-20 rounded-full border-4 border-white shadow-lg mb-3 object-cover">
                        <h1 class="${textSizeClass} ${textWeightClass} drop-shadow-lg" style="color: ${textColor}; ${fontStyle}">${name}</h1>
                        ${bio ? `<p class="opacity-90" style="color: ${textColor}; ${fontStyle}">${bio}</p>` : ''}
                    </div>
                </div>
            `;
        }

        // 링크 HTML
        let linksHTML = '';
        links.forEach((link, index) => {
            if (link.title && link.url) {
                const buttonClass = getButtonClass(link.button_style, link.button_size);
                const hoverClass = link.hover_effect !== 'none' ? `hover-${link.hover_effect}` : '';
                const animationDelay = `animation-delay: ${index * 0.1}s;`;

                // 타입별 렌더링
                if (link.type === 'social') {
                    // 소셜형: 아이콘 + 텍스트
                    const iconEmoji = {
                        'link': '🔗', 'instagram': '📷', 'youtube': '🎥', 'tiktok': '🎵',
                        'twitter': '🐦', 'facebook': '👥', 'linkedin': '💼', 'github': '💻',
                        'email': '✉️', 'phone': '📞'
                    }[link.icon] || '🔗';

                    linksHTML += `
                        <a href="${link.url}"
                            class="${buttonClass} ${hoverClass} font-medium flex items-center justify-center gap-2 mb-3 shadow-lg transition-all animate-${animationEntrance} animate-${animationSpeed}"
                            style="${animationDelay} background-color: ${link.button_bg_color}; color: ${link.button_text_color}; ${fontStyle}">
                            <span class="text-xl">${iconEmoji}</span>
                            <span>${link.title}</span>
                        </a>
                    `;
                } else if (link.type === 'card') {
                    // 카드형: 썸네일 + 제목 + 설명
                    linksHTML += `
                        <a href="${link.url}"
                            class="${hoverClass} bg-white rounded-xl overflow-hidden shadow-lg transition-all mb-3 block animate-${animationEntrance} animate-${animationSpeed}"
                            style="${animationDelay}">
                            ${link.thumbnail ? `
                                <img src="${link.thumbnail}"
                                    onerror="this.style.display='none'"
                                    class="w-full h-32 object-cover"
                                    alt="${link.title}">
                            ` : ''}
                            <div class="p-4">
                                <h3 class="font-bold text-gray-900 mb-1" style="${fontStyle}">${link.title}</h3>
                                ${link.description ? `<p class="text-sm text-gray-600" style="${fontStyle}">${link.description}</p>` : ''}
                            </div>
                        </a>
                    `;
                } else if (link.type === 'product') {
                    // 판매형: 가격 + 할인율
                    const price = parseFloat(link.price);
                    const salePrice = parseFloat(link.sale_price);
                    const hasDiscount = price && salePrice && salePrice < price;
                    const discountPercent = hasDiscount ? Math.round(((price - salePrice) / price) * 100) : 0;

                    const formatPrice = (value, currency) => {
                        if (!value) return '';
                        if (currency === 'KRW') return `${Number(value).toLocaleString()}원`;
                        if (currency === 'USD') return `$${value}`;
                        if (currency === 'EUR') return `€${value}`;
                        return value;
                    };

                    linksHTML += `
                        <a href="${link.url}"
                            class="${buttonClass} ${hoverClass} shadow-lg transition-all mb-3 block animate-${animationEntrance} animate-${animationSpeed}"
                            style="${animationDelay} background-color: ${link.button_bg_color}; color: ${link.button_text_color}; ${fontStyle}">
                            <div class="flex items-center justify-between">
                                <span class="font-medium">${link.title}</span>
                                <div class="text-right">
                                    ${hasDiscount ? `
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-bold" style="color: ${link.button_text_color}; opacity: 0.8;">${discountPercent}% OFF</span>
                                            <span class="text-sm line-through" style="color: ${link.button_text_color}; opacity: 0.5;">${formatPrice(price, link.currency)}</span>
                                        </div>
                                        <div class="text-lg font-bold">${formatPrice(salePrice, link.currency)}</div>
                                    ` : `
                                        <div class="text-lg font-bold">${formatPrice(price || salePrice, link.currency)}</div>
                                    `}
                                </div>
                            </div>
                        </a>
                    `;
                } else if (link.type === 'contact') {
                    // 연락처형: 타입별 아이콘
                    const contactIcons = {
                        'phone': '📞',
                        'email': '✉️',
                        'kakao': '💬',
                        'telegram': '✈️',
                        'whatsapp': '📱'
                    };
                    const contactIcon = contactIcons[link.contact_type] || '📞';

                    linksHTML += `
                        <a href="${link.url}"
                            class="${buttonClass} ${hoverClass} font-medium flex items-center justify-center gap-2 mb-3 shadow-lg transition-all animate-${animationEntrance} animate-${animationSpeed}"
                            style="${animationDelay} background-color: ${link.button_bg_color}; color: ${link.button_text_color}; ${fontStyle}">
                            <span class="text-xl">${contactIcon}</span>
                            <span>${link.title}</span>
                        </a>
                    `;
                } else {
                    // 기본 링크형
                    linksHTML += `
                        <a href="${link.url}"
                            class="${buttonClass} ${hoverClass} font-medium text-center block mb-3 shadow-lg transition-all animate-${animationEntrance} animate-${animationSpeed}"
                            style="${animationDelay} background-color: ${link.button_bg_color}; color: ${link.button_text_color}; ${fontStyle}">
                            ${link.title}
                        </a>
                    `;
                }
            }
        });

        // 전체 미리보기 HTML
        // const fontFamilyStylePreview = fontFamily.includes(' ') ? `'${fontFamily}'` : fontFamily;
        const previewHTML = `
            <div class="relative w-full h-full" style="${backgroundStyle}${backgroundStyle ? ' ' : ''}font-family: ${fontFamilyStyle}, sans-serif;">
                ${backgroundHTML}
                <div class="relative z-10 p-6 overflow-y-auto h-full">
                    ${profileHTML}
                    <div class="space-y-3">
                        ${linksHTML || `<p class="text-center opacity-70" style="color: ${textColor}; ${fontStyle}">링크를 추가하세요</p>`}
                    </div>
                </div>
            </div>
        `;

        preview.innerHTML = previewHTML;

        if (mobilePreview) {
            mobilePreview.innerHTML = previewHTML;
        }
    } catch (error) {
        console.error('❌ Preview update error:', error);
    }
}

/**
 * 버튼 클래스 생성
 */
function getButtonClass(style, size) {
    let classes = [];

    switch(style) {
        case 'rounded': classes.push('rounded-xl'); break;
        case 'pill': classes.push('rounded-full'); break;
        case 'sharp': classes.push('rounded-none'); break;
        case 'soft': classes.push('rounded-lg'); break;
    }

    switch(size) {
        case 'small': classes.push('py-2 px-4 text-sm'); break;
        case 'medium': classes.push('py-3 px-6 text-base'); break;
        case 'large': classes.push('py-4 px-8 text-lg'); break;
    }

    return classes.join(' ');
}

/**
 * Google Font 로드
 */
let loadedFonts = new Set(['Pretendard']); // 기본 폰트는 이미 로드됨

function loadGoogleFont(fontName) {
    if (!fontName || fontName === 'Pretendard' || fontName === 'custom' || loadedFonts.has(fontName)) {
        console.log('⏭️ Font already loaded or skipped:', fontName);
        return; // 이미 로드되었거나 커스텀 폰트면 스킵
    }

    console.log('📥 Loading Google Font:', fontName);

    // 기존 링크가 있는지 확인
    const existingLink = document.querySelector(`link[href*="family=${fontName.replace(' ', '+')}"]`);
    if (existingLink) {
        console.log('✅ Font link already exists:', fontName);
        loadedFonts.add(fontName);
        return;
    }

    try {
        // Google Fonts CSS 동적 로드
        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = `https://fonts.googleapis.com/css2?family=${fontName.replace(/ /g, '+')}:wght@400;500;600;700&display=swap`;

        // 로드 완료 이벤트
        link.onload = function() {
            console.log('✅ Font loaded successfully:', fontName);
            loadedFonts.add(fontName);
        };

        // 에러 처리
        link.onerror = function() {
            console.error('❌ Font load failed:', fontName);
        };

        document.head.appendChild(link);
        console.log('📤 Font link added to head:', link.href);

    } catch (error) {
        console.error('❌ Error loading font:', fontName, error);
    }
}

/**
 * 커스텀 폰트 업로드 처리
 */
let customFontData = null;

function handleCustomFont(event) {
    const file = event.target.files[0];
    if (!file) return;

    // 파일 확장자 체크
    const validExtensions = ['.ttf', '.woff', '.woff2', '.otf'];
    const fileName = file.name.toLowerCase();
    const isValid = validExtensions.some(ext => fileName.endsWith(ext));

    if (!isValid) {
        alert('지원하는 폰트 형식: .ttf, .woff, .woff2, .otf');
        return;
    }

    // 파일 크기 체크 (최대 5MB)
    if (file.size > 5 * 1024 * 1024) {
        alert('폰트 파일 크기는 최대 5MB까지 가능합니다.');
        return;
    }

    const reader = new FileReader();
    reader.onload = function(e) {
        customFontData = e.target.result;

        // 커스텀 폰트 동적 적용
        const fontFormat = fileName.endsWith('.woff2') ? 'woff2' :
                          fileName.endsWith('.woff') ? 'woff' :
                          fileName.endsWith('.otf') ? 'opentype' : 'truetype';

        const style = document.createElement('style');
        style.id = 'custom-font-style';
        style.textContent = `
            @font-face {
                font-family: 'CustomFont';
                src: url('${e.target.result}') format('${fontFormat}');
                font-weight: normal;
                font-style: normal;
            }
        `;

        // 기존 커스텀 폰트 스타일 제거
        const oldStyle = document.getElementById('custom-font-style');
        if (oldStyle) oldStyle.remove();

        document.head.appendChild(style);

        // 폰트 선택을 'custom'으로 변경
        const fontSelect = document.getElementById('font_family');
        if (fontSelect) {
            fontSelect.value = 'custom';
        }

        updatePreview();
    };

    reader.readAsDataURL(file);
}

/**
 * 현재 선택된 폰트 가져오기
 */
function getCurrentFont() {
    const fontSelect = document.getElementById('font_family');

    if (!fontSelect) {
        console.warn('⚠️ font_family element not found! Using default Pretendard');
        return 'Pretendard';
    }

    const fontValue = fontSelect.value;
    console.log('📝 Font select value:', fontValue);

    if (fontValue === 'custom' && customFontData) {
        console.log('✅ Using custom font');
        return 'CustomFont';
    }

    const result = fontValue || 'Pretendard';
    console.log('🎯 Final font:', result);
    return result;
}

// 전역 함수로 노출
window.updateLink = updateLink;
window.removeLink = removeLink;
window.handleLinkThumbnail = handleLinkThumbnail;
window.loadGoogleFont = loadGoogleFont;
window.handleCustomFont = handleCustomFont;
