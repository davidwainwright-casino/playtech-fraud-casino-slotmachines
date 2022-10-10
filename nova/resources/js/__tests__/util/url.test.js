import url from '@/util/url'

it('it can generate proper urls', () => {
  expect(url('nova', '/resources/users')).toEqual('nova/resources/users')
  expect(url('nova', '/resources/users', { users_per_page: 15 })).toEqual(
    'nova/resources/users?users_per_page=15'
  )
  expect(
    url('nova', '/resources/users', { search: 'nova', users_per_page: 15 })
  ).toEqual('nova/resources/users?search=nova&users_per_page=15')
  expect(url('nova', '/resources/users', { resources: [1, 2, 3] })).toEqual(
    'nova/resources/users?resources=1%2C2%2C3'
  )
})
