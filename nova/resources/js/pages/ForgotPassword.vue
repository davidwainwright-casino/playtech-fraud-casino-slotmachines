<template>
  <LoadingView :loading="false">
    <Head :title="__('Forgot Password')" />

    <form
      @submit.prevent="attempt"
      class="bg-white dark:bg-gray-800 shadow rounded-lg p-8 w-[25rem] mx-auto"
    >
      <h2 class="text-2xl text-center font-normal mb-6 text-90">
        {{ __('Forgot your password?') }}
      </h2>

      <DividerLine />

      <div class="mb-6">
        <label class="block mb-2" for="email">{{ __('Email Address') }}</label>
        <input
          v-model="form.email"
          class="form-control form-input form-input-bordered w-full"
          :class="{ 'form-input-border-error': form.errors.has('email') }"
          id="email"
          type="email"
          name="email"
          required=""
          autofocus=""
        />

        <HelpText class="mt-2 text-red-500" v-if="form.errors.has('email')">
          {{ form.errors.first('email') }}
        </HelpText>
      </div>

      <DefaultButton class="w-full flex justify-center" type="submit">
        {{ __('Send Password Reset Link') }}
      </DefaultButton>
    </form>
  </LoadingView>
</template>

<script>
import Auth from '@/layouts/Auth'

export default {
  layout: Auth,

  data: () => ({
    form: Nova.form({
      email: '',
    }),
  }),

  methods: {
    async attempt() {
      const { message } = await this.form.post(Nova.url('/password/email'))
      Nova.success(message)
      Nova.redirectToLogin()
    },
  },

  computed: {
    supportsPasswordReset() {
      return Nova.config('withPasswordReset')
    },

    forgotPasswordPath() {
      return Nova.config('forgotPasswordPath')
    },
  },
}
</script>
