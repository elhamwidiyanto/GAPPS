<script setup>
import { useLayoutStore } from '@/Stores/layout.js'
import { useStyleStore } from '@/Stores/style.js'
import NavBar from '@/Components/NavBar.vue'
import AsideMenu from '@/Components/AsideMenu.vue'
import FooterBar from '@/Components/FooterBar.vue'

const layoutAsidePadding = "xl:pl-60";

const styleStore = useStyleStore()

const layoutStore = useLayoutStore()
</script>

<template>
  <div :class="{ 'dark': styleStore.darkMode, 'overflow-hidden lg:overflow-visible': layoutStore.isAsideMobileExpanded }">
    <div
      :class="[layoutAsidePadding, { 'ml-60 lg:ml-0': layoutStore.isAsideMobileExpanded }]"
      class="pt-14 min-h-screen w-screen transition-position lg:w-auto bg-gray-100 dark:bg-slate-800 dark:text-slate-100"
    >
      <NavBar :class="[layoutAsidePadding, { 'ml-60 lg:ml-0': layoutStore.isAsideMobileExpanded }]" />
      <AsideMenu />
      <slot />
      <FooterBar />
    </div>
  </div>
</template>
