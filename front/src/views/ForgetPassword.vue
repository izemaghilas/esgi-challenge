<script setup>
import { ref } from 'vue';
import { toast } from 'vue3-toastify';
import { AxiosError } from 'axios';
import { useRouter } from 'vue-router';
import useApi from '../hooks/useApi';
import { APP_ROUTES } from '../utils/constants';

const router = useRouter()
const api = useApi()
const mailSent = ref(false)
const errorMessage = ref("")
const loading = ref(false)
const emailRef = ref("")

async function sendMail() {
    if (emailRef.value.trim() === "") {
        errorMessage.value = "veuillez saisir une adresse mail !"
        return
    }

    try {
        loading.value = true
        await api.sendResetPasswordMail(emailRef.value.trim())
        mailSent.value = true
    } catch (error) {
        if (error instanceof AxiosError) {
            if (error.response.status === 400) {
                errorMessage.value = "veuillez saisir une adresse mail valide !"
            } else if (error.response.status === 404) {
                toast("compte introuvable, veuillez  vous inscrire !", { type: 'error', position: 'top-right' })
                router.replace({ name: APP_ROUTES.signup, replace: true })
            } else {
                errorMessage.value = "erreur lors de l'envoi de lien !"
            }
        } else {
            errorMessage.value = "erreur lors de l'envoi de lien !"
        }
    } finally {
        loading.value = false
    }
}

</script>

<template>
    <v-container class="d-flex flex-column w-50 pa-15">
        <div v-if="mailSent">
            <div class="form-container">
                <h1>Confirmation d'envoi</h1>
                <div class="mt-5">
                    Un lien sécurisé vous permettant de créer votre nouveau mot de passe a été envoyé à l'adresse mail
                    saisie.
                    <br />
                    Vous n'avez pas reçu d'email ? Vérifiez que celui-ci n'est pas dans votre dossier « SPAM » ou « courrier
                    indésirable ».
                </div>
            </div>
        </div>
        <div class="form-container" v-else>
            <h3>Réinitialiser mon mot de passe</h3>
            <p class="error-message">{{ errorMessage }}</p>
            <v-form class="d-flex flex-column w-100 mt-4" @submit.prevent="sendMail">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" v-model="emailRef" required />
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