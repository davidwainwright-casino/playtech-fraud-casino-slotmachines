<template>
  <div>
    <Head :title="__('Reset Password')" />

    <form
      @submit.prevent="attempt"
      class="bg-white dark:bg-gray-800 shadow rounded-lg p-8 w-[25rem] mx-auto"
    >
      <h2 class="text-2xl text-center font-normal mb-6 text-90">
        {{ __('Reset Password') }}
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

      <div class="mb-6">
        <label class="block mb-2" for="password">{{ __('Password') }}</label>
        <input
          v-model="form.password"
          class="form-control form-input form-input-bordered w-full"
          :class="{ 'form-input-border-error': form.errors.has('password') }"
          id="password"
          type="password"
          name="password"
          required=""
        />

        <HelpText class="mt-2 text-red-500" v-if="form.errors.has('password')">
          {{ form.errors.first('password') }}
        </HelpText>
      </div>

      <div class="mb-6">
        <label class="block mb-2" for="password_confirmation">{{
          __('Confirm Password')
        }}</label>
        <input
          v-model="form.password_confirmation"
          class="form-control form-input form-input-bordered w-full"
          :class="{
            'form-input-border-error': form.errors.has('password_confirmation'),
          }"
          id="password_confirmation"
          type="password"
          name="password_confirmation"
          required=""
        />

        <HelpText
          class="mt-2 text-red-500"
          v-if="form.errors.has('password_confirmation')"
        >
          {{ form.errors.first('password_confirmation') }}
        </HelpText>
      </div>

      <DefaultButton class="w-full flex justify-center" type="submit">
        {{ __('Reset Password') }}
      </DefaultButton>
    </form>
  </div>
</template>

<script>
import Cookies from 'js-cookie'
import Auth from '@/layouts/Auth'

export default {
  layout: Auth,

  props: ['token'],

  data() {
    return {
      form: Nova.form({
        email: '',
        password: '',
        password_confirmation: '',
        token: this.token,
      }),
    }
  },

  methods: {
    async attempt() {
      const { message } = await this.form.post(Nova.url('/password/reset'))
      Nova.success(message)
      Cookies.set('token', Math.random().toString(36), { expires: 365 })

      Nova.visit('/')
    },
  },
}
</script>
