<script setup>
import Loader from '../components/Loader.vue';
import useStoreActions from '../hooks/useStoreActions';
import { useRouter } from 'vue-router';
import { APP_ROUTES } from '../utils/constants';
import { onMounted } from 'vue';

const router = useRouter()
const storeActions = useStoreActions()

onMounted(()=>{
    storeActions.logout()
    router.push({ name: APP_ROUTES.home, replace: true })
})

</script>

<template>
    <Loader />
</template>