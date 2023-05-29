<script setup>
import { inject, onMounted, ref } from 'vue';
import useApi from '../hooks/useApi';
import Purchase from './Purchase.vue';
import Loader from './Loader.vue';
import NoElements from './dashboard/admin/NoElements.vue';

const { state } = inject("store");
const api = useApi()
const loading = ref(true)
const purchases = ref([])

onMounted(async () => {
    try {
        loading.value = true
        purchases.value = await api.getUserPurchases(state.user.id)
    } catch (error) {
        console.log("error fetching user purchased courses")
    } finally {
        loading.value = false
    }
})
</script>

<template>
    <v-container class="d-flex flex-column">
        <Loader v-if="loading" />
        <v-container v-else>
            <NoElements :message="'Pas de cours'" v-if="purchases.length === 0" />
            <Purchase v-for="purchase in purchases" :key="purchase.id" :purchase="purchase" />
        </v-container>
    </v-container>
</template>