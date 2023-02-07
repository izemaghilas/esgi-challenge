<script setup>
import APP_ROUTES from '../../../utils/routes';
const { views: dashboard } = APP_ROUTES.dashboard;
</script>

<template>
    <nav>
        <router-link class="link" :to="dashboard.admin.views.users">
            <span class="link">Utilisateurs</span>
        </router-link>
        <router-link class="link" :to="dashboard.admin.views.courses">
            <span class="link">Cours</span>
        </router-link>
        <router-link class="link" :to="dashboard.admin.views.comments">
            <span class="link">Commentaires</span>
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
</style>