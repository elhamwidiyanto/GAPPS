<script setup>
import { usePage, router } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import { containerMaxW } from '@/config.js'
import { useMainStore } from '@/Stores/main.js'
import { useStyleStore } from '@/Stores/style.js'
import { useLayoutStore } from '@/Stores/layout.js'
import {
  mdiForwardburger,
  mdiBackburger,
  mdiClose,
  mdiDotsVertical,
  mdiMenu,
  mdiClockOutline,
  mdiCloud,
  mdiCrop,
  mdiAccount,
  mdiCogOutline,
  mdiEmail,
  mdiLogout,
  mdiGithub,
  mdiThemeLightDark
} from '@mdi/js'
import NavBarItem from '@/Components/NavBarItem.vue'
import NavBarItemLabel from '@/Components/NavBarItemLabel.vue'
import NavBarMenu from '@/Components/NavBarMenu.vue'
import BaseDivider from '@/Components/BaseDivider.vue'
import UserAvatarCurrentUser from '@/Components/UserAvatarCurrentUser.vue'
import BaseIcon from '@/Components/BaseIcon.vue'
import NavBarSearch from '@/Components/NavBarSearch.vue'

const mainStore = useMainStore()

const userName = computed(() => usePage().props.auth.user.name)

const styleStore = useStyleStore()

const toggleLightDark = () => {
  styleStore.setDarkMode()
}

const layoutStore = useLayoutStore()

const isMenuNavBarActive = ref(false)

const menuNavBarToggle = () => {
  isMenuNavBarActive.value = !isMenuNavBarActive.value
}

const menuOpenLg = () => {
  layoutStore.isAsideLgActive = true
}

const logout = () => {
  router.post(route('logout'))
}
</script>

<template>
  <nav
    class="top-0 left-0 right-0 fixed bg-gray-100 h-14 z-30 w-screen transition-position xl:pl-60 lg:w-auto dark:bg-slate-800"
  >
    <div
      class="flex lg:items-stretch"
      :class="containerMaxW"
    >
      <div class="flex-1 items-stretch flex h-14">
        <NavBarItem
          type="flex lg:hidden"
          @click.prevent="layoutStore.asideMobileToggle()"
        >
          <BaseIcon
            :path="layoutStore.isAsideMobileExpanded ? mdiBackburger : mdiForwardburger"
            size="24"
          />
        </NavBarItem>
        <NavBarItem
          type="hidden lg:flex xl:hidden"
          @click.prevent="menuOpenLg"
        >
          <BaseIcon
            :path="mdiMenu"
            size="24"
          />
        </NavBarItem>
        <NavBarItem>
          <!-- <NavBarSearch /> -->
        </NavBarItem>
      </div>
      <div class="flex-none items-stretch flex h-14 lg:hidden">
        <NavBarItem @click.prevent="menuNavBarToggle">
          <BaseIcon
            :path="isMenuNavBarActive ? mdiClose : mdiDotsVertical"
            size="24"
          />
        </NavBarItem>
      </div>
      <div
        class="absolute w-screen top-14 left-0 bg-gray-100 shadow
          lg:w-auto lg:items-stretch lg:flex lg:grow lg:static lg:border-b-0 lg:overflow-visible lg:shadow-none dark:bg-slate-800"
        :class="[isMenuNavBarActive ? 'block' : 'hidden']"
      >
        <div
          class="max-h-screen-menu overflow-y-auto lg:overflow-visible lg:flex lg:items-stretch lg:justify-end lg:ml-auto"
        >
          <!-- <NavBarMenu>
            <NavBarItemLabel
              :icon="mdiMenu"
              label="Sample menu"
            />

            <template #dropdown>
              <NavBarItem>
                <NavBarItemLabel
                  :icon="mdiClockOutline"
                  label="Item One"
                />
              </NavBarItem>
              <NavBarItem>
                <NavBarItemLabel
                  :icon="mdiCloud"
                  label="Item Two"
                />
              </NavBarItem>
              <BaseDivider nav-bar />
              <NavBarItem>
                <NavBarItemLabel
                  :icon="mdiCrop"
                  label="Item Last"
                />
              </NavBarItem>
            </template>
          </NavBarMenu> -->
          <NavBarMenu>
            <NavBarItemLabel :label="userName">
              <UserAvatarCurrentUser class="w-6 h-6 mr-3 inline-flex" />
            </NavBarItemLabel>

            <template #dropdown>
              <NavBarItem route-name="admin.account.info">
                <NavBarItemLabel
                  :icon="mdiAccount"
                  label="My Profile"
                />
              </NavBarItem>
              <!-- <NavBarItem>
                <NavBarItemLabel
                  :icon="mdiCogOutline"
                  label="Settings"
                />
              </NavBarItem> -->
              <!-- <NavBarItem>
                <NavBarItemLabel
                  :icon="mdiEmail"
                  label="Messages"
                />
              </NavBarItem> -->
              <!-- <BaseDivider nav-bar />
              <NavBarItem>
                <NavBarItemLabel
                  :icon="mdiLogout"
                  label="Log Out"
                />
              </NavBarItem> -->
            </template>
          </NavBarMenu>
          <NavBarItem
            is-desktop-icon-only
            @click.prevent="toggleLightDark"
          >
            <NavBarItemLabel
              :icon="mdiThemeLightDark"
              label="Light/Dark"
              is-desktop-icon-only
            />
          </NavBarItem>
          <!-- <NavBarItem
            href="https://github.com/balajidharma/laravel-vue-admin-panel"
            is-desktop-icon-only
          >
            <NavBarItemLabel
              :icon="mdiGithub"
              label="GitHub"
              is-desktop-icon-only
            />
          </NavBarItem> -->
          <NavBarItem
            is-desktop-icon-only
            @click="logout"
          >
            <NavBarItemLabel
              :icon="mdiLogout"
              label="Log out"
              is-desktop-icon-only
            />
          </NavBarItem>
        </div>
      </div>
    </div>
  </nav>
</template>