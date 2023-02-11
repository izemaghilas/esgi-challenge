<script setup>
import { onMounted, ref } from "vue"
import useApi from "../../../hooks/useApi";

const api = useApi()
const comments = ref([])
onMounted(async () => {
    try {
        comments.value = await api.getAllComments()
    } catch (error) {
        console.error("error fetching comments");
    }
})
</script>

<template>
    <v-container class="container">
        <v-container class="no-comments-container" v-if="comments.length === 0">
            <v-icon icon="mdi-alert-circle-outline" size="64"></v-icon>
            <span>pas de commentaires</span>
        </v-container>
        <v-card v-else class="card-comment" v-for="comment in comments" :key="comment.id">
            <span>{{ comment.content }}</span>
            <div class="card-comment-tools">
                <v-btn color="error" @click="removeUser(comment)">supprimer</v-btn>
            </div>
        </v-card>
    </v-container>
</template>

<style scoped>
.container {
    display: flex;
    flex-direction: column;
    row-gap: 10px;
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
    row-gap: 20px;
    width: 60%;
}

.card-comment span {
    font-size: 16px;
    font-weight: 600;
    margin-left: 20px;
    margin-top: 20px;
    width: 50%
}

.card-comment-tools {
    display: flex;
    flex-direction: row;
    justify-content: flex-end;
    column-gap: 20px;
    width: 90%;
}
</style>