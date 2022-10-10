<template>
  <Dropdown v-if="themeSwitcherEnabled" placement="bottom-end">
    <DropdownTrigger
      :show-arrow="false"
      class="h-10 w-10 hover:text-primary-500"
    >
      <Icon :type="themeIcon" :class="themeColor" />
    </DropdownTrigger>
    <template #menu>
      <DropdownMenu>
        <div class="flex flex-col py-1">
          <DropdownMenuItem
            as="button"
            @click.prevent="toggleLightTheme"
            class="flex themeColor-center hover:bg-gray-100 py-1"
          >
            <Icon :solid="true" type="sun" />
            <span class="ml-2">{{ __('Light') }}</span>
          </DropdownMenuItem>

          <DropdownMenuItem
            as="button"
            @click.prevent="toggleDarkTheme"
            class="flex items-center hover:bg-gray-100"
          >
            <Icon :solid="true" type="moon" />
            <span class="ml-2">{{ __('Dark') }}</span>
          </DropdownMenuItem>

          <DropdownMenuItem
            as="button"
            @click.prevent="toggleSystemTheme"
            class="flex items-center hover:bg-gray-100"
          >
            <Icon :solid="true" type="desktop-computer" />
            <span class="ml-2">{{ __('System') }}</span>
          </DropdownMenuItem>
        </div>
      </DropdownMenu>
    </template>
  </Dropdown>
</template>

<script>
export default {
  data() {
    return {
      theme: 'system',
      listener: null,
      matcher: window.matchMedia('(prefers-color-scheme: dark)'),
      themes: ['light', 'dark'],
    }
  },

  mounted() {
    if (Nova.config('themeSwitcherEnabled')) {
      if (this.themes.includes(localStorage.novaTheme)) {
        this.theme = localStorage.novaTheme
      }

      this.listener = e => {
        if (this.theme == 'system') {
          this.applyColorScheme()
        }
      }
      this.matcher.addEventListener('change', this.listener)
    } else {
      localStorage.removeItem('novaTheme')
    }
  },

  beforeUnmount() {
    if (Nova.config('themeSwitcherEnabled')) {
      this.matcher.removeEventListener('change', this.listener)
    }
  },

  watch: {
    theme(theme) {
      if (theme == 'light') {
        localStorage.novaTheme = 'light'
        document.documentElement.classList.remove('dark')
      }

      if (theme == 'dark') {
        localStorage.novaTheme = 'dark'
        document.documentElement.classList.add('dark')
      }

      if (theme == 'system') {
        localStorage.removeItem('novaTheme')
        this.applyColorScheme()
      }
    },
  },

  methods: {
    applyColorScheme() {
      console.log(Nova.config('themeSwitcherEnabled'))
      if (Nova.config('themeSwitcherEnabled')) {
        if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
          document.documentElement.classList.add('dark')
        } else {
          document.documentElement.classList.remove('dark')
        }
      }
    },

    toggleLightTheme() {
      this.theme = 'light'
    },

    toggleDarkTheme() {
      this.theme = 'dark'
    },

    toggleSystemTheme() {
      this.theme = 'system'
    },
  },

  computed: {
    themeSwitcherEnabled() {
      return Nova.config('themeSwitcherEnabled')
    },

    themeIcon() {
      return {
        light: 'sun',
        dark: 'moon',
        system: 'desktop-computer',
      }[this.theme]
    },

    themeColor() {
      return {
        light: 'text-primary-500',
        dark: 'text-primary-500',
        system: '',
      }[this.theme]
    },
  },
}
</script>
