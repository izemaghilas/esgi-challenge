<script setup>
import { ref } from 'vue'
import { APP_ROUTES } from '../../../utils/constants'

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
        <v-sheet color="grey-lighten-4" class="pa-4">
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