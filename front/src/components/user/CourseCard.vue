<template>
  <v-hover v-slot="{ hover }">
    <v-card class="card" :elevation="hover ? 16 : 2" :class="{ 'on-hover': hover }">
      <router-link :to="{ name: APP_ROUTES.course, params: {id: course.id} }">
        <v-img :src="thumbnail" alt="" class="thumbnail"></v-img>
      </router-link>
      <v-card-title>{{ course.title }}</v-card-title>
      <v-card-subtitle class="description">{{ course.description }}</v-card-subtitle>
      <v-card-text>
        <v-row align="center" class="mx-0">
          <div class="grey--text">
            {{ createdAt }}
          </div>
        </v-row>
      </v-card-text>
      <div class="title-container">
        <v-btn :to="{ name: APP_ROUTES.course, params: {id: course.id} }" class="title" variant="tonal" color="primary">Voir le
          cours</v-btn>
      </div>
    </v-card>
  </v-hover>
</template>

<script setup>
import { computed } from 'vue';
import { APP_ROUTES } from '../../utils/constants';

const props = defineProps({
  course: {
    required: true,
    type: Object
  }
})

const { course } = props
const thumbnail = computed(() => {
  const thumbnailUrl = course.thumbnailUrl;

  if (thumbnailUrl.startsWith('/thumbnails/https://')) {
    return thumbnailUrl.substring('/thumbnails/'.length);
  } else if (thumbnailUrl.startsWith('/thumbnails/http://')) {
    return thumbnailUrl.substring('/thumbnails/'.length);
  } else {
    return thumbnailUrl ?? '';
  }
})
const createdAt = computed(() => {
  const createdAt = new Date(course.createdAt).toLocaleDateString('fr-Fr', {
    weekday: "long", year: "numeric", month: "short", day: "numeric"
  })
  return createdAt
})
</script>

<style scoped>
.card {
  width: 100%;
  height: 100%;
  overflow: hidden;
  transition: all 0.3s ease;
}

.card:hover {
  transform: scale(1.06);
}

.thumbnail {
  height: 200px;
  width: 100%;
  object-fit: cover;
}

.title-container {
  display: flex;
  height: 100%;
}

.title {
  width: 100%;
}
</style>