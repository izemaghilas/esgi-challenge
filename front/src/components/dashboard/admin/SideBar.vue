<script setup>
import { ref } from 'vue'
import { APP_ROUTES } from '../../../utils/constants'

const { views: dashboardAdmin } = APP_ROUTES.dashboard.views.admin
const links = ref({
    [dashboardAdmin.users]: {
        label: "Utilisateurs",
        to: dashboardAdmin.users,
    },
    [dashboardAdmin.courses]: {
        label: "Cours",
        to: dashboardAdmin.courses,
    },
    [dashboardAdmin.comments]: {
        label: "Commentaires",
        to: dashboardAdmin.comments,
    }
})

</script>

<template>
    <nav>
        <router-link v-for="link in links" :key="link" class="link"
            :to="link.to" exact-active-class="link-active">
            <span>{{ link.label }}</span>
        </router-link>
    </nav>
</template>

<style scoped>
nav {
    display: flex;
    flex-direction: column;
    width: 250px;
}

.link {
    display: flex;
    flex-direction: row;
    align-items: center;
    height: 50px;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    color: #11161a;
    text-decoration: none;
}

.link span {
    margin-left: 15px;
}

.link-active {
    background-color: #d6dee4;
}
</style>