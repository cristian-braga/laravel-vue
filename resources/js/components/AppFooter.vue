<template>
  <footer class="main-footer">
    <div class="float-right d-none d-sm-inline">Anything you want</div>
    <strong>Copyright &copy; {{ new Date().getFullYear() }} <a href="#">{{ settings?.app_name }}</a>.</strong>
    All rights reserved.
  </footer>
</template>

<script setup>
defineProps({
  settings: Object
});
</script>
