import singularOrPlural from '@/util/singularOrPlural'

test('it can return correct inflector results', () => {
  expect(singularOrPlural(0, 'hour')).toBe('hours')
  expect(singularOrPlural(1, 'hour')).toBe('hour')
  expect(singularOrPlural(1.23, 'hour')).toBe('hours')
  expect(singularOrPlural(40, 'hour')).toBe('hours')
  expect(singularOrPlural(40, 'Bouqueté')).toBe('Bouquetés')
})

test('it does ignore when suffix is a symbol', () => {
  expect(singularOrPlural(40, '%')).toBe('%')
  expect(singularOrPlural(40, '!')).toBe('!')
})
