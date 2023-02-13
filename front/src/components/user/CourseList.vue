<template>
    <div v-if="loading" class="loader">
        <Loader />
    </div>
    <v-container v-else>
        <div class=loading v-if="loading">
            <Loader />
        </div>
        <div v-else-if="data.courses.length === 0">
            <v-container class="no-course-container">
                <v-icon icon="mdi-school" size="30"></v-icon>
                <span class="ml-5">pas de cours disponible pour l'instant</span>
            </v-container>
        </div>
        <div class="mx-3" v-else>
            <v-card-title class="text-h5 font-weight-bold">Les categories</v-card-title>
            <v-container fluid>
                <v-row>
                    <v-col cols="12" sm="3" v-for="category in data.categories" :key="category.id">
                        <v-btn color="secondary" class="category" :to="`/esgi-challenge/list/${category.id}`">
                            {{ category.title }}
                        </v-btn>
                    </v-col>
                </v-row>
            </v-container>
            <h2 class="mt-2 ml-5 grey--text">{{ props.heading }}</h2>
            <v-container fluid>
                <v-row>
                    <v-col cols="12" sm="3" v-for="course in data.courses" :key="course.id">
                        <CourseCard :course="course" />
                    </v-col>
                </v-row>
            </v-container>
        </div>
    </v-container>
</template>

<script setup>
import useApi from '../../hooks/useApi';
import { reactive, onMounted, ref } from "vue"
import { useRoute } from 'vue-router'
import Loader from '../Loader.vue';

const route = useRoute()
const api = useApi()
const loading = ref(false);


const data = reactive({
    courses: [],
})

onMounted(async () => {
    try {
        loading.value = true
        const res = await api.getAllCourses();
        data.courses = res;
        console.log("courses", data);
    } catch (error) {
        console.log("error", error);
    } finally {
        loading.value = false
    }
})


</script>

<style scoped>

</style>