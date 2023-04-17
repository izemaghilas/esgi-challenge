<script setup>
import { onMounted, ref } from 'vue';
import useApi from '../../../hooks/useApi';
import Loader from '../../Loader.vue';
import NoElements from './NoElements.vue';

const api = useApi()
const validationRequests = ref([])
const loading = ref(false)
onMounted(async () => {
    try {
        loading.value = true
        validationRequests.value = await api.getActiveValidationRequests()
    } catch (error) {
        console.error("error fetching validation requests")
    } finally {
        loading.value = false
    }
})
</script>

<template>
    <v-container class="d-flex flex-column p-0 h-100">
        <Loader v-if="loading" />
        <template v-else>
            <NoElements :message="'pas de demandes'" v-if="validationRequests.length === 0" />
            <v-card class="d-flex flex-row align-center w-100 h-100 px-5 py-3 my-5" v-else
                v-for="request in validationRequests" :key="request.id">
                <div class="d-flex flex-column w-50">
                    <span class="course-title">{{ request.contentId.title }}</span>
                    <div class="d-flex flex-column">
                        <div class="reviewed-by">
                            <span>examiné par</span>
                            <span class="reviewer-name ml-2">{{ `${request.reviewerId.lastname}
                                                            ${request.reviewerId.firstname}` }}</span>
                        </div>
                        <span class="send-date">envoyé le {{ new Date(request.createdAt).toLocaleDateString('fr', {
                            year: "numeric", month: "long",
                            day: "2-digit"
                        }) }}</span>
                    </div>
                </div>
                <div class="d-flex flex-row justify-end w-50">
                    <v-badge class="mr-6" color="info" content="en cours" inline></v-badge>
                </div>
            </v-card>
        </template>
    </v-container>
</template>

<style scoped>
.course-title {
    font-size: 25px;
    font-weight: bold;
    color: #000;
}

.reviewed-by {
    display: flex;
    flex-direction: row;
    font-size: 14px;
    color: #000;
}

.reviewer-name {
    font-weight: bold;
}

.send-date {
    font-size: 14px;
    color: #000;
}
</style>