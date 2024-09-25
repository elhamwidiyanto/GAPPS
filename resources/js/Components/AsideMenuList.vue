<script setup>
import AsideMenuItem from '@/Components/AsideMenuItem.vue'
import { mdiLogout, mdiClose } from '@mdi/js'
import { router } from '@inertiajs/vue3'
import { computed } from 'vue'

defineProps({
  isDropdownList: Boolean,
  menu: {
    type: Object,
    default: () => {}
  }
})

const emit = defineEmits(['menu-click'])

const menuClick = (event, item) => {
  emit('menu-click', event, item)
}

const logoutItem = computed(() => ({
  name: 'Logout',
  icon: mdiLogout,
  color: 'info',
  link: '#'
}))

const logoutItemClick = () => {
  router.post(route('logout'))
}
</script>

<template>
  <ul>
    <AsideMenuItem
      v-for="(item, index) in menu"
      :key="index"
      :item="item"
      :is-dropdown-list="isDropdownList"
      @menu-click="menuClick"
    />
    <!-- <AsideMenuItem
      :item="logoutItem"
      @menu-click="logoutItemClick"
    /> -->
  </ul>
</template>
