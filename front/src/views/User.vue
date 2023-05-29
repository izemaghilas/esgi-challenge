<script setup>
import { inject } from 'vue';
import RequireRole from '../components/RequireRole.vue';
import { APP_ROUTES, ROLES } from '../utils/constants';
import PurchaseList from '../components/PurchaseList.vue';

const { state } = inject("store");
</script>

<template>
    <RequireRole :role="ROLES.user.value">
        <v-navigation-drawer permanent>
            <v-sheet class="d-flex flex-row align-center px-2 py-3" color="grey-lighten-4">
                <v-avatar size="30px">
                    <v-img
                        src="https://www.pngmart.com/files/22/User-Avatar-Profile-Download-PNG-Isolated-Image.png"></v-img>
                </v-avatar>
                <span class="ml-2 text-md font-weight-bold">{{ `${state.user.lastname.toUpperCase()}
                                    ${state.user.firstname.charAt(0).toUpperCase() + state.user.firstname.slice(1)}` }}</span>
            </v-sheet>
            <v-divider></v-divider>
            <v-list>
                <v-list-item>
                    <v-btn class="w-100" color="primary" :to="APP_ROUTES.dashboard.views.user">
                        Cours achetés
                    </v-btn>
                </v-list-item>
            </v-list>
        </v-navigation-drawer>
        <PurchaseList />
    </RequireRole>
</template>