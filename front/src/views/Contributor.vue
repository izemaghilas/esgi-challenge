<script setup>
import { ref, onMounted, inject, computed } from 'vue';
import { toast } from 'vue3-toastify'
import RequireRole from '../components/RequireRole.vue';
import { ROLES } from '../utils/constants';
import useApi from '../hooks/useApi';
import Loader from '../components/Loader.vue';
import Course from '../components/dashboard/contributor/Course.vue';
import NoElements from '../components/dashboard/admin/NoElements.vue';
import ValidationRequest from '../components/dashboard/reviewer/ValidationRequest.vue';

const { state } = inject("store");
const api = useApi()
const loading = ref(false)

const dialogPostCourse = ref(false)
const dialogBeReviewer = ref(false)
const dialogReview = ref(false)
const tab = ref("pending")

const categories = ref([])
const publishedCourses = ref([])
const pendingCourses = ref([])
const beReviewerApplication = ref(null)
const reviewerValidationRequests = ref([])

const titleRef = ref()
const descriptionRef = ref()
const categoryRef = ref()
const priceRef = ref()
const freeCourseRef = ref(false)
const thumbnailRef = ref()
const thumbnailUrl = ref()
const videoRef = ref()
const videoUrl = ref()
const postCourseForm = ref(false)
const postCourseLoading = ref(false)

const motivationRef = ref()
const skillsRef = ref()
const beReviewerForm = ref(false)
const beReviewerLoading = ref(false)

onMounted(async () => {
    try {
        loading.value = true
        const apiCategories = await api.getAllCategories()
        const courses = await api.getCoursesByCreatorId(state.user.id)

        publishedCourses.value = [...courses.filter(e => e.active)]
        pendingCourses.value = [...courses.filter(e => !e.active)]
        categories.value = [...apiCategories]

    } catch (error) {
        console.error("error on initilizing dashboard")
    }

    try {
        beReviewerApplication.value = await api.getBeReviewerApplication(state.user.id)
        if (beReviewerApplication.value != null && beReviewerApplication.value.status === "ACCEPTED") {
            const validationRequests = await api.getValidationRequetsByReviewerId(state.user.id)
            reviewerValidationRequests.value = [...validationRequests.filter(e => e.active === true)]
        }

    } catch (error) {
        console.error("error on initilizing dashboard")
    } finally {
        loading.value = false
    }
})

const isReviewer = computed(() => {
    if (beReviewerApplication.value == null) {
        return false
    }

    if (beReviewerApplication.value.status === "ACCEPTED") {
        return true
    }

    return false
})

const beReviewerStatus = computed(() => {
    if (beReviewerApplication.value == null) {
        return ""
    }

    if (beReviewerApplication.value.status === "PENDING") {
        return `Votre candidature envoyée le ${new Date(beReviewerApplication.value.createdAt).toLocaleDateString('fr', {
            year: "numeric", month: "long",
            day: "2-digit"
        })}, est en attente de validation.`
    }

    if (beReviewerApplication.value.status === "REFUSED") {
        return `Votre candidature envoyée le ${new Date(beReviewerApplication.value.createdAt).toLocaleDateString('fr', {
            year: "numeric", month: "long",
            day: "2-digit"
        })}, a été refusée.`
    }
})

function getFileUrl(file) {
    if (file == null) {
        return null
    }

    return URL.createObjectURL(file)
}

function previewImage(image) {
    const url = getFileUrl(image[0])
    if (url) {
        thumbnailUrl.value = url
    }
}

function previewVideo(video) {
    const url = getFileUrl(video[0])
    if (url) {
        videoUrl.value = url
    }
}

async function postCourse() {
    if (postCourseForm.value) {
        try {
            postCourseLoading.value = true
            const course = await api.addCourse(titleRef.value, descriptionRef.value, freeCourseRef.value ? 0 : priceRef.value, categoryRef.value.id, thumbnailRef.value[0], videoRef.value[0])
            pendingCourses.value = [...pendingCourses.value, course]
            dialogPostCourse.value = false

            titleRef.value = null
            descriptionRef.value = null
            categoryRef.value = categories.value.length > 0 ? categories.value[0] : null
            thumbnailRef.value = null
            thumbnailUrl.value = null
            videoRef.value = null
            videoUrl.value = null

            toast("le cours a bien été soumis à validation", { type: 'success' })
        } catch (error) {
            console.error("error while posting course", error)
            toast("erreur lors de la création de cours", { type: 'error' })
        } finally {
            postCourseLoading.value = false
        }
    }
}

async function applyToBeReviewer() {
    if (beReviewerForm.value) {
        try {
            beReviewerLoading.value = true
            beReviewerApplication.value = await api.sendBeReviewerApplication(motivationRef.value, skillsRef.value)
            dialogBeReviewer.value = false
            toast("candidature envoyée", { type: 'success' })
        } catch (error) {
            console.error("error while sending be reviewer application")
            toast("erreur lors de l'envoi de la candidature pour le post d'examinateur", { type: 'error' })
        } finally {
            beReviewerLoading.value = false
        }
    }
}

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
    <RequireRole :role="ROLES.contributor.value">
        <v-navigation-drawer permanent>
            <v-sheet color="grey-lighten-4" class="pa-4">
            </v-sheet>
            <v-divider></v-divider>
            <v-list>
                <v-list-item>
                    <v-dialog v-model="dialogPostCourse" fullscreen>
                        <template v-slot:activator="{ props }">
                            <v-btn class="w-100" color="primary" v-bind="props">
                                Publier un cours
                            </v-btn>
                        </template>
                        <v-sheet>
                            <v-toolbar color="primary">
                                <v-toolbar-title>Publier un cours</v-toolbar-title>
                                <v-spacer></v-spacer>
                                <v-btn icon dark @click="dialogPostCourse = false">
                                    <v-icon>mdi-close</v-icon>
                                </v-btn>
                            </v-toolbar>
                            <v-form v-model="postCourseForm" @submit.prevent="postCourse">
                                <v-container class="d-flex flex-column">
                                    <v-row class="w-100">
                                        <v-col cols="12">
                                            <v-text-field label="Titre" id="titre" v-model.trim="titleRef"
                                                :rules="[value => !!value || 'Veuillez saisir un titre']"></v-text-field>
                                        </v-col>
                                        <v-col cols="12">
                                            <v-textarea label="Description" id="description" v-model.trim="descriptionRef"
                                                :rules="[value => !!value || 'Veuillez saisir une description']"></v-textarea>
                                        </v-col>
                                        <v-col cols="12">
                                            <v-select v-model="categoryRef" :items="categories" item-title="title"
                                                item-value="id" label="Catégorie" return-object
                                                :rules="[value => !!value || 'Veuillez choisir une catégorie']">
                                            </v-select>
                                        </v-col>
                                        <v-col class="d-flex flex-column" cols="12">
                                            <v-checkbox v-model="freeCourseRef" color="primary"
                                                label="Cours gratuit"></v-checkbox>
                                            <v-text-field v-if="!freeCourseRef" v-model.number="priceRef" label="Prix"
                                                id="price" type="number"
                                                :rules="[v => !!v || 'Veuillez saisir un prix', v => v >= 0 || 'Veuillez saisir une valeur valide']"></v-text-field>
                                        </v-col>
                                        <v-col cols="12">
                                            <v-file-input v-model="thumbnailRef" label="image" prepend-icon="mdi-camera"
                                                accept="image/*" @update:modelValue="previewImage"
                                                :rules="[value => (value && value[0]?.size > 0) || 'Veuillez choisir une image']">
                                            </v-file-input>
                                            <!-- <img class="w-50" :src="thumbnailUrl" v-if="thumbnailRef != null" /> -->
                                        </v-col>
                                        <v-col cols="12">
                                            <v-file-input v-model="videoRef" label="video" accept="video/*"
                                                :rules="[value => (value && value[0]?.size > 0) || 'Veuillez choisir une video']"
                                                @update:modelValue="previewVideo">
                                            </v-file-input>
                                            <!-- <video class="w-100 h-100 mt-6" controls :src="videoUrl" v-if="videoUrl != null"></video> -->
                                        </v-col>
                                    </v-row>
                                    <v-row class="d-flex flex-row justify-end w-100">
                                        <v-btn :loading="postCourseLoading" color="blue-darken-1" type="submit">
                                            Publier
                                        </v-btn>
                                    </v-row>
                                </v-container>
                            </v-form>
                        </v-sheet>
                    </v-dialog>
                </v-list-item>
                <v-list-item v-if="!isReviewer">
                    <v-dialog v-model="dialogBeReviewer" fullscreen v-if="beReviewerApplication == null">
                        <template v-slot:activator="{ props }">
                            <v-btn class="w-100" color="primary" v-bind="props">
                                Devenir examinateur
                            </v-btn>
                        </template>
                        <v-sheet>
                            <v-toolbar color="primary">
                                <v-toolbar-title>Devenir examinateur</v-toolbar-title>
                                <v-spacer></v-spacer>
                                <v-btn icon dark @click="dialogBeReviewer = false">
                                    <v-icon>mdi-close</v-icon>
                                </v-btn>
                            </v-toolbar>
                            <v-form v-model="beReviewerForm" @submit.prevent="applyToBeReviewer">
                                <v-container class="d-flex flex-column">
                                    <v-row>
                                        <v-col cols="12">
                                            <v-textarea label="Motivation" id="motivation" v-model.trim="motivationRef"
                                                :rules="[value => !!value || 'Veuillez expliquer vos motivations']"></v-textarea>
                                        </v-col>
                                        <v-col cols="12">
                                            <v-textarea label="Compétences" id="skills" v-model.trim="skillsRef"
                                                :rules="[value => !!value || 'Veuillez saisir vos compétences']"></v-textarea>
                                        </v-col>
                                    </v-row>
                                    <v-row class="d-flex flex-row justify-end w-100">
                                        <v-btn :loading="beReviewerLoading" color="blue-darken-1" type="submit">
                                            Envoyer
                                        </v-btn>
                                    </v-row>
                                </v-container>
                            </v-form>
                        </v-sheet>
                    </v-dialog>
                    <v-dialog v-model="dialogBeReviewer" v-else>
                        <template v-slot:activator="{ props }">
                            <v-btn class="w-100" color="primary" v-bind="props">
                                Devenir examinateur
                            </v-btn>
                        </template>
                        <v-card class="align-self-center w-50">
                            <v-card-text>{{ beReviewerStatus }}</v-card-text>
                            <v-card-actions>
                                <v-spacer></v-spacer>
                                <v-btn color="blue-darken-1" variant="text" @click="dialogBeReviewer = false">
                                    Fermer
                                </v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-dialog>
                </v-list-item>
                <v-list-item v-else>
                    <v-dialog v-model="dialogReview" fullscreen>
                        <template v-slot:activator="{ props }">
                            <v-btn class="w-100" color="primary" v-bind="props">
                                Examiner
                            </v-btn>
                        </template>
                        <v-sheet>
                            <v-toolbar color="primary">
                                <v-toolbar-title>Examiner</v-toolbar-title>
                                <v-spacer></v-spacer>
                                <v-btn icon dark @click="dialogReview = false">
                                    <v-icon>mdi-close</v-icon>
                                </v-btn>
                            </v-toolbar>
                            <NoElements :message="'Pas de demandes de validation'"
                                v-if="reviewerValidationRequests.length === 0" />
                            <v-container class="d-flex flex-column" v-else>
                                <ValidationRequest v-for="validationRequest in reviewerValidationRequests"
                                    :key="validationRequest.id" :request="validationRequest" :on-validate="validate" />
                            </v-container>
                        </v-sheet>
                    </v-dialog>
                </v-list-item>
            </v-list>
        </v-navigation-drawer>
        <v-container class="d-flex flex-column">
            <Loader v-if="loading" />
            <template v-else>
                <v-container>
                    <v-tabs v-model="tab" grow>
                        <v-tab value="pending">
                            <span>En attente de validation</span>
                            <v-badge color="info" :content="pendingCourses.length" inline
                                v-show="pendingCourses.length"></v-badge>
                        </v-tab>
                        <v-tab value="published">
                            <span>Publié</span>
                            <v-badge color="success" :content="publishedCourses.length" inline
                                v-show="publishedCourses.length"></v-badge>
                        </v-tab>
                    </v-tabs>
                    <v-window v-model="tab" class="d-flex flex-column mt-8 px-3 py-5">
                        <v-window-item value="pending">
                            <NoElements :message="'Pas de cours'" v-if="pendingCourses.length === 0" />
                            <Course v-else v-for="course in pendingCourses" :key="course.id" :course="course" />
                        </v-window-item>
                        <v-window-item value="published">
                            <NoElements :message="'Pas de cours'" v-if="publishedCourses.length === 0" />
                            <Course v-else v-for="course in publishedCourses" :key="course.id" :course="course" />
                        </v-window-item>
                    </v-window>
                </v-container>
            </template>
        </v-container>
    </RequireRole>
</template>

<style scoped>
.error-message {
    color: red;
    margin-top: 10px;
    margin-bottom: 20px
}
</style>