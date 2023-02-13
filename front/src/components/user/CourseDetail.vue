<template>
    <div v-if="loading" class="loader">
        <Loader />
    </div>
    <v-container v-else>
        <v-row>
            <v-col cols="12" sm="4">
                <v-img :src="data.course.thumbnail" alt="" class="" />
            </v-col>
            <v-col cols="12" sm="8">
                <v-card-title class="title">{{ data.course.title }}</v-card-title>
                <v-card-subtitle class="description">{{ data.course.description }}</v-card-subtitle>
            </v-col>
            <v-dialog v-model="dialog" fullscreen :scrim="false" transition="dialog-bottom-transition">
                <template v-slot:activator="{ props }">
                    <v-btn class="button" dark v-bind="props">
                        Voir le cours
                    </v-btn>
                </template>
                <v-card>
                    <v-toolbar dark color="#251d5d">
                        <v-btn icon dark color="#f4a118" @click="dialog = false">
                            <v-icon>mdi-close</v-icon>
                        </v-btn>
                        <v-toolbar-title class="video-title">{{ data.course.title }}</v-toolbar-title>
                        <v-spacer></v-spacer>
                    </v-toolbar>
                    <div class="video-container">
                        <iframe allowfullscreen :src="data.course.mediaLink"></iframe>
                    </div>
                </v-card>
            </v-dialog>
        </v-row>
        <v-card class="comments">
            <v-card-title class="title">Commentaires</v-card-title>
            <v-card-text>
                <Comments :courseId="data.course.id" />
            </v-card-text>
        </v-card>
    </v-container>
</template>

<script setup>
import useApi from '../../hooks/useApi';
import { reactive, onMounted, ref } from "vue"
import { useRoute } from 'vue-router'
import Loader from '../Loader.vue';
import Comments from './Comments.vue';

const route = useRoute()
const api = useApi()
const loading = ref(false);
console.log(route.params.id)

const data = reactive({
    course: {},
})

const dialog = ref(false)
onMounted(async () => {
    try {
        loading.value = true
        const response = await api.getCourseById(route.params.id)
        data.course = response
    } catch (error) {
        console.log(error)
    } finally {
        loading.value = false
    }
})


</script>

<style scoped>
.button {
    background-color: #251d5d;
    color: #ffffff;
    padding: 10px 70px;
    margin-top: 20px;
    margin-left: 10px;
}

.loader {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

.comments {
    margin-top: 60px;
    box-shadow: 12px 12px 2px 1px rgba(0, 0, 255, .2);
    background: rgba(255, 255, 255, 0.19);
    border-radius: 6px;
    box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
    backdrop-filter: blur(4.5px);
    -webkit-backdrop-filter: blur(4.5px);
    border: 1px solid rgba(255, 255, 255, 0.22);
}

.video-title {
    color: #ffffff;
    font-size: 20px;
}

.video-container {
    overflow: hidden;
    padding-top: 56.25%;
    position: relative;
}

.video-container iframe {
    border: 0;
    width: 100%;
    height: 80%;
    left: 0;
    position: absolute;
    top: 0;
}
</style>