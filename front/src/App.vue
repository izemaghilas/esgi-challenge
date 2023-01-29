<script setup>
import { provide } from "vue";
import store from "./store";

provide("store", store);
</script>

<template>
  <router-view></router-view>
</template>

<style scoped>

</style>
