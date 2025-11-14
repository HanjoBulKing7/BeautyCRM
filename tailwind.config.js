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
        beige: '#F5F1ED',
      },
      fontFamily: {
        montas: ['Montserrat', 'sans-serif'], // Si usas Montserrat
      },
    },
  },
  plugins: [],
}