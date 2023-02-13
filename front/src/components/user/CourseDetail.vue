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
                <v-btn @click="handleVideoPlayClick" prepend-icon="mdi-school" class="button"
                    style="color:white; background-color: #251d5d;">
                    Commencer le cours
                </v-btn>
            </v-col>
            <v-col class="mt-0">
                <v-btn prepend-icon="mdi-close-box" @click="handleReportClick" class="button"
                    style="color:white; background-color: #251d5d;">Signler ce
                    cours</v-btn>
            </v-col>
            <v-dialog v-model="dialogVideo" fullscreen :scrim="false" transition="dialog-bottom-transition">
                <v-card>
                    <v-toolbar dark color="#251d5d">
                        <v-btn icon dark color="#f4a118" @click="dialogVideo = false">
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
        <v-dialog v-model="dialogReport" max-width="500px">
            <v-card>
                <v-card-title>
                    <span class="headline">Signaler le cours : {{ data.course.title }} .</span>
                </v-card-title>
                <v-card-text>
                    <v-textarea v-model="reportInput" label="Description" outlined></v-textarea>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="blue darken-1" text @click="dialogReport = false">Annuler</v-btn>
                    <v-btn color="blue darken-1" text @click="postReport">Signaler</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <v-snackbar v-model="snackBarShow" :timeout="timeout">
            {{ snackBarText }}

            <template v-slot:actions>
                <v-btn color="blue" variant="text" @click="snackBarShow = false">
                    Fermer
                </v-btn>
            </template>
        </v-snackbar>
    </v-container>
</template>

<script setup>
import useApi from '../../hooks/useApi';
import { reactive, onMounted, ref } from "vue"
import { useRoute } from 'vue-router'
import Loader from '../Loader.vue';
import Comments from './Comments.vue';
import useUser from '../../hooks/useUser';

const route = useRoute()
const api = useApi()
const userData = useUser()
const loading = ref(false);
const reportInput = ref('');
const dialogVideo = ref(false);
const dialogReport = ref(false)

const snackBarText = ref('');
const timeout = ref(3000);
const snackBarShow = ref(false);

const data = reactive({
    course: {},
})

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

const handleReportClick = () => {
    dialogReport.value = true
}

const handleVideoPlayClick = () => {
    dialogVideo.value = true
}

const postReport = async () => {
    try {
        loading.value = true
        const data = {
            description: reportInput.value,
            reporterId: userData.id,
            contentId: route.params.id,
        }
        const response = await api.postReportContent(data)
        snackBarText.value = "Le cours a été signalé"
        snackBarShow.value = true
    } catch (error) {
        console.log(error)
    } finally {
        loading.value = false
        dialogReport.value = false
    }
}

</script>

<style scoped>
.button {
    background-color: #251d5d;
    color: #ffffff;
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