<script setup>
import Header from '../components/Header.vue';
</script>

<template>
    <v-app>
        <Header />
        <v-main style="height: 100%;">
            <router-view></router-view>
        </v-main>
    </v-app>
</template>