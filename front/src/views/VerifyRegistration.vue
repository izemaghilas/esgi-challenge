<script setup>
import { useRoute, useRouter } from 'vue-router';
import { onMounted, ref } from 'vue';
import { toast } from 'vue3-toastify'
import Loader from '../components/Loader.vue';
import useApi from '../hooks/useApi';
import { APP_ROUTES } from '../utils/constants';
import { AxiosError } from 'axios';

const router = useRouter()
const route = useRoute()
const api = useApi()
const loading = ref(false)
const failedRef = ref(false)
const emailRef = ref("")

onMounted(async () => {
    if (!failedRef.value) {
        try {
            await api.verifyRegistration(route.query['url'])
            toast("votre compte a bien été confimer", { type: 'success' })
            router.replace({ name: APP_ROUTES.login, replace: true })
        } catch (error) {
            if (error instanceof AxiosError) {
                if (error.response.status === 400) {
                    toast("lien expiré !", { type: 'error', position: 'top-right' })
                } else if (error.response.status === 404) {
                    toast("compte introuvable, veuillez  vous inscrire", { type: 'error', position: 'top-right' })
                    router.replace({ name: APP_ROUTES.signup, replace: true })
                } else {
                    toast("erreur lors de la confirmation de l'adresse mail !", { type: 'error', position: 'top-right' })
                }
            }
            else {
                toast("erreur lors de la confirmation de l'adresse mail !", { type: 'error', position: 'top-right' })
            }
            failedRef.value = true
        }
    }
})

async function sendVerificationEmail() {
    if (emailRef.value.trim() !== "") {
        try {
            loading.value = true
            await api.sendVerificationEmail(emailRef.value.trim())
        } catch (error) {
            if (error instanceof AxiosError) {
                if (error.response.status === 400) {
                    toast("veuillez saisir une adresse mail valide !", { type: 'error', position: 'top-right' })
                } else if (error.response.status === 404) {
                    toast("compte introuvable, veuillez  vous inscrire !", { type: 'error', position: 'top-right' })
                    router.replace({ name: APP_ROUTES.signup, replace: true })
                } else {
                    toast("erreur lors de l'envoi de lien !", { type: 'error', position: 'top-right' })
                }
            } else {
                toast("erreur lors de l'envoi de lien !", { type: 'error', position: 'top-right' })
            }
        } finally {
            loading.value = false
        }
    }
}
</script>

<template>
    <div class="loading" v-if="!failedRef">
        <Loader />
    </div>
    <v-sheet class="mx-auto" v-else>
        <div class="form-container">
            <span>Veuillez saisir votre adresse mail pour envoyer le nouveau lien</span>
            <v-form validate-on="submit" @submit.prevent="sendVerificationEmail" class="form">
                <v-text-field v-model="emailRef" label="adresse mail" autofocus
                    :rules="[value => !!value.trim() || 'veuillez saisir votre adresse mail']"></v-text-field>
                <v-btn color="info" v-if="loading === true">
                    <v-progress-circular class="loader" indeterminate color="white"></v-progress-circular>
                </v-btn>
                <v-btn type="submit" color="info" v-else>envoyer</v-btn>
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