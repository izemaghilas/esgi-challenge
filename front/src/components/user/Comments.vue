<template>
    <div v-if="loading">
        <Loader />
    </div>
    <v-list v-else lines="two">
        <v-container class="no-comments-container" v-if="data.comments.length === 0">
            <v-icon icon="mdi-comment" size="30"></v-icon>
            <span class="ml-5">pas de commentaires</span>
        </v-container>
        <v-list-item class="comment" v-for="comment in data.comments" :key="comment.id"
            prepend-avatar="https://www.pngmart.com/files/22/User-Avatar-Profile-Download-PNG-Isolated-Image.png"
            :title="getFullName(comment.commenterId)" :subtitle="comment.content">
        </v-list-item>
    </v-list>
</template>

<script setup>
import Loader from '../Loader.vue';
import { reactive, onMounted, computed, ref } from "vue"
import useApi from '../../hooks/useApi';

const api = useApi()
const props = defineProps(['courseId'])
const loading = ref(false);

const getFullName = (user) => {
    return user.firstname + " " + user.lastname;
}

const data = reactive({
    comments: [],
})

onMounted(async () => {
    try {
        loading.value = true
        const response = await api.getCommentsByCourse(props.courseId)
        console.log(response)
        data.comments = response
    } catch (error) {
        console.log(error)
    } finally {
        loading.value = false
    }
})

computed
</script>

<style scoped>
.comment {
    margin: 10px;
    padding: 10px;
    border-bottom: 1px solid #ccc;
    border-radius: 5px;
}
</style>