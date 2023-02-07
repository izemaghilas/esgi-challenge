<script setup>
import NavBar from '../components/dashboard/admin/NavBar.vue';
</script>

<template>
    <v-container class="admin-container">
        <NavBar />
        <v-main style="height: 100%;">
            <router-view></router-view>
        </v-main>
    </v-container>
</template>

<style scoped>
.admin-container {
    display: flex;
    flex-direction: row;
    margin-top: 40px;
}
</style>