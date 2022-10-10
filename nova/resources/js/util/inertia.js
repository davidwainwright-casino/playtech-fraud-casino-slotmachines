import { Inertia } from '@inertiajs/inertia'
import { InertiaProgress } from '@inertiajs/progress'
import debounce from 'lodash/debounce'

export function setupInertia() {
  InertiaProgress.init({
    delay: 250,
    includeCSS: false,
    showSpinner: false,
  })

  const handlePopstateEvent = function (event) {
    if (this.ignoreHistoryState === false) {
      this.handlePopstateEvent(event)
    }
  }

  Inertia.ignoreHistoryState = false

  Inertia.setupEventListeners = function () {
    window.addEventListener('popstate', handlePopstateEvent.bind(Inertia))
    document.addEventListener(
      'scroll',
      debounce(Inertia.handleScrollEvent.bind(Inertia), 100),
      true
    )
  }
}
