<script setup>
import { ref, onMounted, inject, computed } from 'vue';
import { toast } from 'vue3-toastify'
import RequireRole from '../components/RequireRole.vue';
import { ROLES } from '../utils/constants';
import useApi from '../hooks/useApi';
import Loader from '../components/Loader.vue';
import Course from '../components/dashboard/contributor/Course.vue';
import NoElements from '../components/dashboard/admin/NoElements.vue';

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

const titleRef = ref()
const descriptionRef = ref()
const categoryRef = ref()
const thumbnailRef = ref()
const thumbnailUrl = ref()
const videoRef = ref()
const videoUrl = ref()

const motivationRef = ref()
const skillsRef = ref()

onMounted(async () => {
    try {
        loading.value = true
        const apiCategories = await api.getAllCategories()
        const courses = await api.getCoursesByCreatorId(state.user.id)
        beReviewerApplication.value = await api.getBeReviewerApplication(state.user.id)
        publishedCourses.value = [...courses.filter(e => e.active)]
        pendingCourses.value = [...courses.filter(e => !e.active)]
        categories.value = [...apiCategories]
        categoryRef.value = apiCategories.length > 0 ? apiCategories[0].id : null
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
    try {
        const course = await api.addCourse(titleRef.value, descriptionRef.value, categoryRef.value.id, thumbnailRef.value[0], videoRef.value[0])
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
    }
}

async function applyToBeReviewer() {
    try {
        beReviewerApplication.value = await api.sendBeReviewerApplication(motivationRef.value, skillsRef.value)
        dialogBeReviewer.value = false
        toast("candidature envoyée", { type: 'success' })
    } catch (error) {
        console.error("error while sending be reviewer application")
        toast("erreur lors de l'envoi de la candidature pour le post d'examinateur", { type: 'error' })
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
                    <v-dialog v-model="dialogPostCourse" persistent>
                        <template v-slot:activator="{ props }">
                            <v-btn color="primary" v-bind="props">
                                Publier un cours
                            </v-btn>
                        </template>
                        <v-card class="align-self-center w-75">
                            <v-card-item>
                                <v-card-title>Publier un cours</v-card-title>
                            </v-card-item>
                            <v-card-text>
                                <v-container>
                                    <v-row>
                                        <v-col cols="12">
                                            <v-text-field label="Titre" id="titre" required
                                                v-model="titleRef"></v-text-field>
                                        </v-col>
                                        <v-col cols="12">
                                            <v-textarea label="Description" id="description" required
                                                v-model="descriptionRef"></v-textarea>
                                        </v-col>
                                        <v-col cols="12">
                                            <v-select v-model="categoryRef" :items="categories" item-title="title"
                                                item-value="id" label="Catégorie" return-object>
                                            </v-select>
                                        </v-col>
                                        <v-col cols="12">
                                            <v-file-input v-model="thumbnailRef" label="image" accept="image/*"
                                                @update:modelValue="previewImage">
                                            </v-file-input>
                                            <!-- <img class="w-50" :src="thumbnailUrl" v-if="thumbnailRef != null" /> -->
                                        </v-col>
                                        <v-col cols="12">
                                            <v-file-input v-model="videoRef" label="video" accept="video/*"
                                                @update:modelValue="previewVideo">
                                            </v-file-input>
                                            <!-- <video class="w-100 h-100 mt-6" controls :src="videoUrl" v-if="videoUrl != null"></video> -->
                                        </v-col>
                                    </v-row>
                                </v-container>
                            </v-card-text>
                            <v-card-actions>
                                <v-spacer></v-spacer>
                                <v-btn color="blue-darken-1" variant="text" @click="postCourse()">
                                    Publier
                                </v-btn>
                                <v-btn color="blue-darken-1" variant="text" @click="dialogPostCourse = false">
                                    Fermer
                                </v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-dialog>
                </v-list-item>
                <v-list-item v-if="!isReviewer">
                    <v-dialog v-model="dialogBeReviewer" persistent>
                        <template v-slot:activator="{ props }">
                            <v-btn color="primary" v-bind="props">
                                Devenir examinateur
                            </v-btn>
                        </template>
                        <v-card class="align-self-center w-50" v-if="beReviewerApplication == null">
                            <v-card-item>
                                <v-card-title>Devenir examinateur</v-card-title>
                            </v-card-item>
                            <v-card-text>
                                <v-container>
                                    <v-row>
                                        <v-col cols="12">
                                            <v-textarea label="Motivation" id="motivation" required
                                                v-model="motivationRef"></v-textarea>
                                        </v-col>
                                        <v-col cols="12">
                                            <v-textarea label="Compétences" id="skills" required
                                                v-model="skillsRef"></v-textarea>
                                        </v-col>
                                    </v-row>
                                </v-container>
                            </v-card-text>
                            <v-card-actions>
                                <v-spacer></v-spacer>
                                <v-btn color="blue-darken-1" variant="text" @click="applyToBeReviewer">
                                    Envoyer
                                </v-btn>
                                <v-btn color="blue-darken-1" variant="text" @click="dialogBeReviewer = false">
                                    Fermer
                                </v-btn>
                            </v-card-actions>
                        </v-card>
                        <v-card class="align-self-center w-50" v-else>
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
                    <v-dialog v-model="dialogReview">
                        <template v-slot:activator="{ props }">
                            <v-btn color="primary" v-bind="props">
                                Demandes d'examination
                            </v-btn>
                        </template>
                        <v-card>
                            <v-card-text>
                                <span>A faire au même temps que l'implementation de dashboard examinateur</span>
                            </v-card-text>
                        </v-card>
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