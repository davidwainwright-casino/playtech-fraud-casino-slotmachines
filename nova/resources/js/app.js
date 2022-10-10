import Localization from '@/mixins/Localization'
import { setupAxios } from '@/util/axios'
import { setupNumbro } from '@/util/numbro'
import { setupInertia } from '@/util/inertia'
import url from '@/util/url'
import { createInertiaApp, Head, Link } from '@inertiajs/inertia-vue3'
import { Inertia } from '@inertiajs/inertia'
import NProgress from 'nprogress'
import { registerViews } from './components'
import { registerFields } from './fields'
import Mousetrap from 'mousetrap'
import Form from 'form-backend-validation'
import { createNovaStore } from './store'
import resourceStore from './store/resources'
import FloatingVue from 'floating-vue'
import find from 'lodash/find'
import isNil from 'lodash/isNil'
import fromPairs from 'lodash/fromPairs'
import isString from 'lodash/isString'
import Toasted from 'toastedjs'
import Emitter from 'tiny-emitter'
import Layout from '@/layouts/AppLayout'
import CodeMirror from 'codemirror'
import 'codemirror/mode/markdown/markdown'
import 'codemirror/mode/javascript/javascript'
import 'codemirror/mode/php/php'
import 'codemirror/mode/ruby/ruby'
import 'codemirror/mode/shell/shell'
import 'codemirror/mode/sass/sass'
import 'codemirror/mode/yaml/yaml'
import 'codemirror/mode/yaml-frontmatter/yaml-frontmatter'
import 'codemirror/mode/nginx/nginx'
import 'codemirror/mode/xml/xml'
import 'codemirror/mode/vue/vue'
import 'codemirror/mode/dockerfile/dockerfile'
import 'codemirror/keymap/vim'
import 'codemirror/mode/sql/sql'
import 'codemirror/mode/twig/twig'
import 'codemirror/mode/htmlmixed/htmlmixed'
import { ColorTranslator } from 'colortranslator'

import 'floating-vue/dist/style.css'

const { parseColor } = require('tailwindcss/lib/util/color')

CodeMirror.defineMode('htmltwig', function (config, parserConfig) {
  return CodeMirror.overlayMode(
    CodeMirror.getMode(config, parserConfig.backdrop || 'text/html'),
    CodeMirror.getMode(config, 'twig')
  )
})

const emitter = new Emitter()

window.createNovaApp = config => new Nova(config)
window.Vue = require('vue')

const { createApp, h } = window.Vue

class Nova {
  constructor(config) {
    this.bootingCallbacks = []
    this.bootedCallbacks = []
    this.appConfig = config
    this.useShortcuts = true

    this.pages = {
      'Nova.Attach': require('@/pages/Attach').default,
      'Nova.Create': require('@/pages/Create').default,
      'Nova.Dashboard': require('@/pages/Dashboard').default,
      'Nova.Detail': require('@/pages/Detail').default,
      'Nova.Error': require('@/pages/AppError').default,
      'Nova.Error403': require('@/pages/Error403').default,
      'Nova.Error404': require('@/pages/Error404').default,
      'Nova.ForgotPassword': require('@/pages/ForgotPassword').default,
      'Nova.Index': require('@/pages/Index').default,
      'Nova.Lens': require('@/pages/Lens').default,
      'Nova.Login': require('@/pages/Login').default,
      'Nova.Replicate': require('@/pages/Replicate').default,
      'Nova.ResetPassword': require('@/pages/ResetPassword').default,
      'Nova.Update': require('@/pages/Update').default,
      'Nova.UpdateAttached': require('@/pages/UpdateAttached').default,
    }

    this.$toasted = new Toasted({
      theme: 'nova',
      position: config.rtlEnabled ? 'bottom-left' : 'bottom-right',
      duration: 6000,
    })
    this.$progress = NProgress
  }

  /**
   * Register a callback to be called before Nova starts. This is used to bootstrap
   * addons, tools, custom fields, or anything else Nova needs
   */
  booting(callback) {
    this.bootingCallbacks.push(callback)
  }

  /**
   * Execute all of the booting callbacks.
   */
  boot() {
    this.store = createNovaStore()

    this.bootingCallbacks.forEach(callback => callback(this.app, this.store))
    this.bootingCallbacks = []
  }

  booted(callback) {
    // this.bootedCallbacks.push(callback)
    callback(this.app, this.store)
  }

  async countdown() {
    this.log('Initiating Nova countdown...')

    const appName = this.config('appName')

    await createInertiaApp({
      title: title => (!title ? appName : `${appName} - ${title}`),
      resolve: name => {
        const page = !isNil(this.pages[name])
          ? this.pages[name]
          : require('@/pages/Error404').default

        page.layout = page.layout || Layout

        return page
      },
      setup: ({ el, App, props, plugin }) => {
        this.mountTo = el
        this.app = createApp({ render: () => h(App, props) })

        // TODO: Only needed until Vue 3.3 https://vuejs.org/guide/components/provide-inject.html#working-with-reactivity
        this.app.config.unwrapInjectedRef = true

        this.app.use(plugin)
        this.app.use(FloatingVue, {
          themes: {
            Nova: {
              $extend: 'tooltip',
              triggers: ['click'],
              autoHide: true,
              placement: 'bottom',
              html: true,
            },
          },
        })
      },
    })
  }

  /**
   * Start the Nova app by calling each of the tool's callbacks and then creating
   * the underlying Vue instance.
   */
  liftOff() {
    this.log('We have lift off!')

    this.boot()

    if (this.config('notificationCenterEnabled')) {
      this.notificationPollingInterval = setInterval(() => {
        if (document.hasFocus()) {
          this.$emit('refresh-notifications')
        }
      }, this.config('notificationPollingInterval'))
    }

    this.registerStoreModules()

    this.app.mixin(Localization)

    setupInertia()

    document.addEventListener('inertia:before', () => {
      ;(async () => {
        this.log('Syncing Inertia props to the store...')
        await this.store.dispatch('assignPropsFromInertia')
      })()
    })

    document.addEventListener('inertia:navigate', () => {
      ;(async () => {
        this.log('Syncing Inertia props to the store...')
        await this.store.dispatch('assignPropsFromInertia')
      })()
    })

    this.app.mixin({
      methods: {
        $url: (path, parameters) => this.url(path, parameters),
      },
    })

    this.component('Link', Link)
    this.component('InertiaLink', Link)
    this.component('Head', Head)

    registerViews(this)
    registerFields(this)

    this.app.mount(this.mountTo)

    let mousetrapDefaultStopCallback = Mousetrap.prototype.stopCallback

    Mousetrap.prototype.stopCallback = (e, element, combo) => {
      if (!this.useShortcuts) {
        return true
      }

      return mousetrapDefaultStopCallback.call(this, e, element, combo)
    }

    Mousetrap.init()

    this.applyTheme()

    this.log('All systems go...')
  }

  config(key) {
    return this.appConfig[key]
  }

  /**
   * Return a form object configured with Nova's preconfigured axios instance.
   *
   * @param {object} data
   */
  form(data) {
    return new Form(data, {
      http: this.request(),
    })
  }

  /**
   * Return an axios instance configured to make requests to Nova's API
   * and handle certain response codes.
   */
  request(options) {
    let axios = setupAxios()

    if (options !== undefined) {
      return axios(options)
    }

    return axios
  }

  /**
   * Get the URL from base Nova prefix.
   */
  url(path, parameters) {
    if (path === '/') {
      path = this.config('initialPath')
    }

    return url(this.config('base'), path, parameters)
  }

  /**
   * Register a listener on Nova's built-in event bus
   */
  $on(...args) {
    emitter.on(...args)
  }

  /**
   * Register a one-time listener on the event bus
   */
  $once(...args) {
    emitter.once(...args)
  }

  /**
   * Unregister an listener on the event bus
   */
  $off(...args) {
    emitter.off(...args)
  }

  /**
   * Emit an event on the event bus
   */
  $emit(...args) {
    emitter.emit(...args)
  }

  /**
   * Determine if Nova is missing the requested resource with the given uri key
   */
  missingResource(uriKey) {
    return find(this.config('resources'), r => r.uriKey == uriKey) == undefined
  }

  /**
   * Register a keyboard shortcut.
   */
  addShortcut(keys, callback) {
    Mousetrap.bind(keys, callback)
  }

  /**
   * Unbind a keyboard shortcut.
   */
  disableShortcut(keys) {
    Mousetrap.unbind(keys)
  }

  /**
   * Pause all keyboard shortcuts.
   */
  pauseShortcuts() {
    this.useShortcuts = false
  }

  /**
   * Resume all keyboard shortcuts.
   */
  resumeShortcuts() {
    this.useShortcuts = true
  }

  /**
   * Register the built-in Vuex modules for each resource
   */
  registerStoreModules() {
    this.app.use(this.store)

    this.config('resources').forEach(resource => {
      this.store.registerModule(resource.uriKey, resourceStore)
    })
  }

  /**
   * Register Inertia component.
   */
  inertia(name, component) {
    this.pages[name] = component
  }

  /**
   * Register a custom Vue component.
   */
  component(name, component) {
    if (isNil(this.app._context.components[name])) {
      this.app.component(name, component)
    }
  }

  /**
   * Show an error message to the user.
   *
   * @param {string} message
   */
  info(message) {
    this.$toasted.show(message, { type: 'info' })
  }

  /**
   * Show an error message to the user.
   *
   * @param {string} message
   */
  error(message) {
    this.$toasted.show(message, { type: 'error' })
  }

  /**
   * Show a success message to the user.
   *
   * @param {string} message
   */
  success(message) {
    this.$toasted.show(message, { type: 'success' })
  }

  /**
   * Show a warning message to the user.
   *
   * @param {string} message
   */
  warning(message) {
    this.$toasted.show(message, { type: 'warning' })
  }

  /**
   * Format a number using numbro.js for consistent number formatting.
   */
  formatNumber(number, format) {
    const numbro = setupNumbro(
      document.querySelector('meta[name="locale"]').content
    )
    const num = numbro(number)

    if (format !== undefined) {
      return num.format(format)
    }

    return num.format()
  }

  /**
   * Log a message to the console with the NOVA prefix
   *
   * @param message
   * @param type
   */
  log(message, type = 'log') {
    console[type](`[NOVA]`, message)
  }

  /**
   * Redirect to login path.
   */
  redirectToLogin() {
    const url =
      !this.config('withAuthentication') && this.config('customLoginPath')
        ? this.config('customLoginPath')
        : this.url('/login')

    this.visit({
      remote: true,
      url,
    })
  }

  /**
   * Visit page using Inertia visit or window.location for remote.
   */
  visit(path, options) {
    if (isString(path)) {
      Inertia.visit(this.url(path), options || {})
      return
    }

    if (isString(path.url) && path.hasOwnProperty('remote')) {
      if (path.remote == true) {
        window.location = path.url
        return
      }

      Inertia.visit(path.url, options || {})
    }
  }

  applyTheme() {
    const brandColors = this.config('brandColors')

    if (Object.keys(brandColors).length > 0) {
      const style = document.createElement('style')

      // Handle converting any non-RGB user strings into valid RGB strings.
      // This allows the user to specify any color in HSL, RGB, and RGBA
      // format, and we'll convert it to the proper format for them.
      let css = Object.keys(brandColors).reduce((carry, v) => {
        let colorValue = brandColors[v]
        let validColor = parseColor(colorValue)

        if (validColor) {
          let parsedColor = parseColor(
            ColorTranslator.toRGBA(convertColor(validColor))
          )

          let rgbaString = `${parsedColor.color.join(' ')} / ${
            parsedColor.alpha
          }`

          return carry + `\n  --colors-primary-${v}: ${rgbaString};`
        }

        return carry + `\n  --colors-primary-${v}: ${colorValue};`
      }, '')

      style.innerHTML = `:root {${css}\n}`

      document.head.append(style)
    }
  }
}

function convertColor(parsedColor) {
  let color = fromPairs(
    Array.from(parsedColor.mode).map((v, i) => {
      return [v, parsedColor.color[i]]
    })
  )

  if (parsedColor.alpha !== undefined) {
    color.a = parsedColor.alpha
  }

  return color
}
