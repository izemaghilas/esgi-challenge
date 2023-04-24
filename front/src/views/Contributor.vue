<script setup>
import { ref, onMounted, inject } from 'vue';
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

const dialog = ref(false)
const tab = ref("pending")

const categories = ref([])
const publishedCourses = ref([])
const pendingCourses = ref([])

const titleRef = ref()
const descriptionRef = ref()
const categoryRef = ref()
const thumbnailRef = ref()
const thumbnailUrl = ref()
const videoRef = ref()
const videoUrl = ref()

onMounted(async () => {
    try {
        loading.value = true
        const apiCategories = await api.getAllCategories()
        const courses = await api.getCoursesByCreatorId(state.user.id)
        publishedCourses.value = [...courses.filter(e => e.active)]
        pendingCourses.value = [...courses.filter(e => !e.active)]
        categories.value = [...apiCategories]
        categoryRef.value = apiCategories.length > 0 ? apiCategories[0] : null
    } catch (error) {
        console.error("error on fetching course categories")
    } finally {
        loading.value = false
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
        dialog.value = false
        toast("le cours a bien été soumis à validation", { type: 'success' })
    } catch (error) {
        console.error("error while posting course", error)
        toast("erreur lors de la création de cours", { type: 'error' })
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
                    <v-dialog v-model="dialog" persistent>
                        <template v-slot:activator="{ props }">
                            <v-btn color="primary" v-bind="props">
                                Publier un cours
                            </v-btn>
                        </template>
                        <v-card>
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
                                                item-value="id" label="Catégorie">
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
                                <v-btn color="blue-darken-1" variant="text" @click="dialog = false">
                                    Fermer
                                </v-btn>
                            </v-card-actions>
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