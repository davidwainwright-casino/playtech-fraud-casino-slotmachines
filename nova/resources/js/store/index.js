import { createStore } from 'vuex'
import nova from './nova'
import notifications from './notifications'

export function createNovaStore() {
  return createStore({
    ...nova,
    modules: {
      nova: {
        namespaced: true,
        modules: {
          notifications,
        },
      },
    },
  })
}
