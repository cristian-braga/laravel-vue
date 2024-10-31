<template>
  <div class="wrapper" id="app">
    <AppNavbar />

    <SidebarLeft :user="user" :settings="settings" />

    <div class="content-wrapper">
      <RouterView></RouterView>
    </div>

    <SidebarRight />

    <AppFooter :settings="settings" />
  </div>
</template>

<script setup>
import axios from "axios";
import { onMounted, ref } from "vue";
import AppNavbar from "./components/AppNavbar.vue";
import SidebarLeft from "./components/SidebarLeft.vue";
import SidebarRight from "./components/SidebarRight.vue";
import AppFooter from "./components/AppFooter.vue";
import { useAuthUserStore } from "./stores/AuthUserStore";

const authUserStore = useAuthUserStore();
authUserStore.getAuthUser();

const settings = ref(null);
const user = ref(null);

const fetchSettings = () => {
  axios.get('/api/settings')
    .then((response) => {
      settings.value = response.data;
    });
};

const fetchAuthUser = () => {
  axios.get('/api/profile')
    .then((response) => {
      user.value = response.data;
    });
};

onMounted(() => {
  fetchSettings();
  fetchAuthUser();
});
</script>