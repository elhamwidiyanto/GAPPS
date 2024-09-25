<script setup>
import { containerMaxW } from "@/config.js";
import BaseLevel from "@/Components/BaseLevel.vue";
import JustboilLogo from "@/Components/JustboilLogo.vue";

const year = new Date().getFullYear();
</script>

<template>
  <footer class="py-2 px-6" :class="containerMaxW">
    <BaseLevel>
      <div class="text-center md:text-left text-slate-950 dark:text-slate-50">
        <b
          >&copy;{{ year }},
          <a href="#">IT Dept. pangansari.com</a>.</b
        >
        <slot />
      </div>
      <div class="md:py-2">
        <a href="#">
          <!-- <JustboilLogo class="w-auto h-8 md:h-6" /> -->
        </a>
      </div>
    </BaseLevel>
  </footer>
</template>
