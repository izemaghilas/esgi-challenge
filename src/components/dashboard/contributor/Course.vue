<script setup>
import { ref, computed, onMounted } from 'vue';
import useApi from '../../../hooks/useApi';
import PriceBadge from '../../PriceBadge.vue';

const api = useApi()
const props = defineProps({
    course: Object,
})
const { course } = props
const show = ref(false)
const video = ref(null)

onMounted(async () => {
    try {
        video.value = await api.getCourseVideo(course.mediaLinkUrl)
    } catch (error) {
        console.error("error fetching course video")
    }
})
const status = computed(() => {
    if (course.active) {
        return {
            text: "Publié",
            color: "success",
        }
    }
    return {
        text: "En attente de validation",
        color: "info"
    }
})

</script>

<template>
    <v-card :class="['card-course', { 'card-course-show': show }]">
        <div class="d-flex flex-row w-100 align-center">
            <div class="d-flex flex-column justify-center w-25">
                <span class="title">
                    {{ course.title }}
                    <PriceBadge :class="'ml-2 font-weight-bold'" :price="course.price" />
                </span>
                <span class="category mt-2">{{ course.categoryId.title }}</span>
            </div>
            <div class="d-flex flex-row justify-end align-center w-75">
                <v-badge class="mr-6" :color="status.color" :content="status.text" inline></v-badge>
                <v-icon icon="mdi-chevron-down" size="60" v-if="!show" @click="show = true"></v-icon>
                <v-icon icon="mdi-chevron-up" size="60" v-else @click="show = false"></v-icon>
            </div>
        </div>
        <div class="card-course-body" v-show="show">
            <div class="d-flex flex-column ml-3">
                <div class="d-flex flex-row w-100">
                    <img class="w-50 h-50" :src="course.thumbnailUrl">
                    <div class="d-flex flex-column ml-8">
                        <h2>Description</h2>
                        <p class="mt-2 text-body-2">{{ course.description }}</p>
                    </div>
                </div>
                <div class="d-flex flex-column mt-10">
                    <div class="text-h5">Video</div>
                    <video class="w-100 h-100 mt-6" controls :src="video"></video>
                </div>
            </div>
        </div>
    </v-card>
</template>

<style scoped>
.card-course {
    display: flex;
    flex-direction: column;
    justify-content: center;
    height: 100%;
    width: 100%;
    row-gap: unset;
    padding: 10px 20px;
    margin-top: 40px;
}

.card-course-show {
    row-gap: 40px;
}

.title {
    display: flex;
    flex-direction: row;
    align-items: center;
    font-size: 20px;
    font-weight: bold;
    color: #000;
}

.category {
    font-size: 14px;
    font-weight: 100;
    color: #000;
}

.price {
    font-size: 16px;
    font-weight: bold;
    color: #000;
    margin-top: 10px;
}

.card-course-body {
    display: flex;
    flex-direction: column;
    justify-content: center;
    row-gap: 30px;
}

.card-course-tools {
    display: flex;
    flex-direction: row;
    margin-top: 40px;
}
</style>