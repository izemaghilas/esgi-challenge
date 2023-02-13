<script setup>
import SideBar from '../components/dashboard/admin/SideBar.vue'
</script>

<template>
    <v-container class="admin-container">
        <SideBar />
        <v-container class="admin-main-view">
            <router-view></router-view>
        </v-container>
    </v-container>
</template>

<style scoped>
.admin-container {
    display: flex;
    flex-direction: row;
    margin-top: 40px;
    column-gap: 40px;
}

.admin-main-view {
    display: flex;
    flex-direction: column;
    padding: 0;
}
</style>