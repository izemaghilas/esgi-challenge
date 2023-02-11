<template>
  <div class="mx-3">
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

const props = defineProps(['heading'])

const api = useApi()
const loading = ref(false)

const data = reactive({
  courses: [],
  data: []
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