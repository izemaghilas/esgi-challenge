<template>
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
</template>

<script setup>
import CourseCard from './CourseCard.vue'
import useApi from '../../hooks/useApi';
import { reactive, onMounted, ref } from "vue"
import Loader from '../Loader.vue';

const props = defineProps(['heading'])

const api = useApi()
const loading = ref(false)

const data = reactive({
  courses: [],
  categories: [],
})

onMounted(async () => {
  try {
    loading.value = true
    const res = await api.getAllCourses();
    data.courses = res;
  } catch (error) {
    console.error("error", error);
  } finally {
    loading.value = false
  }

  try {
    loading.value = true
    const res = await api.getAllCategories();
    data.categories = res;
    console.log("courses", data);
  } catch (error) {
    console.log("error", error);
  } finally {
    loading.value = false
  }

})
</script>

<style scoped>
.loading {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
}

.category {
  height: 45px;
  display: flex;
  justify-content: center;
  align-items: center;
  box-shadow: 12px 12px 2px 1px rgba(0, 0, 255, .2);
  background: rgba(255, 255, 255, 0.19);
  border-radius: 5px;
  box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
  backdrop-filter: blur(1px);
  -webkit-backdrop-filter: blur(2px);
  border-bottom: 1px solid rgba(0, 0, 0, 0.22);
}

.no-course-container {
  display: flex;
  width: 100%;
  margin-top: 80px;
  padding: 20px 10px 20px 10px;
  justify-content: center;
  align-items: flex-start;
  box-shadow: 12px 12px 2px 1px rgba(70, 70, 202, 0.2);
  border: 1px solid rgba(70, 70, 202, 0.2);
}
</style>