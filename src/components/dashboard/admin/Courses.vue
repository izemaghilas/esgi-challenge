<script setup>
import { ref, onMounted } from 'vue';
import { toast } from 'vue3-toastify'
import useApi from '../../../hooks/useApi';
import Loader from '../../Loader.vue';
import NoElements from './NoElements.vue';
import Course from './Course.vue';

const api = useApi()
const publishedCourses = ref([])
const pendingCourses = ref([])
const reviewers = ref([])

const loading = ref(false)
const tab = ref("pending")

onMounted(async () => {
    try {
        loading.value = true
        const courses = await api.getAllCourses()
        reviewers.value = await api.getReviewers()
        publishedCourses.value = [...courses.filter(e => e.active)]
        pendingCourses.value = [...courses.filter(e => !e.active)]
    } catch (error) {
        console.error("error fetching courses");
    } finally {
        loading.value = false
    }
})

async function publishCourse(course) {
    try {
        const editedCourse = await api.publishCourse(course.id)
        pendingCourses.value = [...pendingCourses.value.filter(e => e.id !== course.id)]
        publishedCourses.value = [...publishedCourses.value, editedCourse]
        toast('Le cours a bien été publié', { type: 'success' })
    } catch (error) {
        toast('erreur lors de la publication de cours, veuillez réessayer ultérieurement!', { type: 'success' })
        console.error("error on publishing course")
    }
}
</script>

<template>
    <v-container class="d-flex flex-column p-0 h-100">
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
                        <Course v-else v-for="course in pendingCourses" :key="course.id" :course="course"
                            :reviewers="reviewers" :on-publish="publishCourse" />
                    </v-window-item>
                    <v-window-item value="published">
                        <NoElements :message="'Pas de cours'" v-if="publishedCourses.length === 0" />
                        <Course v-else v-for="course in publishedCourses" :key="course.id" :course="course"
                            :reviewers="reviewers" :on-publish="publishCourse" />
                    </v-window-item>
                </v-window>
            </v-container>
        </template>
    </v-container>
</template>