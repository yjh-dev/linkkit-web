/** @type {import('tailwindcss').Config} */
export default {
    content: [
      "./resources/**/*.blade.php",
      "./resources/**/*.js",
      "./resources/**/*.vue",
      "./public/**/*.js",
    ],
    theme: {
      extend: {
        colors: {
          'linkkit-blue': '#2B7FFF',
        },
        fontFamily: {
          'pretendard': ['Pretendard', 'sans-serif'],
        },
      },
    },
    plugins: [],
  }
