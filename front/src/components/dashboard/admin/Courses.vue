<script setup>
import { ref, onMounted } from 'vue';
import useApi from '../../../hooks/useApi';
import Loader from '../../Loader.vue';

const api = useApi()
const courses = ref([])
const loading = ref(false)

onMounted(async () => {
    try {
        loading.value = true
        courses.value = await api.getAllCourses()
    } catch (error) {
        console.error("error fetching courses");
    } finally {
        loading.value = false
    }
})
</script>

<template>
    <v-container class="container">
        <Loader v-if="loading" />
        <template v-else>
            <v-container class="no-courses-container" v-if="courses.length === 0">
                <v-icon icon="mdi-alert-circle-outline" size="64"></v-icon>
                <span>pas de cours</span>
            </v-container>
            <div v-else>
                <span>la liste des cours</span>
            </div>
        </template>
    </v-container>

</template>

<style scoped>
.container {
    display: flex;
    flex-direction: column;
    row-gap: 10px;
    padding: 0;
}

.no-courses-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    row-gap: 10px;
}

.no-courses-container span {
    font-size: 16px;
    font-weight: 600;
}
</style>