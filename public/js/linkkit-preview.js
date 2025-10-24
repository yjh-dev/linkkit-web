(function() {
    const fabBtn = document.getElementById('openPreviewFab');
    const modal = document.getElementById('previewModal');
    const closeBtn = document.getElementById('closePreviewModal');

    const homeSlot = document.getElementById('previewPanelHome');
    const modalSlot = document.getElementById('previewPanelModalSlot');
    const panel = document.getElementById('previewPanel');

    function openModal() {
        // 프리뷰 패널을 모달 슬롯으로 이동
        if (panel && modalSlot && panel.parentElement !== modalSlot) {
            modalSlot.appendChild(panel);
        }
        modal.classList.remove('hidden');
        fabBtn?.setAttribute('aria-expanded', 'true');
        // 바닥 스크롤 잠금
        document.documentElement.style.overflow = 'hidden';
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        // 프리뷰 패널을 원래 자리로 복귀
        if (panel && homeSlot && panel.parentElement !== homeSlot) {
            homeSlot.appendChild(panel);
        }
        modal.classList.add('hidden');
        fabBtn?.setAttribute('aria-expanded', 'false');
        // 스크롤 잠금 해제
        document.documentElement.style.overflow = '';
        document.body.style.overflow = '';
    }

    fabBtn?.addEventListener('click', openModal);
    closeBtn?.addEventListener('click', closeModal);
    // 배경 클릭 닫기
    modal?.addEventListener('click', (e) => {
        if (e.target === modal || e.target === modal.firstElementChild) closeModal();
    });
    // ESC 닫기
    document.addEventListener('keydown', (e) => {
        if (!modal.classList.contains('hidden') && e.key === 'Escape') closeModal();
    });
})();
/**
 * LinkKit Preview Manager
 * create.blade.php와 edit.blade.php에서 공통으로 사용하는 프리뷰 기능 모듈
 */
class LinkKitPreview {
    constructor(options = {}) {
        this.preset = options.preset || 'basic';
        this.currentColor = options.color || '#2B7FFF';
        this.currentBgType = options.bgType || 'gradient';
        this.currentBgColor = options.bgColor || '#EFF6FF';
        this.currentBgSecondaryColor = options.bgSecondaryColor || '#FFFFFF';
        this.linkIndex = options.linkIndex || 1;

        this.init();
    }

    init() {
        this.setupProfileImagePreview();
        this.setupColorPickers();
        this.setupBackgroundPickers();
        this.setupLinkManagement();
        this.updateAllPreviews();
    }

    /**
     * 프로필 이미지 미리보기
     */
    setupProfileImagePreview() {
        const profileInput = document.getElementById('profile_image');
        const profilePreview = document.getElementById('profilePreview');
        const previewProfile = document.getElementById('previewProfile');

        if (profileInput && profilePreview) {
            profileInput.addEventListener('change', (e) => {
                const file = e.target.files[0];
                if (file && file.type.startsWith('image/')) {
                    // 파일 크기 체크 (2MB)
                    if (file.size > 2 * 1024 * 1024) {
                        alert('⚠️ 이미지는 2MB 이하여야 합니다.');
                        e.target.value = '';
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = (event) => {
                        const imgHTML = `<img src="${event.target.result}" class="w-full h-full object-cover">`;
                        profilePreview.innerHTML = imgHTML;
                        if (previewProfile) {
                            previewProfile.innerHTML = imgHTML;
                            previewProfile.style.border = `4px solid ${this.currentColor}40`;
                        }
                    };
                    reader.readAsDataURL(file);
                }
            });
        }
    }

    /**
     * 색상 선택기 설정
     */
    setupColorPickers() {
        // 팔레트 라디오 버튼
        const colorRadios = document.querySelectorAll('.color-radio');
        colorRadios.forEach(radio => {
            radio.addEventListener('change', (e) => {
                if (e.target.checked) {
                    this.updateColor(e.target.value);
                }
            });
        });

        // 커스텀 색상 피커는 외부에서 호출
        window.updateColor = (color) => this.updateColor(color);
        window.updateCustomColor = (color) => this.updateCustomColor(color);
    }

    updateColor(color) {
        this.currentColor = color;

        // 팔레트 체크 해제
        document.querySelectorAll('.color-radio').forEach(radio => {
            radio.checked = false;
        });

        // 선택된 색상 체크
        const selectedRadio = document.querySelector(`.color-radio[value="${color}"]`);
        if (selectedRadio) {
            selectedRadio.checked = true;
        }

        // 커스텀 색상 피커 업데이트
        const customColorInput = document.getElementById('customColor');
        const colorValueInput = document.getElementById('colorValue');
        if (customColorInput) customColorInput.value = color;
        if (colorValueInput) colorValueInput.value = color;

        this.updateAllPreviews();
    }

    updateCustomColor(color) {
        this.currentColor = color;

        // 모든 팔레트 체크 해제
        document.querySelectorAll('.color-radio').forEach(radio => {
            radio.checked = false;
        });

        // 값 표시 업데이트
        const colorValueInput = document.getElementById('colorValue');
        if (colorValueInput) colorValueInput.value = color;

        this.updateAllPreviews();
    }

    /**
     * 배경 선택기 설정
     */
    setupBackgroundPickers() {
        // 배경 프리셋 라디오 버튼
        const bgRadios = document.querySelectorAll('.background-radio');
        bgRadios.forEach(radio => {
            radio.addEventListener('change', (e) => {
                if (e.target.checked) {
                    const type = e.target.dataset.type;
                    const color = e.target.dataset.color;
                    const secondary = e.target.dataset.secondary;
                    this.updateBackground(type, color, secondary);
                }
            });
        });

        // 커스텀 배경은 외부에서 호출
        window.updateBackground = (type, color, secondary) => this.updateBackground(type, color, secondary);
        window.updateCustomBackground = () => this.updateCustomBackground();
    }

    updateBackground(type, color, secondaryColor = null) {
        this.currentBgType = type;
        this.currentBgColor = color;
        this.currentBgSecondaryColor = secondaryColor || color;

        // hidden input 업데이트
        const bgTypeInput = document.querySelector('input[name="background_type"]');
        const bgColorInput = document.querySelector('input[name="background_color"]');
        const bgSecondaryInput = document.querySelector('input[name="background_secondary_color"]');

        if (bgTypeInput) bgTypeInput.value = type;
        if (bgColorInput) bgColorInput.value = color;
        if (bgSecondaryInput) bgSecondaryInput.value = secondaryColor || '';

        // 커스텀 입력 필드도 업데이트
        const customBgColor = document.getElementById('customBackgroundColor');
        const customBgSecondary = document.getElementById('customBackgroundSecondary');
        if (customBgColor) customBgColor.value = color;
        if (customBgSecondary) customBgSecondary.value = secondaryColor || color;

        this.updatePreview();
    }

    updateCustomBackground() {
        const type = document.querySelector('input[name="custom_bg_type"]:checked')?.value || 'solid';
        const color = document.getElementById('customBackgroundColor')?.value || this.currentBgColor;
        const secondary = document.getElementById('customBackgroundSecondary')?.value || color;

        // 모든 프리셋 체크 해제
        document.querySelectorAll('.background-radio').forEach(radio => {
            radio.checked = false;
        });

        this.updateBackground(type, color, secondary);
    }

    /**
     * 링크 관리 기능
     */
    setupLinkManagement() {
        // 링크 추가 버튼
        const addLinkBtn = document.getElementById('addLink');
        if (addLinkBtn) {
            addLinkBtn.addEventListener('click', () => this.addLink());
        }

        // 링크 삭제
        document.addEventListener('click', (e) => {
            if (e.target.closest('.remove-link')) {
                const linkItem = e.target.closest('.link-item');
                const container = document.getElementById('linksContainer');

                if (container.querySelectorAll('.link-item').length <= 1) {
                    alert('⚠️ 최소 1개의 링크는 필요합니다.');
                    return;
                }

                linkItem.remove();
                this.updateLinkNumbers();
                this.updatePreviewLinks();
            }
        });

        // 기존 링크 입력 이벤트 연결
        this.attachLinkListeners();
    }

    addLink() {
        const container = document.getElementById('linksContainer');
        const currentCount = container.querySelectorAll('.link-item').length;

        const newLink = document.createElement('div');
        newLink.className = 'link-item flex items-start gap-4 p-4 bg-gray-50 rounded-xl border-2 border-gray-200 hover:border-linkkit-blue transition-all';
        newLink.innerHTML = `
            <div class="w-10 h-10 bg-linkkit-blue rounded-lg flex items-center justify-center flex-shrink-0">
                <span class="text-white font-bold">${currentCount + 1}</span>
            </div>
            <div class="flex-1 space-y-4">
                <input
                    type="text"
                    name="links[${this.linkIndex}][title]"
                    placeholder="링크 제목 (예: 유튜브)"
                    required
                    class="link-title w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-linkkit-blue focus:border-linkkit-blue transition-all bg-white"
                >
                <input
                    type="url"
                    name="links[${this.linkIndex}][url]"
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
        `;

        container.appendChild(newLink);
        this.linkIndex++;

        this.updateLinkNumbers();
        this.updatePreviewLinks();
        this.attachLinkListeners();
    }

    updateLinkNumbers() {
        document.querySelectorAll('.link-item').forEach((item, index) => {
            const numberSpan = item.querySelector('.bg-linkkit-blue span');
            if (numberSpan) {
                numberSpan.textContent = index + 1;
            }
        });
    }

    attachLinkListeners() {
        document.querySelectorAll('.link-title, .link-url').forEach(input => {
            input.removeEventListener('input', this.updatePreviewLinks.bind(this));
            input.addEventListener('input', this.updatePreviewLinks.bind(this));
        });
    }

    /**
     * 미리보기 업데이트 (통합)
     */
    updateAllPreviews() {
        this.updatePreview();
        this.updatePreviewLinks();
    }

    updatePreview() {

        const previewContainer = document.getElementById('previewContainer');
        const previewProfile = document.getElementById('previewProfile');
        const previewName = document.getElementById('previewName');
        const previewBio = document.getElementById('previewBio');
        const previewFooter = document.getElementById('previewFooter');

        // 배경 업데이트
        if (previewContainer) {
            if (this.currentBgType === 'gradient' && this.currentBgSecondaryColor) {
                previewContainer.style.background = `linear-gradient(135deg, ${this.currentBgColor}, ${this.currentBgSecondaryColor})`;
            } else {

                previewContainer.style.background = this.currentBgColor;
            }
        }

        // 프로필 테두리 색상
        if (previewProfile) {
            previewProfile.style.border = `4px solid ${this.currentColor}40`;
        }

        // 이름 색상
        if (previewName) {
            previewName.style.color = this.currentColor;
            const nameInput = document.getElementById('name');
            if (nameInput) {
                previewName.textContent = nameInput.value || '당신의 이름';
            }
        }

        // 소개 업데이트
        if (previewBio) {
            const bioInput = document.getElementById('bio');
            if (bioInput) {
                previewBio.textContent = bioInput.value || '여기에 소개를 적어보세요';
            }
        }

        // 푸터 색상
        if (previewFooter) {
            //자식중에 span태그를 찾아서 색상 변경
            const span = previewFooter.querySelector('span');
            if (span) {
                span.style.color = this.currentColor;
            }
        }
    }

    updatePreviewLinks() {
        const previewLinksContainer = document.getElementById('previewLinks');
        const linkItems = document.querySelectorAll('.link-item');

        if (!previewLinksContainer) return;

        if (linkItems.length === 0) {
            previewLinksContainer.innerHTML = `
                <div class="text-center py-12">
                    <p class="text-gray-400">링크를 추가하면<br>여기에 표시돼요</p>
                </div>
            `;
            return;
        }

        let html = '';
        linkItems.forEach((item, index) => {
            const titleInput = item.querySelector('.link-title');
            const urlInput = item.querySelector('.link-url');

            const title = titleInput?.value.trim() || `링크 ${index + 1}`;
            const url = urlInput?.value.trim() || '';

            let urlDisplay = '';
            if (url) {
                try {
                    urlDisplay = new URL(url).host;
                } catch {
                    urlDisplay = url;
                }
            }

            // show.blade.php와 동일한 구조로 렌더링
            const bgClass = this.preset === 'basic' ? 'bg-white border-2' :
                           this.preset === 'minimal' ? 'bg-white border' :
                           'bg-gray-700 border-2';

            const textColorClass = this.preset === 'dark' ? 'text-gray-400' : 'text-gray-500';
            const titleColor = this.preset === 'dark' ? '#FFFFFF' : this.currentColor;

            html += `
                <a href="javascript:void(0)"
                    class="group block rounded-2xl p-5 transition-all transform hover:-translate-y-1 ${bgClass}"
                    style="border-color: ${this.currentColor}40;"
                    onmouseover="this.style.borderColor='${this.currentColor}'"
                    onmouseout="this.style.borderColor='${this.currentColor}40'">
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-base mb-2 transition-colors"
                                style="color: ${titleColor};">
                                ${this.escapeHtml(title)}
                            </h3>
                            <p class="text-xs truncate ${textColorClass}">
                                ${this.escapeHtml(urlDisplay) || 'URL을 입력해주세요'}
                            </p>
                        </div>
                        <div class="flex-shrink-0">
                            <svg class="w-5 h-5 transition-colors" style="color: ${this.currentColor};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </div>
                </a>
            `;
        });

        previewLinksContainer.innerHTML = html;
    }

    /**
     * 이름/소개 입력 감지 설정
     */
    setupBasicInfoListeners() {
        const nameInput = document.getElementById('name');
        const bioInput = document.getElementById('bio');

        if (nameInput) {
            nameInput.addEventListener('input', () => this.updatePreview());
        }
        if (bioInput) {
            bioInput.addEventListener('input', () => this.updatePreview());
        }
    }

    /**
     * HTML 이스케이프 (XSS 방지)
     */
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    /**
     * 폼 제출 전 검증
     */
    setupFormValidation(formId) {
        const form = document.getElementById(formId);
        if (!form) return;

        form.addEventListener('submit', (e) => {
            const name = document.getElementById('name')?.value.trim();
            const links = document.querySelectorAll('.link-item');

            if (!name) {
                e.preventDefault();
                alert('⚠️ 이름을 입력해주세요.');
                document.getElementById('name')?.focus();
                return;
            }

            if (links.length === 0) {
                e.preventDefault();
                alert('⚠️ 최소 1개의 링크를 추가해주세요.');
                return;
            }

            // 로딩 표시
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.innerHTML = '<span class="animate-spin">⏳</span> <span>처리 중...</span>';
                submitBtn.disabled = true;
            }
        });
    }
}

// 전역 함수로 노출 (blade에서 바로 사용 가능)
window.LinkKitPreview = LinkKitPreview;
