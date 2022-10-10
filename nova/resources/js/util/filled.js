import isNil from 'lodash/isNil'

export default function filled(value) {
  return !isNil(value) && value !== ''
}
