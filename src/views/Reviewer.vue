<script setup>
import { inject, onMounted, ref } from 'vue';
import { toast } from 'vue3-toastify';
import useApi from '../hooks/useApi';
import Loader from '../components/Loader.vue';
import NoElements from '../components/dashboard/admin/NoElements.vue';
import ValidationRequest from '../components/dashboard/reviewer/ValidationRequest.vue';

const { state } = inject("store")
const api = useApi()
const reviewerValidationRequests = ref([])
const loading = ref(false)

onMounted(async () => {
    try {
        loading.value = true
        const validationRequests = await api.getValidationRequetsByReviewerId(state.user.id)
        reviewerValidationRequests.value = [...validationRequests.filter(e => e.active === true)]
    } catch (error) {
        console.error("error fetching reviewer validation requests")
    } finally {
        loading.value = false
    }
})

async function validate(validationRequest) {
    try {
        await api.publishCourse(validationRequest.contentId.id)
        reviewerValidationRequests.value = [...reviewerValidationRequests.value.filter(e => e.id !== validationRequest.id)]
        toast('Le cours a bien été publié', { type: 'success' })
    } catch (error) {
        toast('erreur lors de la publication de cours!', { type: 'error' })
        console.error("error on publishing course")
    }
}
</script>

<template>
    <v-container class="d-flex flex-column p-0 h-100">
        <Loader v-if="loading" />
        <template v-else>
            <NoElements :message="'Pas de demandes de validation'" v-if="reviewerValidationRequests.length === 0" />
            <v-container class="d-flex flex-column" v-else>
                <ValidationRequest v-for="validationRequest in reviewerValidationRequests" :key="validationRequest.id"
                    :request="validationRequest" :on-validate="validate" />
            </v-container>
        </template>
    </v-container>
</template>