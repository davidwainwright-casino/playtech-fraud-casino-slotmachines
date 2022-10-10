import numbro from 'numbro'
import numbroLanguages from 'numbro/dist/languages.min'

export function setupNumbro(locale) {
  if (locale) {
    locale = locale.replace('_', '-')

    Object.values(numbroLanguages).forEach(language => {
      let name = language.languageTag

      if (locale === name || locale === name.substr(0, 2)) {
        numbro.registerLanguage(language)
      }
    })

    numbro.setLanguage(locale)
  }

  numbro.setDefaults({
    thousandSeparated: true,
  })

  return numbro
}
