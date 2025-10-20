<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'LinkKit - 모든 링크를 하나로')</title>

    <!-- Pretendard 폰트 -->
    <link rel="stylesheet" as="style" crossorigin href="https://cdn.jsdelivr.net/gh/orioncactus/pretendard@v1.3.9/dist/web/static/pretendard.min.css" />

    <!-- Vite로 CSS, JS 로드 -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-pretendard bg-gray-50">
    @yield('content')
</body>
</html>
