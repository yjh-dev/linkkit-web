<!-- resources/views/layouts/app.blade.php -->

<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'LinkKit - 모든 링크를 하나로')</title>

    <!-- Pretendard 폰트 -->
    <link rel="stylesheet" as="style" crossorigin
        href="https://cdn.jsdelivr.net/gh/orioncactus/pretendard@v1.3.9/dist/web/static/pretendard.min.css" />

    <!-- 👇 여기 추가! Google Fonts 프리로드 -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@400;500;600;700&family=Nanum+Gothic:wght@400;700&family=Nanum+Myeongjo:wght@400;700&family=Gowun+Batang:wght@400;700&family=Roboto:wght@400;500;700&family=Open+Sans:wght@400;600;700&family=Lato:wght@400;700&family=Montserrat:wght@400;600;700&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Vite로 CSS, JS 로드 -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-pretendard bg-gray-50">
    {{-- ✨ Toast 알림 --}}
    @if (session('success'))
        <div id="toast"
            class="fixed top-8 right-8 bg-green-500 text-white px-6 py-4 rounded-xl shadow-2xl flex items-center gap-3 animate-slide-in" style="z-index: 9999">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="font-semibold">{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div id="toast"
            class="fixed top-8 right-8 z-50 bg-red-500 text-white px-6 py-4 rounded-xl shadow-2xl flex items-center gap-3 animate-slide-in" style="z-index: 9999">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="font-semibold">{{ session('error') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div id="toast"
            class="fixed top-8 right-8 z-50 bg-red-500 text-white px-6 py-4 rounded-xl shadow-2xl animate-slide-in max-w-md" style="z-index: 9999">
            <div class="flex items-start gap-3">
                <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    @foreach ($errors->all() as $error)
                        <p class="font-semibold">{{ $error }}</p>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <script>
        // Toast 자동 숨김
        const toast = document.getElementById('toast');
        if (toast) {
            setTimeout(() => {
                toast.style.animation = 'slide-out 0.5s ease-out forwards';
                setTimeout(() => toast.remove(), 500);
            }, 3000);
        }
    </script>

    @yield('content')
    @stack('scripts')
</body>

</html>
