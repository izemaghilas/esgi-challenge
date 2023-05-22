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
const errorMessage = ref("")
const failed = ref(false)
const email = ref("")

onMounted(async () => {
    if (!failed.value) {
        try {
            await api.verifyRegistration(route.query['url'])
            toast("votre compte a bien été confimer", { type: 'success' })
            router.replace({ name: APP_ROUTES.login, replace: true })
        } catch (error) {
            if (error instanceof AxiosError) {
                if (error.response.status === 400) {
                    toast("lien expiré !", { type: 'error' })
                } else if (error.response.status === 404) {
                    toast("compte introuvable, veuillez  vous inscrire", { type: 'error' })
                    router.replace({ name: APP_ROUTES.signup, replace: true })
                } else {
                    toast("erreur lors de la confirmation de l'adresse mail !", { type: 'error' })
                }
            }
            else {
                toast("erreur lors de la confirmation de l'adresse mail !", { type: 'error' })
            }

        } finally {
            failed.value = true
        }
    }
})

async function sendVerificationEmail() {
    if (email.value.trim() === "") {
        errorMessage.value = "veuillez saisir votre adresse mail !"
        return
    }

    try {
        loading.value = true
        await api.sendVerificationEmail(email.value.trim())
    } catch (error) {
        if (error instanceof AxiosError) {
            if (error.response.status === 400) {
                toast("veuillez saisir une adresse mail valide !", { type: 'error', })
            } else if (error.response.status === 404) {
                toast("compte introuvable, veuillez  vous inscrire !", { type: 'error', })
                router.replace({ name: APP_ROUTES.signup, replace: true })
            } else {
                toast("erreur lors de l'envoi de lien !", { type: 'error', })
            }
        } else {
            toast("erreur lors de l'envoi de lien !", { type: 'error', })
        }
    } finally {
        loading.value = false
    }
}
</script>

<template>
    <v-container class="d-flex flex-column w-50 pa-15">
        <div class="loading" v-if="!failed">
            <Loader />
        </div>
        <div class="form-container" v-else>
            <h3>Confirmation de l'adresse mail</h3>
            <p class="error-message">{{ errorMessage }}</p>
            <v-form class="d-flex flex-column w-100 mt-4" @submit.prevent="sendVerificationEmail">
                <div class="form-group">
                    <label for="email">Veuillez saisir votre adresse mail pour envoyer le nouveau lien</label>
                    <input type="email" id="email" v-model="email" required />
                </div>
                <button type="submit">
                    <div v-if="loading">
                        <v-progress-circular class="loader" indeterminate color="red"></v-progress-circular>
                    </div>
                    <div v-else>
                        Envoyer
                    </div>
                </button>
            </v-form>
        </div>

    </v-container>
</template>

<style scoped>
.loading {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

.form-container {
    width: 100%;
    padding: 20px;
    border: 1px solid #ccc;
    border-radius: 5px;
    margin: 0 auto;
    justify-content: center;
    align-items: center;
    box-shadow: 12px 12px 2px 1px rgba(0, 0, 255, .2);
    background: rgba(255, 255, 255, 0.19);
    border-radius: 16px;
    box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
    backdrop-filter: blur(4.5px);
    -webkit-backdrop-filter: blur(4.5px);
    border: 1px solid rgba(255, 255, 255, 0.22);
}

.form-group {
    display: flex;
    flex-direction: column;
    margin-bottom: 20px;
    row-gap: 10px;
}

button[type="submit"] {
    width: 100%;
    height: 45px;
    justify-content: center;
    align-items: center;
    flex-direction: row;
    display: block;
    padding: 10px 20px;
    background-color: rgb(65, 65, 160);
    color: white;
    border-radius: 5px;
    border: none;
    cursor: pointer;
}

input[type="email"] {
    padding: 10px;
    font-size: 14px;
    border-radius: 5px;
    border: 1px solid #ccc;
}

.error-message {
    color: red;
    margin-top: 10px;
    margin-bottom: 10px
}
</style>