<script setup>
import { computed, onMounted, ref } from 'vue';
import useApi from '../../../hooks/useApi';
import Loader from "../../Loader.vue";
import BeReviewerApplication from './BeReviewerApplication.vue';
import NoElements from './NoElements.vue';

const api = useApi()
const pendingApplications = ref([])
const acceptedApplications = ref([])
const refusedApplications = ref([])

const loading = ref(false)
const tab = ref("pendingApplications")

onMounted(async () => {
    try {
        loading.value = true
        const applications = await api.getBeReviewerApplications()
        pendingApplications.value = applications.length > 0 ? applications.filter(e => e.status === "PENDING") : []
        acceptedApplications.value = applications.length > 0 ? applications.filter(e => e.status === "ACCEPTED") : []
        refusedApplications.value = applications.length > 0 ? applications.filter(e => e.status === "REFUSED") : []
    } catch (error) {
        console.error("error fetching be reviewer applications");
    } finally {
        loading.value = false
    }
})

async function acceptApplication(application) {
    try {
        const editedApplication = await api.acceptBeReviwerApplication(application.id)
        pendingApplications.value = [...pendingApplications.value.filter(e => e.id !== application.id)]
        acceptedApplications.value = [...acceptedApplications.value, editedApplication]
    } catch (error) {
        console.error("error on accepting be reviewer applications");
    }
}

async function refuseApplication(application) {
    try {
        const editedApplication = await api.refuseBeReviwerApplication(application.id)
        pendingApplications.value = [...pendingApplications.value.filter(e => e.id !== application.id)]
        refusedApplications.value = [...refusedApplications.value, editedApplication]
    } catch (error) {
        console.error("error on refusing be reviewer applications");
    }
}

</script>

<template>
    <v-container class="container">
        <Loader v-if="loading" />
        <template v-else>
            <v-container class="tabs-container">
                <v-tabs v-model="tab" grow>
                    <v-tab value="pendingApplications">
                        <span>En attentes</span>
                        <v-badge color="info" :content="pendingApplications.length" inline
                            v-show="pendingApplications.length"></v-badge>
                    </v-tab>
                    <v-tab value="acceptedOrRefusedApplications">
                        <span>Acceptées - Refusées</span>
                        <v-badge color="success" :content="acceptedApplications.length" inline
                            v-show="acceptedApplications.length"></v-badge>
                        <v-badge color="error" :content="refusedApplications.length" inline
                            v-show="refusedApplications.length"></v-badge>
                    </v-tab>
                </v-tabs>
                <v-window v-model="tab" class="d-flex flex-column pa-5">
                    <v-window-item value="pendingApplications">
                        <NoElements :message="'pas de candidatures'" v-if="pendingApplications.length === 0" />
                        <BeReviewerApplication v-else v-for="application in pendingApplications" :key="application.id"
                            :application="application" :on-accept="acceptApplication" :on-refuse="refuseApplication" />
                    </v-window-item>
                    <v-window-item value="acceptedOrRefusedApplications">
                        <NoElements :message="'pas de candidatures'"
                            v-if="[...acceptedApplications, ...refusedApplications].length === 0" />
                        <BeReviewerApplication v-else
                            v-for="application in [...acceptedApplications, ...refusedApplications]" :key="application.id"
                            :application="application" :on-accept="acceptApplication" :on-refuse="refuseApplication" />
                    </v-window-item>
                </v-window>
            </v-container>
        </template>
    </v-container>
</template>

<style scoped>
.container {
    display: flex;
    flex-direction: column;
    align-items: center;
    height: 100%;
    row-gap: 30px;
    padding: 0;
}


.tabs-container {
    display: flex;
    flex-direction: column;
    row-gap: 20px;
    width: 100%;
}

.number-of-applications {
    color: #000;
    font-size: 16px;
    font-weight: 600;
    margin-top: 8px;
}
</style>