<script setup>
import RequireRole from '../components/RequireRole.vue';
import { ROLES } from '../utils/constants';


</script>

<template>
    <v-app>
        <v-main style="height: 100%;">
            <RequireRole :role="ROLES.admin.value">
                <router-view></router-view>
            </RequireRole>
        </v-main>
    </v-app>
</template>