<script setup>
import { computed } from 'vue'
import { useStyleStore } from '@/Stores/style'
import { gradientBgPurplePink, gradientBgDark, gradientBgPinkRed } from '@/colors'

const props = defineProps({
  bg: {
    type: String,
    required: true,
    validator: value => ['dark', 'purplePink', 'pinkRed'].includes(value)
  }
})

const colorClass = computed(() => {
  if (useStyleStore().darkMode) {
    return gradientBgDark
  }

  switch (props.bg) {
    case 'purplePink':
      return gradientBgPurplePink
    case 'pinkRed':
      return  gradientBgPinkRed
    case 'dark':
      return  gradientBgDark
  }
  
  return ''
})
</script>

<template>
  <!-- :class="colorClass" -->
  <div
    class="flex min-h-screen items-center justify-center bg-custom"
  >
      <slot card-class="w-11/12 md:w-7/12 lg:w-6/12 xl:w-4/12 shadow-2xl" />
  </div>
</template>

<style scoped>

.bg-custom {
    background-image: url(https://servicedesk.digipufr.com/request_files/6-adnan_humam_dhulfikar-301586923-pufr_digi-01.jpg);
    background-position: 50%;
    background-repeat: no-repeat;
    background-size: cover;
    height: 100%;
}

@media only screen and (max-width: 576px){

  .bg-custom {
    background-image: url(https://servicedesk.digipufr.com/request_files/6-adnan_humam_dhulfikar-1652411694-pufr_digi-02.jpg);
    background-position: 50%;
    background-repeat: no-repeat;
    background-size: cover;
    height: 100%;
  }
}

</style>