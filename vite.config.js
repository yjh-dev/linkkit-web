import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'

export default defineConfig({
  plugins: [laravel({
    input: ['resources/css/app.css', 'resources/js/app.js'],
    refresh: true,
  })],
  server: {
    host: '0.0.0.0',
    port: 5173,
    // 옵션 A: 자격 증명 안 쓰면 와일드카드
    // cors: true,  // (Vite 기본은 '*' 헤더로 응답)

    // 옵션 B: 정확히 8000만 허용(쿠키 등 credentials 필요할 때)
    cors: {
      origin: 'http://58.79.17.72:8000',
      methods: ['GET', 'HEAD', 'OPTIONS'],
      allowedHeaders: ['*'],
      credentials: true,
    },
    hmr: {
      host: '58.79.17.72',
      port: 5173,
    },
  },
})
