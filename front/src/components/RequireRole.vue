<script setup>
import { onMounted } from 'vue';
import { useRouter } from 'vue-router';
import useUser from '../hooks/useUser';
import { getUserRole } from '../utils';
import { APP_ROUTES } from '../utils/constants';

const router = useRouter()
const user = useUser()
const props = defineProps({
    role: String
})

onMounted(() => {
    if (props.role != getUserRole(user).value) {
        router.push({ name: APP_ROUTES.login, replace: true })
    }
})
</script>

<template>
    <slot></slot>
</template>