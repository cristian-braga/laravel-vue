<template>
  <div v-if="loading" class="preloader d-flex align-items-center justify-content-center">
    <span class="loader"></span>
  </div>
</template>

<script setup>
defineProps({
  loading: {
    type: Boolean,
    default: false,
    required: true
  }
});
</script>

<style scoped>
.preloader {
  background-color: #000;
  opacity: 95%;
}

.loader {
  border: 2px solid;
  border-color: transparent #fff;
  width: 48px;
  height: 48px;
  border-radius: 50%;
  display: inline-block;
  position: relative;
  box-sizing: border-box;
  animation: rotation 2s linear infinite;
}
.loader::after {
  content: "";
  box-sizing: border-box;
  position: absolute;
  left: 50%;
  top: 50%;
  border: 24px solid;
  border-color: transparent rgba(255, 255, 255, 0.15);
  border-radius: 50%;
  transform: translate(-50%, -50%);
}

@keyframes rotation {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}
</style>