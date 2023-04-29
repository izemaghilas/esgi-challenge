<script setup>
import { inject, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { getUserRole } from '../utils';
import { APP_ROUTES } from '../utils/constants';

const { state } = inject("store");
const router = useRouter()
const props = defineProps({
    role: String
})

onMounted(() => {
    if (state.user == null || props.role != getUserRole(state.user).value) {
        router.push({ name: APP_ROUTES.login, replace: true })
    }
})
</script>

<template>
    <slot></slot>
</template>