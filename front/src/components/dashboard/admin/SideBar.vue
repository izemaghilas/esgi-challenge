<script setup>
import { inject, ref } from 'vue'
import { APP_ROUTES } from '../../../utils/constants'

const { state } = inject("store");
const { views: dashboardAdmin } = APP_ROUTES.dashboard.views.admin
const links = ref({
    [dashboardAdmin.courses]: {
        label: "Cours",
        to: dashboardAdmin.courses,
    },
    [dashboardAdmin.categories]: {
        label: "Catégories des cours",
        to: dashboardAdmin.categories,
    },
    [dashboardAdmin.validationRequests]: {
        label: "Demandes de validation",
        to: dashboardAdmin.validationRequests,
    },
    [dashboardAdmin.beReviewer]: {
        label: "Candidatures",
        to: dashboardAdmin.beReviewer,
    },
    [dashboardAdmin.comments]: {
        label: "Commentaires",
        to: dashboardAdmin.comments,
    },
})

</script>

<template>
    <v-navigation-drawer permanent>
        <v-sheet class="d-flex flex-row align-center px-2 py-3" color="grey-lighten-4">
            <v-avatar size="30px">
                <v-img src="https://www.pngmart.com/files/22/User-Avatar-Profile-Download-PNG-Isolated-Image.png"></v-img>
            </v-avatar>
            <span class="ml-2 text-md font-weight-bold">{{ `${state.user.lastname.toUpperCase()}
                            ${state.user.firstname.charAt(0).toUpperCase() + state.user.firstname.slice(1)}` }}</span>
        </v-sheet>
        <v-divider></v-divider>
        <v-list>
            <v-list-item v-for="link in links" :key="link">
                <v-btn class="w-100" color="primary" :to="link.to">
                    <span>{{ link.label }}</span>
                </v-btn>
            </v-list-item>
        </v-list>
    </v-navigation-drawer>
</template>