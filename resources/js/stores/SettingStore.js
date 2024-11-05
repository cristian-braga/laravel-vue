import { useStorage } from "@vueuse/core";
import axios from "axios";
import { defineStore } from "pinia";
import { ref } from "vue";

export const useSettingStore = defineStore('SettingStore', () => {
  const setting = ref({
    app_name: ''
  });

  const theme = useStorage('SettingStore:theme', ref('light'));

  const getSetting = async () => {
    await axios.get('/api/settings')
      .then((response) => {
        setting.value = response.data;
      });
  };

  const changeTheme = () => {
    theme.value = theme.value === 'light' ? 'dark' : 'light';
  };

  return { setting, getSetting, theme, changeTheme };
});
