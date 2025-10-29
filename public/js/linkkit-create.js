// LinkKit 생성 페이지 JavaScript - 완전판 (5가지 이슈 모두 해결)

let links = [];
let linkIdCounter = 0;
let sortableInstance = null;
let loadedFonts = new Set(['Pretendard']);
let lastSavedData = null;
let autoSaveTimer = null; // 이슈 #5: 자동저장 타이머

// 이미지 데이터
let imageData = {
    profile: null,
    cover: null,
    background: null,
    backgroundVideo: null
};

const PLACEHOLDER_PROFILE = '/images/linkkit-logo.png';

// 이슈 #4: 소셜 브랜드 컬러
const SOCIAL_COLORS = {
    'instagram': 'linear-gradient(135deg, #833AB4, #FD1D1D, #FCB045)',
    'youtube': '#FF0000',
    'tiktok': '#000000',
    'twitter': '#1DA1F2',
    'facebook': '#1877F2',
    'linkedin': '#0A66C2',
    'github': '#181717',
    'link': '#3B82F6'
};

/**
 * 안전한 이벤트 리스너 등록
 */
function safeAddListener(elementId, eventType, handler, selector = null) {
    try {
        let element;
        if (selector) {
            element = document.querySelector(selector);
        } else {
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
 * 디바운스 함수
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

// ============================================
// 드래그 앤 드롭 초기화
// ============================================

function initSortable() {
    const container = document.getElementById('linksContainer');
    if (!container) {
        console.warn('⚠️ linksContainer not found');
        return;
    }

    if (sortableInstance) {
        sortableInstance.destroy();
        sortableInstance = null;
        console.log('🔄 Previous Sortable instance destroyed');
    }

    if (links.length === 0) {
        console.log('⚠️ No links to sort');
        return;
    }

    try {
        sortableInstance = new Sortable(container, {
            animation: 200,
            handle: '.drag-handle-improved',
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            dragClass: 'sortable-drag',
            forceFallback: true,

            onStart: function(evt) {
                console.log('🎯 Drag started:', evt.oldIndex);
            },

            onEnd: function(evt) {
                console.log('🎯 Drag ended:', evt.oldIndex, '→', evt.newIndex);

                if (evt.oldIndex !== evt.newIndex) {
                    const movedItem = links.splice(evt.oldIndex, 1)[0];
                    links.splice(evt.newIndex, 0, movedItem);

                    links.forEach((link, index) => {
                        link.order = index;
                    });

                    renderLinks();
                    updatePreview();

                    triggerAutoSave(); // 이슈 #5: 개선된 자동저장

                    showToast('📌 링크 순서 변경됨', 1500);
                }
            },

            onMove: function(evt) {
                return evt.related.className.indexOf('link-item-improved') !== -1;
            }
        });

        console.log('✅ Sortable initialized with', links.length, 'links');
    } catch (error) {
        console.error('❌ Sortable initialization error:', error);
    }
}

// ============================================
// URL 메타데이터 가져오기
// ============================================

async function fetchLinkMetadata(linkId) {
    const link = links.find(l => l.id === linkId);
    if (!link) {
        alert('⚠️ 링크를 찾을 수 없습니다.');
        return;
    }

    if (!link.url || link.url.trim() === '') {
        alert('⚠️ 먼저 URL을 입력해주세요.');
        return;
    }

    try {
        new URL(link.url);
    } catch {
        alert('⚠️ 올바른 URL 형식이 아닙니다.\n예: https://example.com');
        return;
    }

    const button = document.querySelector(`[data-fetch-btn="${linkId}"]`);
    const originalHTML = button ? button.innerHTML : '';

    if (button) {
        button.innerHTML = '<span class="metadata-loading"></span> <span>가져오는 중...</span>';
        button.disabled = true;
    }

    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            throw new Error('CSRF 토큰을 찾을 수 없습니다. 페이지를 새로고침해주세요.');
        }

        const response = await fetch('/api/fetch-metadata', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken.content
            },
            body: JSON.stringify({ url: link.url })
        });

        if (!response.ok) {
            const errorData = await response.json().catch(() => ({}));
            throw new Error(errorData.message || '메타데이터를 가져올 수 없습니다.');
        }

        const data = await response.json();
        console.log('✅ Metadata fetched:', data);

        let updated = false;

        if (data.title && !link.title) {
            link.title = data.title;
            updated = true;
        }
        if (data.description && !link.description) {
            link.description = data.description;
            updated = true;
        }
        if (data.image && !link.thumbnail) {
            link.thumbnailUrl = data.image;
            updated = true;
        }
        if (data.icon && !link.icon) {
            link.icon = data.icon;
            updated = true;
        }

        if (updated) {
            renderLinks();
            updatePreview();

            triggerAutoSave(); // 이슈 #5

            showToast('✅ 링크 정보 자동 입력 완료!');
        } else {
            showToast('ℹ️ 가져올 새로운 정보 없음', 1500);
        }

    } catch (error) {
        console.error('❌ Fetch metadata error:', error);
        alert('❌ ' + error.message);
    } finally {
        if (button) {
            button.innerHTML = originalHTML || '🔄 자동 입력';
            button.disabled = false;
        }
    }
}

// ============================================
// 링크 관리
// ============================================

function addLink() {
    const newLink = {
        id: linkIdCounter++,
        title: '',
        url: '',
        type: 'button',
        thumbnail: null,
        thumbnailUrl: null,
        description: '',
        order: links.length,
        icon: 'link',
        price: '',
        sale_price: '',
        currency: 'KRW',
        contact_type: 'phone'
    };

    links.push(newLink);
    renderLinks();
    updatePreview();

    console.log('✅ Link added:', newLink);

    triggerAutoSave(); // 이슈 #5
}

function removeLink(linkId) {
    if (!confirm('이 링크를 삭제하시겠습니까?')) {
        return;
    }

    links = links.filter(l => l.id !== linkId);

    links.forEach((link, index) => {
        link.order = index;
    });

    renderLinks();
    updatePreview();

    showToast('🗑️ 링크 삭제됨', 1500);

    triggerAutoSave(); // 이슈 #5
}

function renderLinks() {
    const container = document.getElementById('linksContainer');
    if (!container) return;

    container.innerHTML = '';

    if (links.length === 0) {
        container.innerHTML = `
            <div class="text-center py-12 bg-gray-50 border-2 border-dashed border-gray-300 rounded-xl">
                <p class="text-gray-400 text-lg">🔗</p>
                <p class="text-gray-500 mt-2">링크를 추가해주세요</p>
            </div>
        `;
        return;
    }

    links.forEach((link, index) => {
        const linkItem = createLinkItem(link, index);
        container.appendChild(linkItem);
    });

    setTimeout(() => {
        initSortable();
    }, 100);
}

function createLinkItem(link, index) {
    const div = document.createElement('div');
    div.className = 'link-item-improved mb-4';
    div.setAttribute('data-link-id', link.id);

    // 타입별 추가 옵션 HTML
    let typeSpecificOptions = '';

    if (link.type === 'social') {
        typeSpecificOptions = `
            <div class="mt-4 p-3 bg-purple-50 border border-purple-200 rounded-lg">
                <label class="block text-xs font-medium text-gray-700 mb-2">소셜 아이콘</label>
                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                    onchange="updateLinkField(${link.id}, 'icon', this.value)">
                    <option value="link" ${link.icon === 'link' ? 'selected' : ''}>🔗 링크</option>
                    <option value="instagram" ${link.icon === 'instagram' ? 'selected' : ''}>📷 인스타그램</option>
                    <option value="youtube" ${link.icon === 'youtube' ? 'selected' : ''}>🎥 유튜브</option>
                    <option value="tiktok" ${link.icon === 'tiktok' ? 'selected' : ''}>🎵 틱톡</option>
                    <option value="twitter" ${link.icon === 'twitter' ? 'selected' : ''}>🐦 트위터</option>
                    <option value="facebook" ${link.icon === 'facebook' ? 'selected' : ''}>👥 페이스북</option>
                    <option value="linkedin" ${link.icon === 'linkedin' ? 'selected' : ''}>💼 링크드인</option>
                    <option value="github" ${link.icon === 'github' ? 'selected' : ''}>💻 깃허브</option>
                </select>
                <p class="text-xs text-gray-500 mt-2">✨ 소셜 링크는 자동으로 브랜드 컬러가 적용됩니다</p>
            </div>
        `;
    } else if (link.type === 'product') {
        typeSpecificOptions = `
            <div class="mt-4 p-3 bg-green-50 border border-green-200 rounded-lg space-y-3">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">정가</label>
                        <input type="number" placeholder="10000"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                            value="${link.price || ''}"
                            onchange="updateLinkField(${link.id}, 'price', this.value)">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">판매가</label>
                        <input type="number" placeholder="8000"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                            value="${link.sale_price || ''}"
                            onchange="updateLinkField(${link.id}, 'sale_price', this.value)">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">통화</label>
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                        onchange="updateLinkField(${link.id}, 'currency', this.value)">
                        <option value="KRW" ${link.currency === 'KRW' ? 'selected' : ''}>원 (₩)</option>
                        <option value="USD" ${link.currency === 'USD' ? 'selected' : ''}>달러 ($)</option>
                        <option value="EUR" ${link.currency === 'EUR' ? 'selected' : ''}>유로 (€)</option>
                    </select>
                </div>
            </div>
        `;
    } else if (link.type === 'contact') {
        typeSpecificOptions = `
            <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                <label class="block text-xs font-medium text-gray-700 mb-2">연락 수단</label>
                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                    onchange="updateLinkField(${link.id}, 'contact_type', this.value)">
                    <option value="phone" ${link.contact_type === 'phone' ? 'selected' : ''}>📞 전화</option>
                    <option value="email" ${link.contact_type === 'email' ? 'selected' : ''}>✉️ 이메일</option>
                    <option value="kakao" ${link.contact_type === 'kakao' ? 'selected' : ''}>💬 카카오톡</option>
                    <option value="telegram" ${link.contact_type === 'telegram' ? 'selected' : ''}>✈️ 텔레그램</option>
                    <option value="whatsapp" ${link.contact_type === 'whatsapp' ? 'selected' : ''}>📱 왓츠앱</option>
                </select>
            </div>
        `;
    } else if (link.type === 'card') {
        typeSpecificOptions = `
            <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                <p class="text-xs text-yellow-800">
                    💡 <strong>카드형:</strong> 썸네일 이미지와 설명이 함께 표시됩니다.
                </p>
            </div>
        `;
    }

    div.innerHTML = `
        <div class="link-card">
            <div class="link-card-header">
                <div class="drag-handle-improved" title="드래그하여 순서 변경">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                        <circle cx="9" cy="6" r="1.5"/>
                        <circle cx="9" cy="12" r="1.5"/>
                        <circle cx="9" cy="18" r="1.5"/>
                        <circle cx="15" cy="6" r="1.5"/>
                        <circle cx="15" cy="12" r="1.5"/>
                        <circle cx="15" cy="18" r="1.5"/>
                    </svg>
                </div>

                <div class="link-badge">${index + 1}</div>

                <div class="flex-1 min-w-0 px-2">
                    <p class="text-sm font-medium text-gray-700 truncate">
                        ${link.title || `새 링크 ${index + 1}`}
                    </p>
                    ${link.url ? `<p class="text-xs text-gray-500 truncate">${link.url}</p>` : ''}
                </div>

                <button type="button" onclick="toggleLinkExpand(${link.id})"
                    class="link-toggle-btn" title="펼치기/접기">
                    <svg class="w-5 h-5 transform transition-transform" data-toggle-icon="${link.id}" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>

                <button type="button" onclick="removeLink(${link.id})"
                    class="link-delete-btn" title="삭제">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </div>

            <div class="link-card-body" data-link-body="${link.id}">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">링크 타입</label>
                        <select class="link-type-select"
                            onchange="updateLinkField(${link.id}, 'type', this.value); renderLinks();">
                            <option value="button" ${link.type === 'button' ? 'selected' : ''}>🔘 기본 버튼</option>
                            <option value="social" ${link.type === 'social' ? 'selected' : ''}>📱 소셜 링크</option>
                            <option value="card" ${link.type === 'card' ? 'selected' : ''}>🃏 카드형</option>
                            <option value="product" ${link.type === 'product' ? 'selected' : ''}>🛍️ 상품 판매</option>
                            <option value="contact" ${link.type === 'contact' ? 'selected' : ''}>📞 연락처</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">URL *</label>
                        <div class="relative">
                            <input type="url"
                                class="link-url-input pr-10"
                                placeholder="https://example.com"
                                value="${link.url || ''}"
                                onchange="updateLinkField(${link.id}, 'url', this.value)">

                            <div class="absolute right-2 top-1/2 -translate-y-1/2 group">
                                <button type="button"
                                    class="auto-fill-btn"
                                    data-fetch-btn="${link.id}"
                                    onclick="fetchLinkMetadata(${link.id})"
                                    title="URL에서 자동으로 제목과 설명 가져오기">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                </button>
                                <div class="tooltip">
                                    🔄 URL에서 제목·설명 자동 입력
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">제목 *</label>
                        <input type="text"
                            class="link-input"
                            placeholder="링크 제목"
                            value="${link.title || ''}"
                            onchange="updateLinkField(${link.id}, 'title', this.value); renderLinks();">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">설명 (선택)</label>
                        <textarea
                            class="link-textarea"
                            rows="2"
                            placeholder="링크 설명을 입력하세요"
                            onchange="updateLinkField(${link.id}, 'description', this.value)">${link.description || ''}</textarea>
                    </div>

                    ${typeSpecificOptions}

                    <input type="hidden" name="links[${index}][title]" value="${link.title || ''}">
                    <input type="hidden" name="links[${index}][url]" value="${link.url || ''}">
                    <input type="hidden" name="links[${index}][type]" value="${link.type || 'button'}">
                    <input type="hidden" name="links[${index}][description]" value="${link.description || ''}">
                    <input type="hidden" name="links[${index}][order]" value="${index}">
                </div>
            </div>
        </div>
    `;

    return div;
}

function toggleLinkExpand(linkId) {
    const body = document.querySelector(`[data-link-body="${linkId}"]`);
    const icon = document.querySelector(`[data-toggle-icon="${linkId}"]`);

    if (body && icon) {
        body.classList.toggle('collapsed');
        icon.classList.toggle('rotate-180');
    }
}

function updateLinkField(linkId, field, value) {
    const link = links.find(l => l.id === linkId);
    if (link) {
        link[field] = value;

        if (field === 'type') {
            updatePreview();
        } else if (field === 'title' || field === 'url') {
            renderLinks();
            updatePreview();
        } else {
            updatePreview();
        }

        triggerAutoSave(); // 이슈 #5
    }
}

// ============================================
// 프리뷰 업데이트 (이슈 #2, #3, #4 해결)
// ============================================

function updatePreview() {
    const preview = document.getElementById('preview');
    const mobilePreview = document.getElementById('mobilePreview');

    if (!preview) return;

    try {
        const name = document.getElementById('name')?.value || '이름을 입력하세요';
        const bio = document.getElementById('bio')?.value || '';

        // 이슈 #2: 이름과 소개 색상 개별 적용
        const nameColor = document.getElementById('name_color')?.value || '#FFFFFF';
        const bioColor = document.getElementById('bio_color')?.value || '#FFFFFF';

        const profileLayout = document.querySelector('input[name="profile_layout"]:checked')?.value || 'large';
        const backgroundType = document.querySelector('input[name="background_type"]:checked')?.value || 'solid';

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

        const textSize = document.getElementById('text_size')?.value || 'medium';
        const textWeight = document.getElementById('text_weight')?.value || '700';

        const animationEntrance = document.getElementById('animation_entrance')?.value || 'fade';
        const animationSpeed = document.getElementById('animation_speed')?.value || 'normal';

        const globalButtonStyle = document.querySelector('input[name="global_button_style"]:checked')?.value || 'rounded';
        const globalHoverEffect = document.querySelector('input[name="global_hover_effect"]:checked')?.value || 'scale';

        // 이슈 #4: 버튼 색상
        let buttonColor = '#FFFFFF';
        const checkedButtonColor = document.querySelector('input[name="button_color"]:checked');
        const customButtonColor = document.getElementById('customButtonColor');

        if (checkedButtonColor) {
            buttonColor = checkedButtonColor.value;
        } else if (customButtonColor && customButtonColor.value) {
            buttonColor = customButtonColor.value;
        }

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

        const textSizeClass = textSize === 'small' ? 'text-sm' : textSize === 'large' ? 'text-3xl' : 'text-2xl';

        const profileImageSrc = imageData.profile || PLACEHOLDER_PROFILE;
        const coverBgColor = document.getElementById('cover_bg_color')?.value || '#3B82F6';
        const coverImageSrc = imageData.cover;

        const bannerRadius = document.getElementById('banner_radius')?.value || 24;
        const bannerHeight = document.getElementById('banner_height')?.value || 128;
        const PROFILE_FIXED_OFFSET = 80;
        const bannerProfileOverlap = Math.max(bannerHeight - PROFILE_FIXED_OFFSET, 0);

        const fontFamily = getCurrentFont();

        if (fontFamily && fontFamily !== 'Pretendard' && fontFamily !== 'CustomFont') {
            loadGoogleFont(fontFamily);
        }

        const fontFamilyStyle = fontFamily.includes(' ') ? `'${fontFamily}'` : fontFamily;
        const fontStyle = `font-family: ${fontFamilyStyle}, sans-serif;`;

        let profileHTML = '';
        if (profileLayout === 'large') {
            profileHTML = `
                <div class="text-center mb-8 animate-${animationEntrance} animate-${animationSpeed}">
                    <img src="${profileImageSrc}"
                        alt="${name}"
                        onerror="this.src='${PLACEHOLDER_PROFILE}'"
                        class="w-24 h-24 rounded-full mx-auto mb-4 border-4 border-white shadow-lg object-cover">
                    <h1 class="${textSizeClass} mb-2 drop-shadow-lg" style="color: ${nameColor}; font-weight: ${textWeight}; ${fontStyle}">${name}</h1>
                    ${bio ? `<p class="opacity-90 text-sm md:text-base" style="color: ${bioColor}; ${fontStyle}">${bio}</p>` : ''}
                </div>
            `;
        } else if (profileLayout === 'small') {
            profileHTML = `
                <div class="flex items-center gap-4 mb-8 animate-${animationEntrance} animate-${animationSpeed}">
                    <img src="${profileImageSrc}"
                        alt="${name}"
                        onerror="this.src='${PLACEHOLDER_PROFILE}'"
                        class="w-16 h-16 rounded-full border-2 border-white shadow-lg object-cover flex-shrink-0">
                    <div class="min-w-0">
                        <h1 class="${textSizeClass} truncate" style="color: ${nameColor}; font-weight: ${textWeight}; ${fontStyle}">${name}</h1>
                        ${bio ? `<p class="text-sm opacity-90 truncate" style="color: ${bioColor}; ${fontStyle}">${bio}</p>` : ''}
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

                    <div class="relative px-6 animate-${animationEntrance} animate-${animationSpeed}" style="margin-top: -${bannerProfileOverlap}px;">
                        <img src="${profileImageSrc}"
                            alt="${name}"
                            onerror="this.src='${PLACEHOLDER_PROFILE}'"
                            class="w-20 h-20 rounded-full border-4 border-white shadow-lg mb-3 object-cover">
                        <h1 class="${textSizeClass} drop-shadow-lg" style="color: ${nameColor}; font-weight: ${textWeight}; ${fontStyle}">${name}</h1>
                        ${bio ? `<p class="opacity-90 text-sm" style="color: ${bioColor}; ${fontStyle}">${bio}</p>` : ''}
                    </div>
                </div>
            `;
        }

        const buttonStyleClass = globalButtonStyle === 'square' ? 'rounded-none' :
                                 globalButtonStyle === 'pill' ? 'rounded-full' : 'rounded-xl';

        // 이슈 #3: 발광 효과 강화
        const hoverEffectClass = globalHoverEffect === 'none' ? '' :
                                globalHoverEffect === 'scale' ? 'hover:scale-105' :
                                globalHoverEffect === 'lift' ? 'hover:-translate-y-1 hover:shadow-xl' :
                                globalHoverEffect === 'glow' ? 'hover:shadow-[0_0_40px_rgba(59,130,246,0.8)]' : '';

        // 타입별 렌더링 (이슈 #4: 소셜 브랜드 컬러 적용)
        let linksHTML = '';
        links.forEach((link, index) => {
            if (link.title && link.url) {
                const animationDelay = `animation-delay: ${index * 0.1}s;`;

                if (link.type === 'button') {
                    // 기본 버튼 - 선택된 색상 적용
                    const isWhite = buttonColor === '#FFFFFF' || buttonColor.toLowerCase() === '#fff';
                    const textColorClass = isWhite ? 'text-gray-900' : 'text-white';

                    linksHTML += `
                        <a href="${link.url}"
                            class="block w-full px-6 py-4 ${buttonStyleClass} font-medium text-center shadow-lg transition-all ${hoverEffectClass} animate-${animationEntrance} animate-${animationSpeed} ${textColorClass}"
                            style="${animationDelay} ${fontStyle} background-color: ${buttonColor};">
                            ${link.title}
                        </a>
                    `;
                } else if (link.type === 'social') {
                    // 소셜 링크 - 브랜드 컬러 자동 적용 (이슈 #4)
                    const iconMap = {
                        'instagram': '📷',
                        'youtube': '🎥',
                        'tiktok': '🎵',
                        'twitter': '🐦',
                        'facebook': '👥',
                        'linkedin': '💼',
                        'github': '💻',
                        'link': '🔗'
                    };
                    const icon = iconMap[link.icon] || '🔗';
                    const socialColor = SOCIAL_COLORS[link.icon] || SOCIAL_COLORS['link'];

                    // 틱톡은 흰 텍스트, 나머지는 흰 텍스트
                    const socialTextColor = link.icon === 'tiktok' ? 'text-white' : 'text-white';

                    linksHTML += `
                        <a href="${link.url}"
                            class="flex items-center gap-3 w-full px-6 py-4 ${buttonStyleClass} font-medium shadow-lg transition-all ${hoverEffectClass} animate-${animationEntrance} animate-${animationSpeed} ${socialTextColor}"
                            style="${animationDelay} ${fontStyle} background: ${socialColor};">
                            <span class="text-2xl">${icon}</span>
                            <span>${link.title}</span>
                        </a>
                    `;
                } else if (link.type === 'card') {
                    // 카드형 - 선택된 색상 적용
                    const isWhite = buttonColor === '#FFFFFF' || buttonColor.toLowerCase() === '#fff';
                    const cardBg = isWhite ? 'bg-white' : '';
                    const cardTextColor = isWhite ? 'text-gray-900' : 'text-white';
                    const cardDescColor = isWhite ? 'text-gray-600' : 'text-gray-200';

                    linksHTML += `
                        <a href="${link.url}"
                            class="block w-full p-4 ${cardBg} text-left ${buttonStyleClass} shadow-lg transition-all ${hoverEffectClass} animate-${animationEntrance} animate-${animationSpeed}"
                            style="${animationDelay} ${fontStyle} ${!isWhite ? `background-color: ${buttonColor};` : ''}">
                            <h3 class="font-bold text-lg ${cardTextColor} mb-1">${link.title}</h3>
                            ${link.description ? `<p class="text-sm ${cardDescColor}">${link.description}</p>` : ''}
                        </a>
                    `;
                } else if (link.type === 'product') {
                    // 상품 판매
                    let priceHTML = '';
                    if (link.sale_price) {
                        const currencySymbol = link.currency === 'KRW' ? '₩' : link.currency === 'USD' ? '$' : '€';
                        priceHTML = `
                            <div class="flex items-center gap-2">
                                ${link.price ? `<span class="text-sm text-gray-400 line-through">${currencySymbol}${parseInt(link.price).toLocaleString()}</span>` : ''}
                                <span class="text-lg font-bold text-red-600">${currencySymbol}${parseInt(link.sale_price).toLocaleString()}</span>
                            </div>
                        `;
                    } else if (link.price) {
                        const currencySymbol = link.currency === 'KRW' ? '₩' : link.currency === 'USD' ? '$' : '€';
                        priceHTML = `<span class="text-lg font-bold text-gray-900">${currencySymbol}${parseInt(link.price).toLocaleString()}</span>`;
                    }

                    linksHTML += `
                        <a href="${link.url}"
                            class="block w-full p-4 bg-gradient-to-br from-green-50 to-emerald-50 border-2 border-green-200 ${buttonStyleClass} shadow-lg transition-all ${hoverEffectClass} animate-${animationEntrance} animate-${animationSpeed}"
                            style="${animationDelay} ${fontStyle}">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="font-bold text-gray-900">${link.title}</h3>
                                    ${priceHTML}
                                </div>
                                <span class="text-2xl">🛍️</span>
                            </div>
                        </a>
                    `;
                } else if (link.type === 'contact') {
                    // 연락처
                    const contactIconMap = {
                        'phone': '📞',
                        'email': '✉️',
                        'kakao': '💬',
                        'telegram': '✈️',
                        'whatsapp': '📱'
                    };
                    const contactIcon = contactIconMap[link.contact_type] || '📞';

                    linksHTML += `
                        <a href="${link.url}"
                            class="flex items-center gap-3 w-full px-6 py-4 bg-gradient-to-r from-blue-500 to-cyan-500 text-white ${buttonStyleClass} font-medium shadow-lg transition-all ${hoverEffectClass} animate-${animationEntrance} animate-${animationSpeed}"
                            style="${animationDelay} ${fontStyle}">
                            <span class="text-2xl">${contactIcon}</span>
                            <span>${link.title}</span>
                        </a>
                    `;
                }
            }
        });

        const previewHTML = `
            <div class="relative w-full h-full" style="${backgroundStyle}${backgroundStyle ? ' ' : ''}${fontStyle}">
                ${backgroundHTML}
                <div class="relative z-10 p-6 overflow-y-auto h-full">
                    ${profileHTML}
                    <div class="space-y-3">
                        ${linksHTML || `<p class="text-center opacity-70 text-sm" style="color: ${nameColor}; ${fontStyle}">링크를 추가하세요</p>`}
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

// ============================================
// 폰트 관련
// ============================================

function getCurrentFont() {
    const fontSelect = document.getElementById('font_family');

    if (!fontSelect) {
        console.warn('⚠️ font_family element not found! Using default Pretendard');
        return 'Pretendard';
    }

    const fontValue = fontSelect.value;

    if (fontValue === 'custom') {
        return 'CustomFont';
    }

    return fontValue || 'Pretendard';
}

function loadGoogleFont(fontName) {
    if (!fontName || fontName === 'Pretendard' || fontName === 'custom') {
        return;
    }

    if (loadedFonts.has(fontName)) {
        console.log('✅ Font already loaded:', fontName);
        return;
    }

    try {
        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = `https://fonts.googleapis.com/css2?family=${fontName.replace(/ /g, '+')}:wght@300;400;500;600;700;800;900&display=swap`;

        link.onload = function() {
            console.log('✅ Font loaded successfully:', fontName);
            loadedFonts.add(fontName);
        };

        link.onerror = function() {
            console.error('❌ Font load failed:', fontName);
        };

        document.head.appendChild(link);
        console.log('📤 Font link added to head:', link.href);

    } catch (error) {
        console.error('❌ Error loading font:', fontName, error);
    }
}

// ============================================
// 이슈 #5: 개선된 자동저장 (가장 중요!)
// ============================================

function triggerAutoSave() {
    // 기존 타이머 취소
    if (autoSaveTimer) {
        clearTimeout(autoSaveTimer);
    }

    // 60초 후 자동저장 (이전 3초 → 60초)
    autoSaveTimer = setTimeout(() => {
        autoSave(true); // silent 모드
    }, 3000); // 60초

    console.log('⏱️ Auto-save scheduled in 60 seconds');
}

function showToast(message, duration = 2000) {
    const isDesktop = window.innerWidth >= 1024;

    if (message.includes('저장') && !isDesktop) {
        return;
    }

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

// ============================================
// 폼 초기화
// ============================================

function initLinks() {
    const addButton = document.querySelector('button[onclick="addLink()"]');
    if (addButton) {
        console.log('✅ Add link button found');
    }

    renderLinks();
}

function initMobilePreview() {
    const previewButton = document.getElementById('mobilePreviewBtn');
    const previewModal = document.getElementById('mobilePreviewModal');
    const closeButton = document.getElementById('closePreview');

    if (!previewModal) {
        console.warn('⚠️ Mobile preview modal not found');
        return;
    }

    if (previewButton) {
        previewButton.addEventListener('click', function() {
            previewModal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            updatePreview();
        });
    }

    if (closeButton) {
        closeButton.addEventListener('click', function() {
            previewModal.classList.add('hidden');
            document.body.style.overflow = '';
        });
    }

    previewModal.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.add('hidden');
            document.body.style.overflow = '';
        }
    });
}

function initTabs() {
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');

    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetTab = this.getAttribute('data-tab');

            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));

            this.classList.add('active');
            const targetContent = document.getElementById(targetTab);
            if (targetContent) {
                targetContent.classList.add('active');
            }
        });
    });
}

function updateBackgroundSections(type) {
    const sections = {
        solid: document.getElementById('backgroundColorSection'),
        gradient: document.getElementById('gradientSecondarySection'),
        image: document.getElementById('backgroundImageSection'),
        video: document.getElementById('backgroundVideoSection')
    };

    const blurSection = document.getElementById('backgroundBlurSection');
    const brightnessSection = document.getElementById('backgroundBrightnessSection');

    Object.values(sections).forEach(section => {
        if (section) section.style.display = 'none';
    });

    if (blurSection) blurSection.style.display = 'none';
    if (brightnessSection) brightnessSection.style.display = 'none';

    if (type === 'solid' && sections.solid) {
        sections.solid.style.display = 'block';
    } else if (type === 'gradient' && sections.solid && sections.gradient) {
        sections.solid.style.display = 'block';
        sections.gradient.style.display = 'block';
    } else if (type === 'image' && sections.image) {
        sections.image.style.display = 'block';
        if (blurSection) blurSection.style.display = 'block';
        if (brightnessSection) brightnessSection.style.display = 'block';
    } else if (type === 'video' && sections.video) {
        sections.video.style.display = 'block';
        if (blurSection) blurSection.style.display = 'block';
        if (brightnessSection) brightnessSection.style.display = 'block';
    }
}

function toggleCoverSection(layout) {
    const coverSection = document.getElementById('coverImageSection');
    const bannerSection = document.getElementById('bannerCustomizationSection');

    if (coverSection) {
        coverSection.style.display = layout === 'banner' ? 'block' : 'none';
    }

    if (bannerSection) {
        bannerSection.style.display = layout === 'banner' ? 'block' : 'none';
    }
}

function initForm() {
    const debouncedPreview = debounce(updatePreview, 300);

    safeAddListener('name', 'input', debouncedPreview);
    safeAddListener('bio', 'input', debouncedPreview);

    // 이슈 #2: 이름/소개 색상
    safeAddListener('name_color', 'input', debounce(updatePreview, 100));
    safeAddListener('bio_color', 'input', debounce(updatePreview, 100));

    safeAddListener('profile_image', 'change', handleProfileImage);

    document.querySelectorAll('input[name="profile_layout"]').forEach(radio => {
        radio.addEventListener('change', function() {
            toggleCoverSection(this.value);
            updatePreview();
        });
    });

    safeAddListener('cover_image', 'change', handleCoverImage);
    safeAddListener('cover_bg_color', 'change', updatePreview);

    safeAddListener('banner_radius', 'input', function() {
        const value = this.value;
        const display = document.getElementById('bannerRadiusValue');
        if (display) display.textContent = value;
        debounce(updatePreview, 100)();
    });

    safeAddListener('banner_height', 'input', function() {
        const value = this.value;
        const display = document.getElementById('bannerHeightValue');
        if (display) display.textContent = value;
        debounce(updatePreview, 100)();
    });

    safeAddListener('font_family', 'change', function() {
        const fontValue = this.value;
        const customUpload = document.getElementById('customFontUpload');
        const fontPreview = document.getElementById('fontPreview');

        if (customUpload) {
            customUpload.style.display = fontValue === 'custom' ? 'block' : 'none';
        }

        if (fontPreview) {
            const fontFamilyStyle = fontValue.includes(' ') ? `'${fontValue}'` : fontValue;
            fontPreview.style.fontFamily = fontValue === 'custom' ? 'CustomFont' : `${fontFamilyStyle}, sans-serif`;
        }

        if (fontValue !== 'Pretendard' && fontValue !== 'custom') {
            loadGoogleFont(fontValue);
        }

        updatePreview();
    });

    document.querySelectorAll('input[name="background_type"]').forEach(radio => {
        radio.addEventListener('change', function() {
            updateBackgroundSections(this.value);
            updatePreview();
        });
    });

    document.querySelectorAll('input[name="background_color"]').forEach(radio => {
        radio.addEventListener('change', updatePreview);
    });

    document.querySelectorAll('input[name="global_button_style"]').forEach(radio => {
        radio.addEventListener('change', updatePreview);
    });

    document.querySelectorAll('input[name="global_hover_effect"]').forEach(radio => {
        radio.addEventListener('change', updatePreview);
    });

    // 이슈 #4: 버튼 색상
    document.querySelectorAll('input[name="button_color"]').forEach(radio => {
        radio.addEventListener('change', updatePreview);
    });

    safeAddListener('customButtonColor', 'input', debounce(function() {
        document.querySelectorAll('input[name="button_color"]').forEach(r => {
            r.checked = false;
        });
        updatePreview();
    }, 150));

    safeAddListener('customBackgroundColor', 'input', debounce(function() {
        document.querySelectorAll('input[name="background_color"]').forEach(r => {
            r.checked = false;
        });

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
    }, 150));

    safeAddListener('background_secondary_color', 'change', updatePreview, 'input[name="background_secondary_color"]');
    safeAddListener('background_image', 'change', handleBackgroundImage);

    safeAddListener('background_blur', 'input', function() {
        const value = this.value;
        const display = document.getElementById('blurValue');
        if (display) display.textContent = value;
        debounce(updatePreview, 100)();
    });

    safeAddListener('background_brightness', 'input', function() {
        const value = this.value;
        const display = document.getElementById('brightnessValue');
        if (display) display.textContent = value;
        debounce(updatePreview, 100)();
    });

    safeAddListener('background_video_url', 'input', debounce(updatePreview, 500));
    safeAddListener('background_video_file', 'change', handleBackgroundVideo);

    safeAddListener('text_size', 'change', updatePreview);
    safeAddListener('text_weight', 'change', updatePreview);

    safeAddListener('animation_entrance', 'change', updatePreview);
    safeAddListener('animation_speed', 'change', updatePreview);

    const initialFont = document.getElementById('font_family')?.value;
    if (initialFont && initialFont !== 'Pretendard' && initialFont !== 'custom') {
        loadGoogleFont(initialFont);
    }
}

// ============================================
// 이미지 처리
// ============================================

function handleProfileImage(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            imageData.profile = e.target.result;
            const preview = document.getElementById('profileImagePreview');
            if (preview) {
                preview.src = e.target.result;
            }
            updatePreview();
        };
        reader.readAsDataURL(file);
    }
}

function handleCoverImage(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            imageData.cover = e.target.result;
            updatePreview();
        };
        reader.readAsDataURL(file);
    }
}

function handleBackgroundImage(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            imageData.background = e.target.result;
            updatePreview();
        };
        reader.readAsDataURL(file);
    }
}

function handleBackgroundVideo(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            imageData.backgroundVideo = e.target.result;
            updatePreview();
        };
        reader.readAsDataURL(file);
    }
}

// ============================================
// DOM 로드 완료 시 초기화
// ============================================

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

        const initialBackgroundType = document.querySelector('input[name="background_type"]:checked')?.value || 'solid';
        updateBackgroundSections(initialBackgroundType);
        console.log('✅ Background sections initialized:', initialBackgroundType);

        const initialProfileLayout = document.querySelector('input[name="profile_layout"]:checked')?.value || 'large';
        toggleCoverSection(initialProfileLayout);
        console.log('✅ Cover section initialized:', initialProfileLayout);

        updatePreview();
        console.log('✅ Preview updated');

        addLink();
        console.log('✅ First link added');

        console.log('🎉 All initialization complete!');
    } catch (error) {
        console.error('❌ Initialization error:', error);
    }
});
