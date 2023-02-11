<script setup>
import { useRoute } from "vue-router"
import { watch, ref } from 'vue'
import { APP_ROUTES } from '../../../utils/constants'

const { views: dashboardAdmin } = APP_ROUTES.dashboard.views.admin
const route = useRoute()
const links = ref({
    [dashboardAdmin.users]: {
        label: "Utilisateurs",
        to: dashboardAdmin.users,
        active: route.name === dashboardAdmin.users
    },
    [dashboardAdmin.courses]: {
        label: "Cours",
        to: dashboardAdmin.courses,
        active: route.name === dashboardAdmin.courses
    },
    [dashboardAdmin.comments]: {
        label: "Commentaires",
        to: dashboardAdmin.comments,
        active: route.name === dashboardAdmin.comments
    }
})

watch(() => route.name, (newRouteName, oldRouteName) => {
    links.value[oldRouteName].active = false
    links.value[newRouteName].active = true
})

</script>

<template>
    <nav>
        <router-link v-for="link in links" :key="link" :class="`link ${link.active ? 'link-active' : ''}`"
            :to="link.to">
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