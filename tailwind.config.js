/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          50: '#fef5ee',
          100: '#fde8d7',
          200: '#facfae',
          300: '#f7ac7a',
          400: '#f38044',
          500: '#D2691E',
          600: '#A0522D',
          700: '#7a3e1f',
          800: '#66341d',
          900: '#572e1c',
        },
      },
    },
  },
  plugins: [],
}
