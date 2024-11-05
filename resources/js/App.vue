<template>
  <div v-if="authUserStore.user.name !== ''" :class="`wrapper ${currentThemeMode}`" id="app">
    <AppNavbar />

    <SidebarLeft />

    <div class="content-wrapper">
      <RouterView></RouterView>
    </div>

    <SidebarRight />

    <AppFooter />
  </div>
  <div v-else :class="`login-page ${currentThemeMode}`">
    <RouterView></RouterView>
  </div>
</template>

<script setup>
import AppNavbar from "./components/AppNavbar.vue";
import SidebarLeft from "./components/SidebarLeft.vue";
import SidebarRight from "./components/SidebarRight.vue";
import AppFooter from "./components/AppFooter.vue";
import { useAuthUserStore } from "./stores/AuthUserStore";
import { useSettingStore } from "./stores/SettingStore";
import { computed } from "vue";

const authUserStore = useAuthUserStore();
const settingStore = useSettingStore();

const currentThemeMode = computed(() => {
  return settingStore.theme === 'dark' ? 'dark-mode' : '';
});
</script>