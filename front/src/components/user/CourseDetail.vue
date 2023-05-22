<template class="container">
    <div class="container">
        <RequireAuth>
            <div v-if="loading" class="loader">
                <Loader />
            </div>
            <v-container v-else>
                <v-row>
                    <v-col cols="12" sm="10">
                        <div class="videoContainer">
                            <v-img :src="thumbnail" alt="" class="" />
                            <v-icon class="playIcon" @click="handleVideoPlayClick">mdi-play</v-icon>
                        </div>

                        <v-spacer></v-spacer>

                        <v-btn size="x-large" color="surface-variant" variant="text" icon="mdi-heart"></v-btn>

                        <v-btn size="x-large" color="surface-variant" variant="text" icon="mdi-bookmark"></v-btn>

                        <v-btn size="x-large" color="surface-variant" variant="text" icon="mdi-share-variant"></v-btn>

                        <v-btn size="x-large" icon="mdi-flag" @click="handleReportClick" color="surface-variant"
                            variant="text"></v-btn>

                    </v-col>
                </v-row>
                <v-row>
                    <v-col cols="12" sm="8">
                        <v-card-title class="title">{{ course.title }}</v-card-title>
                        <v-card-subtitle class="description">{{ course.description }}</v-card-subtitle>
                        <v-btn v-if="isCoursePurchased || (course.price == null || course.price === 0)" @click="handleVideoPlayClick" prepend-icon="mdi-play" class="button"
                            style="color:white; background-color: #251d5d;">
                            Commencer le cours
                        </v-btn>
                        <v-btn v-else @click="createSession" prepend-icon="mdi-play" class="button"
                            style="color:white; background-color: #251d5d;">
                            Acheter le cours
                        </v-btn>
                    </v-col>
                    <v-dialog v-model="dialogVideo" fullscreen :scrim="false" transition="dialog-bottom-transition">
                        <v-card>
                            <v-toolbar dark color="#251d5d">
                                <v-btn icon dark color="#f4a118" @click="dialogVideo = false">
                                    <v-icon>mdi-close</v-icon>
                                </v-btn>
                                <v-toolbar-title class="video-title">{{ course.title }}</v-toolbar-title>
                                <v-spacer></v-spacer>
                            </v-toolbar>
                            <div class="video-container">
                                <iframe allowfullscreen :src="videoLink"></iframe>
                            </div>
                        </v-card>
                    </v-dialog>
                </v-row>
                <v-card class="comments">
                    <v-card-title class="title">Commentaires</v-card-title>
                    <v-card-text>
                        <Comments :courseId="course.id" />
                    </v-card-text>
                </v-card>
                <v-dialog v-model="dialogReport" max-width="500px">
                    <v-card>
                        <v-card-title>
                            <span class="headline">Signaler le cours : {{ course.title }} .</span>
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
        </RequireAuth>
    </div>
</template>

<script>
import useApi from '../../hooks/useApi';
import { inject } from "vue"
import { useRoute } from 'vue-router'
import Loader from '../Loader.vue';
import Comments from './Comments.vue';
import RequireAuth from '../RequireAuth.vue';
import { loadStripe } from '@stripe/stripe-js'

export default {
    components: {
        Comments,
        Loader,
        RequireAuth
    },
    data() {
        this.route = useRoute()
        this.api = useApi()
        this.state = inject("store").state;
        this.userData = this.state.user
        this.publishableKey = import.meta.env.APP_STRIPE_PUBLISHABLE_KEY
        return {
            course: {},
            loading: false,
            reportInput: '',
            dialogVideo: false,
            dialogReport: false,
            isCoursePurchased: false,
            snackBarText: '',
            timeout: 3000,
            snackBarShow: false,
            thumbnail: '',
            videoLink: '',
            sessionId: 'session'
        }
    },
    async beforeMount() {
        try {
            this.loading = true
            const responseGetCourse = await this.api.getCourseById(this.route.params.id)
            this.course = responseGetCourse
            this.setVideoThumbnail()
            this.setVideoLink()

            const responseIsCoursePurchased = await this.api.getPurchase(this.userData?.id, this.route.params.id)
            if (responseIsCoursePurchased && responseIsCoursePurchased?.length > 0) {
                this.isCoursePurchased = true
            }
        } catch (error) {
            console.error(error)
        } finally {
            this.loading = false
        }
    },
    methods: {
        setVideoThumbnail() {
            const thumbnailUrl = this.course.thumbnailUrl;

            if (thumbnailUrl.startsWith('/thumbnails/https://')) {
                this.thumbnail = thumbnailUrl.substring('/thumbnails/'.length);
            } else if (thumbnailUrl.startsWith('/thumbnails/http://')) {
                this.thumbnail = thumbnailUrl.substring('/thumbnails/'.length);
            } else {
                this.thumbnail = thumbnailUrl ?? '';
            }
        },
        setVideoLink() {
            const video = this.course.mediaLinkUrl;

            if (video && video.startsWith('/videos/https://')) {
                this.videoLink = video.substring('/videos/'.length);
            } else {
                this.videoLink = video ?? '';
            }
        },
        handleReportClick() {
            this.dialogReport = true
        },

        handleVideoPlayClick() {
            this.dialogVideo = true
        },
        async createStripeSession() {
            let response

            try {
                response = await this.api.getStripeSessionId(
                    this.userData.id,
                    this.course.id,
                    import.meta.env.APP_FRONT_URL + '/payment/success',
                    import.meta.env.APP_FRONT_URL + '/payment/cancel'
                )

            } catch (error) {
                console.log("error", error)
            }

            return response?.id

        },
        async createSession() {
            const sessionId = await this.createStripeSession();

            const stripe = await loadStripe(import.meta.env.APP_STRIPE_PUBLISHABLE_KEY)
            stripe.redirectToCheckout({ sessionId: sessionId })
        },
        async postReport() {
            try {
                loading.value = true
                const data = {
                    description: this.reportInput,
                    reporterId: this.userData.id,
                    contentId: this.route.params.id,
                }
                const response = await this.api.postReportContent(data)
                this.snackBarText = "Le cours a été signalé"
                this.snackBarShow = true
            } catch (error) {
                console.error(error)
            } finally {
                this.loading = false
                this.dialogReport = false
            }
        }
    }
}
</script>

<style scoped>
.container {
    width: 100%;
    background-color: #b4bee3;
}

.videoContainer {
    position: relative;
    overflow: hidden;
}

.playIcon {
    position: absolute;
    top: 45%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 100px;
    color: #131313;
    cursor: pointer;
    background-color: #cacaca;
    border-radius: 500px;
}

.button {
    background-color: #170f4b;
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