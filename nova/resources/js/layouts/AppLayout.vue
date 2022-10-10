<template>
  <div id="nova">
    <MainHeader />

    <!-- Content -->
    <div data-testid="content">
      <div
        class="hidden lg:block lg:absolute left-0 bottom-0 lg:top-[56px] lg:bottom-auto w-60 px-3 py-5"
      >
        <MainMenu />
      </div>

      <div class="p-4 md:py-8 md:px-12 lg:ml-60">
        <FadeTransition>
          <slot />
        </FadeTransition>

        <Footer />
      </div>
    </div>
  </div>
</template>

<script>
import MainHeader from '@/layouts/MainHeader'
import Footer from '@/layouts/Footer'

export default {
  components: {
    MainHeader,
    Footer,
  },

  mounted() {
    Nova.$on('error', this.handleError)
    Nova.$on('token-expired', this.handleTokenExpired)
  },

  beforeUnmount() {
    Nova.$off('error', this.handleError)
    Nova.$off('token-expired', this.handleTokenExpired)
  },

  methods: {
    handleError(message) {
      Nova.error(message)
    },

    handleTokenExpired() {
      // @TODO require Nova._createToast() to support action with link.
      Nova.$toasted.show(this.__('Sorry, your session has expired.'), {
        action: {
          onClick: () => Nova.redirectToLogin(),
          text: this.__('Reload'),
        },
        duration: null,
        type: 'error',
      })

      setTimeout(() => {
        Nova.redirectToLogin()
      }, 5000)
    },
  },
}
</script>
