<template>
    <div v-if="loading">
        <Loader />
    </div>
    <v-list v-else lines="two">
        <v-container class="no-comments-container" v-if="data.comments.length === 0">
            <v-icon icon="mdi-comment" size="30"></v-icon>
            <span class="ml-5">pas de commentaires</span>
        </v-container>
        <v-sheet class="mx-auto">
            <v-form class="commentContainer" fast-fail @submit.prevent="postComment">
                <v-container class="d-flex justify-center">
                    <v-text-field v-model="commentInput" label="commentaire"></v-text-field>
                </v-container>
                <v-container class="d-flex justify-start containerButton">
                    <v-btn type="submit" block class="button">Envoyer</v-btn>
                </v-container>
            </v-form>
        </v-sheet>
        <v-list-item class="comment" v-for="comment in data.comments" :key="comment.id"
            prepend-avatar="https://www.pngmart.com/files/22/User-Avatar-Profile-Download-PNG-Isolated-Image.png"
            :title="getFullName(comment.commenterId)" :subtitle="comment.content">
        </v-list-item>
        <v-snackbar v-model="snackBarShow" :timeout="timeout">
            {{ snackBarText }}

            <template v-slot:actions>
                <v-btn color="blue" variant="text" @click="snackBarShow = false">
                    Fermer
                </v-btn>
            </template>
        </v-snackbar>
    </v-list>
</template>

<script setup>
import Loader from '../Loader.vue';
import { reactive, onMounted, computed, ref } from "vue"
import useApi from '../../hooks/useApi';
import useUser from '../../hooks/useUser';

const api = useApi()
const props = defineProps(['courseId'])
const userData = useUser()
const snackBarText = ref('');
const timeout = ref(3000);
const snackBarShow = ref(false);
const commentInput = ref(null)
const loading = ref(false);

const getFullName = (user) => {
    return user.firstname + " " + user.lastname;
}

const data = reactive({
    comments: [],
})

async function postComment() {
    try {
        loading.value = true
        const payload = {
            comment: commentInput.value,
            commenterId: userData.user.id,
            courseId: props.courseId
        }
        const response = await api.postComment(payload)
        console.log("res", response)
        data.comments.unshift(response)
        snackBarText.value = "Commentaire envoyé !"
        snackBarShow.value = true
    } catch (error) {
        console.log(error)
    } finally {
        loading.value = false
    }
}

onMounted(async () => {
    try {
        loading.value = true
        const response = await api.getCommentsByCourse(props.courseId)
        console.log("res", response)
        data.comments = response
    } catch (error) {
        //console.log(error)
    } finally {
        loading.value = false
    }
})

computed
</script>

<style scoped>
.button {
    width: 100%;
    background-color: #3f51b5;
    color: white;
    margin-bottom: 15px;
}

.commentContainer {
    box-shadow: 5px 5px 2px 1px #3f51b5;

    margin-bottom: 30px;
}

.containerButton {
    width: 35%;
    margin-top: -20px;
}

.comment {
    margin: 10px;
    padding: 10px;
    border-bottom: 1px solid #ccc;
    border-radius: 5px;
}
</style>