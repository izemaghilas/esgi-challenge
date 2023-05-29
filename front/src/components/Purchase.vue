<script setup>
import { APP_ROUTES } from '../utils/constants';

const props = defineProps({
    purchase: Object
})
const { purchase } = props
</script>

<template>
    <router-link :to="{ name: APP_ROUTES.course, params: { id: purchase.course.id } }">
        <v-card class="d-flex flex-row align-center w-100 mt-5 px-4">
            <span class="ml-2 py-1 font-weight-bold">{{ purchase.course.title }}</span>
            <v-spacer></v-spacer>
            <v-icon icon="mdi-chevron-right" size="50"></v-icon>
        </v-card>
    </router-link>
</template>

<style scoped>
a {
    text-decoration: none;
}
</style>