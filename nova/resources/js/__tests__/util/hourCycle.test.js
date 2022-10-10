import { default as hourCycle } from '@/util/hourCycle'

it('can uses 12 hour cycles', () => {
  expect(hourCycle('en-US')).toEqual(12)
  expect(hourCycle('ms-MY')).toEqual(12)
  expect(hourCycle('ko-KR')).toEqual(12)
  expect(hourCycle('ar-EG')).toEqual(12)
})

it('can uses 24 hour cycles', () => {
  expect(hourCycle('en-GB')).toEqual(24)
  expect(hourCycle('ja-JP')).toEqual(24)
})
