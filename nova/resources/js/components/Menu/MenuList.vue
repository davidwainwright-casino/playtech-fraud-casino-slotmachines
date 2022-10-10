<template>
  <div class="sidebar-list">
    <menu-item :key="item.key" v-for="item in item.items" :item="item" />
  </div>
</template>

<script>
export default {
  props: ['item'],
}
</script>
