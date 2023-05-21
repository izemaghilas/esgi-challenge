<script setup>
import { ref, computed, onMounted } from 'vue';
import { toast } from 'vue3-toastify'
import useApi from '../../../hooks/useApi';
import NoElements from './NoElements.vue';
import ReviewerHelp from './ReviewerHelp.vue';

const api = useApi()
const props = defineProps({
    course: Object,
    reviewers: Array,
    onPublish: Function
})
const { course, reviewers, onPublish } = props
const show = ref(false)
const video = ref(null)
const validationRequest = ref(null)
const dialog = ref(false)
const onPublishLoading = ref(false)
onMounted(async () => {
    try {
        video.value = await api.getCourseVideo(course.mediaLinkUrl)
    } catch (error) {
        console.error("error fetching course video")
    }

    if (!course.active) {
        try {
            const validationRequests = await api.getValidationRequestsByCourseId(course.id)
            validationRequest.value = validationRequests.length === 0 ? null : validationRequests[0]
        } catch (error) {
            console.error("error fetching course validation request")
        }

    }
})
const status = computed(() => {
    if (course.active) {
        return {
            text: "Publié",
            color: "success",
        }
    }
    if (validationRequest.value != null && validationRequest.value.active) {
        return {
            text: "En attente de la validation d'un examinateur",
            color: "info"
        }
    }
    return {
        text: "En attente",
        color: "info"
    }
})
const filteredReviewers = computed(() => {
    return reviewers.filter(r => r.id !== course.creatorId.id)
})

async function requestReviewerHelp(reviewer) {
    try {
        validationRequest.value = await api.sendValidationRequest(reviewer.id, course.id)
        toast("votre demande a été bien envoyée", { type: 'success' })
    } catch (error) {
        toast("erreur lors de l'envoi de la demande, veuillez réessayer ultérieurement!", { type: 'error' })
        console.error("error while sending reviewer help")
    } finally {
        dialog.value = false
    }
}
</script>

<template>
    <v-card :class="['card-course', { 'card-course-show': show }]">
        <div class="d-flex flex-row w-100 align-center">
            <div class="d-flex flex-column justify-center w-25">
                <span class="title">{{ course.title }}</span>
                <span class="category">{{ course.categoryId.title }}</span>
                <span class="price">{{ course.price == null || course.price === 0 ? '0 €' : `${course.price} €` }}</span>
            </div>
            <div class="d-flex flex-row justify-end align-center w-75">
                <v-badge class="mr-6" :color="status.color" :content="status.text" inline></v-badge>
                <v-icon icon="mdi-chevron-down" size="60" v-if="!show" @click="show = true"></v-icon>
                <v-icon icon="mdi-chevron-up" size="60" v-else @click="show = false"></v-icon>
            </div>
        </div>
        <div class="card-course-body" v-show="show">
            <div class="d-flex flex-row align-center">
                <img src="https://www.pngmart.com/files/22/User-Avatar-Profile-Download-PNG-Isolated-Image.png" width="40"
                    height="40" />
                <div class="d-flex flex-column ml-3">
                    <span class="application-condidat">{{ `${course.creatorId.lastname}
                                            ${course.creatorId.firstname}` }}</span>
                </div>
            </div>
            <div class="d-flex flex-column ml-3">
                <div class="d-flex flex-row w-100">
                    <img class="w-50 h-50" :src="course.thumbnailUrl">
                    <div class="d-flex flex-column ml-8">
                        <h2>Description</h2>
                        <p class="mt-2 text-body-2">{{ course.description }}</p>
                    </div>
                </div>
                <div class="d-flex flex-column mt-10" v-if="!course.active">
                    <div class="text-h5">Video</div>
                    <video class="w-100 h-100 mt-6" controls :src="video"></video>
                </div>
                <div class="card-course-tools" v-if="!course.active">
                    <div class="d-flex flex-row w-25">
                        <v-btn color="info" :loading="onPublishLoading"
                            @click="() => { onPublishLoading = true; onPublish(course) }">publier</v-btn>
                    </div>
                    <div class="d-flex flex-row justify-end w-75" v-if="validationRequest == null">
                        <v-btn color="info" @click="dialog = true">aide d'un examinateur</v-btn>
                    </div>
                </div>
            </div>
        </div>
        <v-dialog class="align-center mx-auto" v-model="dialog">
            <v-sheet class="d-flex flex-column align-center mx-auto w-50 px-5">
                <NoElements :message="'pas d\'examinateurs'" v-if="reviewers.length === 0" />
                <ReviewerHelp v-for="reviewer in filteredReviewers" :key="reviewer.id" :reviewer="reviewer"
                    :on-help="requestReviewerHelp" />
            </v-sheet>
        </v-dialog>
    </v-card>
</template>

<style scoped>
.card-course {
    display: flex;
    flex-direction: column;
    justify-content: center;
    height: 100%;
    width: 100%;
    row-gap: unset;
    padding: 10px 20px;
    margin-top: 40px;
}

.card-course-show {
    row-gap: 40px;
}

.title {
    font-size: 20px;
    font-weight: bold;
    color: #000;
}

.category {
    font-size: 14px;
    font-weight: 100;
    color: #000;
}

.price {
    font-size: 16px;
    font-weight: bold;
    color: #000;
    margin-top: 10px;
}

.card-course-body {
    display: flex;
    flex-direction: column;
    justify-content: center;
    row-gap: 30px;
}

.card-course-tools {
    display: flex;
    flex-direction: row;
    margin-top: 40px;
}
</style>