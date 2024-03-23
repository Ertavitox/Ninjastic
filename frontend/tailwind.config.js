/** @type {import('tailwindcss').Config} */

const themes = require('./themes')

export default {
  content: [
    "./index.html",
    "./src/**/*.{vue,js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      colors: {
        primary: themes.red,
        secondary: themes.green,
        neutral: themes.base
      }
    },
  },
  plugins: [],
}

