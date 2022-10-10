import InteractsWithDates from '@/mixins/InteractsWithDates'

afterAll(() => {
  delete global.Nova
})

test('it can get user timezone', () => {
  global.Nova = {
    config(key) {
      return this.appConfig[key] ?? null
    },
    appConfig: {
      timezone: 'UTC',
      userTimezone: 'Asia/Kuala_Lumpur',
    },
  }

  expect(InteractsWithDates.computed.userTimezone()).toBe('Asia/Kuala_Lumpur')
})

test('it can fallback to application timezone if user does not define timezone', () => {
  global.Nova = {
    config(key) {
      return this.appConfig[key] ?? null
    },
    appConfig: {
      timezone: 'UTC',
    },
  }

  expect(InteractsWithDates.computed.userTimezone()).toBe('UTC')
})
