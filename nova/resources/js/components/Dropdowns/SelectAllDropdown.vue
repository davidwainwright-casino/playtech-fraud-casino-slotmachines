<template>
  <Dropdown
    :handle-internal-clicks="false"
    dusk="select-all-dropdown"
    placement="bottom-start"
  >
    <span class="sr-only">{{ __('Select All Dropdown') }}</span>
    <DropdownTrigger
      class="h-9 px-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded"
    >
      <FakeCheckbox
        :aria-label="__('Select this page')"
        :checked="selectAllAndSelectAllMatchingChecked"
        :indeterminate="selectAllIndeterminate"
      />
    </DropdownTrigger>

    <template #menu>
      <DropdownMenu direction="ltr" width="250">
        <div class="p-4">
          <ul>
            <li class="flex items-center mb-4">
              <CheckboxWithLabel
                :checked="selectAllChecked"
                dusk="select-all-button"
                @input="$emit('toggle-select-all')"
              >
                {{ __('Select this page') }}
              </CheckboxWithLabel>
            </li>

            <li class="flex items-center">
              <CheckboxWithLabel
                :checked="selectAllMatchingChecked"
                dusk="select-all-matching-button"
                @input="$emit('toggle-select-all-matching')"
              >
                <span class="mr-1">
                  {{ __('Select all') }}
                  <CircleBadge>
                    {{ allMatchingResourceCount }}
                  </CircleBadge>
                </span>
              </CheckboxWithLabel>
            </li>
          </ul>
        </div>
      </DropdownMenu>
    </template>
  </Dropdown>
</template>

<script>
export default {
  emits: ['toggle-select-all', 'toggle-select-all-matching'],

  inject: [
    'selectAllChecked',
    'selectAllMatchingChecked',
    'selectAllAndSelectAllMatchingChecked',
    'selectAllIndeterminate',
  ],

  props: {
    allMatchingResourceCount: {
      type: Number,
      default: 0,
    },
  },
}
</script>
