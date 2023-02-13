<template>
  <v-hover v-slot="{ hover }">
    <v-card class="card" :elevation="hover ? 16 : 2" :class="{ 'on-hover': hover }">
      <router-link :to="`/esgi-challenge/course/${course.id}`">
        <v-img :src="thumbnail" alt="" class=""></v-img>
      </router-link>
      <v-card-title class="title">{{ course.title }}</v-card-title>
      <v-card-subtitle class="description">{{ course.description }}</v-card-subtitle>
      <v-card-text>
        <v-row align="center" class="mx-0">
          <div class="grey--text">
            {{ createdAt }}
          </div>
        </v-row>
      </v-card-text>
    </v-card>

  </v-hover>
</template>

<script>
export default {
  props: {
    course: {
      required: true,
    }
  },
  computed: {
    thumbnail() {
      return this.course.thumbnail
    },
    createdAt() {
      const createdAt = new Date(this.course.createdAt).toLocaleDateString('fr-Fr', {
        weekday: "long", year: "numeric", month: "short", day: "numeric"
      })
      return createdAt
    }
  }
}
</script>

<style scoped>
.card {
  width: 100%;
  height: 100%;
  overflow: hidden;
  transition: all 0.3s ease;
}
</style>