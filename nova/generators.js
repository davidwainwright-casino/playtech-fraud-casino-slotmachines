const omit = require('lodash/omit')
const twColors = require('tailwindcss/colors')

const toRGBString = hexCode => {
  if (hexCode.startsWith('#')) {
    let hex = hexCode.replace('#', '')

    if (hex.length === 3) {
      hex = `${hex[0]}${hex[0]}${hex[1]}${hex[1]}${hex[2]}${hex[2]}`
    }

    const r = parseInt(hex.substring(0, 2), 16)
    const g = parseInt(hex.substring(2, 4), 16)
    const b = parseInt(hex.substring(4, 6), 16)

    return `${r}, ${g}, ${b}`
  }

  return hexCode
}

const colors = { primary: twColors.sky, ...twColors, gray: twColors.zinc }

const except = omit(colors, [
  'lightBlue',
  'warmGray',
  'trueGray',
  'coolGray',
  'blueGray',
])

function generateRootCSSVars() {
  return Object.fromEntries(
    Object.entries(except)
      .map(([key, value]) => {
        if (typeof value === 'string') {
          return [[`--colors-${key}`, toRGBString(value)]]
        }

        return Object.entries(value).map(([shade, color]) => {
          return [`--colors-${key}-${shade}`, toRGBString(color)]
        })
      })
      .flat(1)
  )
}

function generateTailwindColors() {
  return Object.fromEntries(
    Object.entries(except).map(([key, value]) => {
      if (typeof value === 'string') {
        return [`${key}`, value]
      }

      return [
        key,
        Object.fromEntries(
          Object.entries(value).map(([shade]) => {
            return [`${shade}`, `rgba(var(--colors-${key}-${shade}))`]
          })
        ),
      ]
    })
  )
}

module.exports = {
  generateRootCSSVars,
  generateTailwindColors,
}
