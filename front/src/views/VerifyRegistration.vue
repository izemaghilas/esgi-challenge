<script setup>
import { useRoute, useRouter } from 'vue-router';
import { onMounted, ref } from 'vue';
import { toast } from 'vue3-toastify'
import Loader from '../components/Loader.vue';
import useApi from '../hooks/useApi';
import { APP_ROUTES } from '../utils/constants';

const router = useRouter()
const route = useRoute()
const api = useApi()
const failedRef = ref(false)
const emailRef = ref("")
onMounted(async () => {
    // TODO: handle exceptions
    if (!failedRef.value) {
        try {
            await api.verifyRegistration(route.query['url'])
            toast("votre compte a bien été confimer", { type: 'success' })
            router.replace({ name: APP_ROUTES.login, replace: true })
        } catch (error) {
            failedRef.value = true
            toast("lien expiré ou compte introuvable!", { type: 'error' })
        }
    }
})

async function sendVerificationEmail() {

}
</script>

<template>
    <div class="loading" v-if="!failedRef">
        <Loader />
    </div>
    <v-sheet class="mx-auto" v-else>
        <div class="form-container">
            <span>Si vous êtes déjà inscrit, veuillez saisir votre adresse mail pour confirmer votre compte</span>
            <v-form @submit.prevent class="form">
                <v-text-field v-model="emailRef" label="adresse mail"></v-text-field>
                <v-btn color="info" @click="sendVerificationEmail">envoyer</v-btn>
            </v-form>
        </div>
    </v-sheet>
</template>

<style scoped>
.loading {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

.form-container {
    display: flex;
    flex-direction: column;
    row-gap: 25px;
    width: 100%;
    padding: 50px;
}
.form {
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
}
</style>