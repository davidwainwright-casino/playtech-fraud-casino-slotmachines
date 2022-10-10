const defaultTheme = require('tailwindcss/defaultTheme')
const { generateRootCSSVars, generateTailwindColors } = require('./generators')

module.exports = {
  mode: 'jit',
  content: [
    './src/**/*.php',
    './src/**/*.vue',
    './resources/**/*{js,vue,blade.php}',
  ],
  darkMode: 'class', // or 'media' or 'class'
  // safelist: [
  // {
  // pattern: /^grid-cols-(?:\d)+/,
  // pattern: /grid-cols-(?:\d)+/,
  // pattern: /^(?:\w+:)?grid-cols-(?:\d)+/,
  // variants: ['sm', 'md', 'lg'],
  // },
  // ],

  theme: {
    colors: generateTailwindColors(),
    extend: {
      fontFamily: { sans: ['Nunito Sans', ...defaultTheme.fontFamily.sans] },
      fontSize: { xxs: '11px' },
      maxWidth: { xxs: '15rem' },
      minHeight: theme => theme('spacing'),
      minWidth: theme => theme('spacing'),

      spacing: {
        5: '1.25rem',
        9: '2.25rem',
        11: '2.75rem',
      },

      top: theme => theme('inset'),
      width: theme => theme('spacing'),
    },
  },
  plugins: [
    require('@tailwindcss/typography'),
    function ({ addBase }) {
      addBase({ ':root': generateRootCSSVars() })
    },
  ],
}
