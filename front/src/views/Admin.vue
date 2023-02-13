<script setup>
import SideBar from '../components/dashboard/admin/SideBar.vue'
import RequireRole from '../components/RequireRole.vue';
import { ROLES } from '../utils/constants';
</script>

<template>
    <RequireRole :role="ROLES.admin.value">
        <v-container class="admin-container">
            <SideBar />
            <v-container class="admin-main-view">
                <router-view></router-view>
            </v-container>
        </v-container>
    </RequireRole>
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