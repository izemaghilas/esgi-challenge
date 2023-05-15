<script setup>
import { onMounted, ref } from "vue"
import useApi from "../../../hooks/useApi";
import Loader from "../../Loader.vue";

const api = useApi()
const comments = ref([])
const loading = ref(false)
const dialog = ref(false)
const commentToRemove = ref()

onMounted(async () => {
    try {
        loading.value = true
        comments.value = await api.getAllComments()
    } catch (error) {
        console.error("error fetching comments");
    } finally {
        loading.value = false
    }
})

async function removeComment() {
    try {
        loading.value = true
        await api.removeComment(commentToRemove.value.id)
        comments.value = [...comments.value.filter(c => c.id !== commentToRemove.value.id)]
        dialog.value = false
        commentToRemove.value = null
    } catch (error) {
        console.error("error on removing comment");
    } finally {
        loading.value = false
    }
}

</script>

<template>
    <v-container class="container">
        <Loader v-if="loading" />
        <template v-else>
            <v-container class="no-comments-container" v-if="comments.length === 0">
                <v-icon icon="mdi-alert-circle-outline" size="64"></v-icon>
                <span>pas de commentaires</span>
            </v-container>
            <v-card v-else class="card-comment" v-for="comment in comments" :key="comment.id">
                <div class="card-comment-header">
                    <img src="https://www.pngmart.com/files/22/User-Avatar-Profile-Download-PNG-Isolated-Image.png" />
                    <div>
                        <span class="card-comment-creator">{{ `${comment.commenterId.lastname}
                        ${comment.commenterId.firstname}` }}</span>
                        <span class="card-comment-date">{{
                            new Date(comment.createdAt).toLocaleDateString('fr', {
                                year: "numeric", month: "long",
                                day: "2-digit"
                            })
                        }}</span>
                    </div>
                </div>
                <span class="card-comment-content">{{ comment.content }}</span>
                <div class="card-comment-tools">
                    <v-btn color="error" @click="dialog = true; commentToRemove = comment">supprimer</v-btn>
                </div>
            </v-card>
            <v-dialog v-model="dialog">
                <v-sheet class="mx-auto confirm-remove-comment">
                    <span>voulez vous supprimer ce commentaires ?</span>
                    <v-btn color="error" @click="removeComment">supprimer</v-btn>
                </v-sheet>
            </v-dialog>
        </template>
    </v-container>
</template>

<style scoped>
.container {
    display: flex;
    flex-direction: column;
    row-gap: 40px;
    padding: 0;
}

.no-comments-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    row-gap: 10px;
}

.no-comments-container span {
    font-size: 16px;
    font-weight: 600;
}

.card-comment {
    display: flex;
    flex-direction: column;
    row-gap: 15px;
    width: 80%;
}

.card-comment-header {
    display: flex;
    flex-direction: row;
    align-items: center;
    column-gap: 10px;
    width: 100%;
    height: 60px;
}

.card-comment-header img {
    width: 40px;
    height: 40px;
    align-self: center;
    margin-left: 10px;
}

.card-comment-header div {
    display: flex;
    flex-direction: column;
    width: 100%;
}

.card-comment-creator {
    font-size: 16px;
    font-weight: 600;
}

.card-comment-date {
    font-size: 10px;
    font-weight: 600;
}

.card-comment-content {
    font-size: 16px;
    font-weight: 600;
    margin-left: 20px;
    margin-top: 20px;
    width: 80%
}

.card-comment-tools {
    display: flex;
    flex-direction: row;
    justify-content: flex-end;
    column-gap: 20px;
    width: 90%;
}

.confirm-remove-comment {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    row-gap: 20px;
    padding: 25px;
}

.confirm-remove-comment span {
    font-size: 16px;
    font-weight: 600;
}
</style>