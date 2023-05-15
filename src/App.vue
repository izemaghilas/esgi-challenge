<script setup>
import { provide } from "vue";
import store from "./store";
import Header from './components/Header.vue'

provide("store", store)
</script>

<template>
  <v-app>
    <Header />
    <router-view></router-view>
  </v-app>
</template>

<style scoped>

</style>
