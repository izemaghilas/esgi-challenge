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
  <div v-else>
    <div class="categoriesListContainer">
      <v-menu>
        <template v-slot:activator="{ props }">
          <v-btn variant="outlined" color="primary" v-bind="props">
            Voir les catégories
          </v-btn>
        </template>

        <v-list>
          <v-list-item v-for="category in data.categories" :key="i" :value="title" :prepend-icon="icon">
            <v-btn class="category" :to="`/esgi-challenge/list/${category.id}/${category.title}`">
              {{ category.title }}
            </v-btn></v-list-item>
        </v-list>
      </v-menu>
    </div>
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
  } catch (error) {
    console.error("error on fetching categories");
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

.categoriesTitle {
  display: flex;
  height: 50px;
  font-size: 1.2rem;
  font-weight: bold;
  justify-content: center;
  align-items: center;
}

.category {
  align-items: center;
  background-color: #FFFFFF;
  border: 1px solid rgba(0, 0, 0, 0.1);
  border-radius: .25rem;
  box-shadow: rgba(0, 0, 0, 0.02) 0 1px 3px 0;
  box-sizing: border-box;
  color: rgba(0, 0, 0, 0.85);
  cursor: pointer;
  display: inline-flex;
  font-size: 16px;
  font-weight: 600;
  justify-content: center;
  line-height: 1.25;
  min-height: 3rem;
}

.categoriesListContainer {
  display: flex;
  justify-content: center;
  align-items: center;
  margin-right: 20px;
  margin-top: 20px;
  margin-bottom: 20px;
  border: 1px solid rgba(0, 0, 0, 0.1);
  padding: 30px 0 30px 0;
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