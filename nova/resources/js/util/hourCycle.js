export default function (locale) {
  let hourCycle = Intl.DateTimeFormat(locale, {
    hour: 'numeric',
  }).resolvedOptions().hourCycle

  if (hourCycle == 'h23' || hourCycle == 'h24') {
    return 24
  }

  return 12
}
