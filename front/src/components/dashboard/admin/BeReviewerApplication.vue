<script setup>
import { ref, computed } from 'vue';

const statuses = {
    'PENDING': {
        text: "En attente",
        color: "info"
    },
    'ACCEPTED': {
        text: "Acceptée",
        color: "success",
    },
    'REFUSED': {
        text: "Refusée",
        color: "error"
    },
}
const props = defineProps({
    application: Object,
    onAccept: Function,
    onRefuse: Function
})
const { application, onAccept, onRefuse } = props
const status = computed(() => {
    return statuses[application.status]
})
const show = ref(false)
const onAcceptLoading = ref(false)
const onRefuseLoading = ref(false)

function setOnAcceptLoading(v) {
    onAcceptLoading.value = v
}
function setOnRefuseLoading(v) {
    onRefuseLoading.value = v
}
</script>

<template>
    <v-card :class="['card-application', { 'card-application-show': show }]">
        <div class="d-flex flex-row w-100 align-center">
            <div class="d-flex flex-row align-center w-25">
                <img src="https://www.pngmart.com/files/22/User-Avatar-Profile-Download-PNG-Isolated-Image.png" width="40"
                    height="40" />
                <div class="d-flex flex-column ml-3">
                    <span class="application-condidat">{{ `${application.contributor.lastname}
                                            ${application.contributor.firstname}` }}</span>
                    <span class="application-date">{{
                        new Date(application.createdAt).toLocaleDateString('fr', {
                            year: "numeric", month: "long",
                            day: "2-digit"
                        })
                    }}</span>
                </div>
            </div>
            <div class="d-flex flex-row justify-end align-center w-75">
                <v-badge class="mr-6" :color="status.color" :content="status.text" inline></v-badge>
                <v-icon icon="mdi-chevron-down" size="60" v-if="!show" @click="show = true"></v-icon>
                <v-icon icon="mdi-chevron-up" size="60" v-else @click="show = false"></v-icon>
            </div>
        </div>
        <div class="card-application-body" v-show="show">
            <div class="card-application-textarea-wrapper">
                <span>Motivation</span>
                <div class="card-application-textarea">{{ application.motivation }}</div>
            </div>
            <div class="card-application-textarea-wrapper">
                <span>Compétences</span>
                <div class="card-application-textarea">{{ application.skills }}</div>
            </div>
        </div>
        <div class="card-application-tools" v-if="application.status === 'PENDING' && show">
            <v-btn color="info" :disabled="onRefuseLoading" :loading="onAcceptLoading"
                @click="onAccept(application, setOnAcceptLoading)">accepter</v-btn>
            <v-btn color="error" :disabled="onAcceptLoading" :loading="onRefuseLoading"
                @click="onRefuse(application, setOnRefuseLoading)">refuser</v-btn>
        </div>
    </v-card>
</template>

<style scoped>
.card-application {
    display: flex;
    flex-direction: column;
    justify-content: center;
    height: 100%;
    width: 100%;
    row-gap: unset;
    padding: 10px 20px;
    margin-top: 40px;
}

.card-application-show {
    row-gap: 40px;
}


.application-condidat {
    font-size: 16px;
    font-weight: 600;
}

.application-date {
    font-size: 12px;
    font-weight: 600;
}

.card-application-body {
    display: flex;
    flex-direction: column;
    justify-content: center;
    row-gap: 30px;
}

.card-application-textarea-wrapper {
    display: flex;
    flex-direction: column;
    row-gap: 10px;
    color: #000;
    font-size: 16px;
    font-weight: bold;
}

.card-application-textarea {
    display: flex;
    flex-direction: column;
    width: 100%;
    height: 150px;
    row-gap: 15px;
    padding: 20px;
    background-color: #f6f6f6;
    border-bottom: 1px solid #afafaf;
    font-size: 14px;
    font-weight: 100;
    overflow-y: auto;
}

.card-application-tools {
    display: flex;
    flex-direction: row;
    column-gap: 25px;
}
</style>