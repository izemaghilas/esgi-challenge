<script setup>
import { useRoute, useRouter } from 'vue-router';
import useApi from '../hooks/useApi';
import { onMounted, ref } from 'vue';
import { toast } from 'vue3-toastify';
import { APP_ROUTES } from '../utils/constants';
import { AxiosError } from 'axios';


const router = useRouter()
const route = useRoute()
const api = useApi()
const loading = ref(false)
const resetPasswordErrorMessage = ref("")
const password = ref("")
const passwordConfirm = ref("")

async function resetPassword() {
    if (password.value !== passwordConfirm.value) {
        resetPasswordErrorMessage.value = "Les mots de passe ne correspondent pas."
        return
    }

    try {
        loading.value = true
        await api.resetPassword(password.value, route.query['url'])
        toast("votre mot passe a bien été réinitialiser", { type: 'success' })
        router.replace({ name: APP_ROUTES.login, replace: true })
    } catch (error) {
        if (error instanceof AxiosError) {
            if (error.response.status === 400) {
                toast("lien expiré !", { type: 'error' })
                router.replace({ name: APP_ROUTES.forgetPassword, replace: true })
            } else if (error.response.status === 404) {
                toast("compte introuvable, veuillez  vous inscrire !", { type: 'error' })
                router.replace({ name: APP_ROUTES.signup, replace: true })
            } else if (error.response.status === 422) {
                resetPasswordErrorMessage.value = "Le mot de passe se retrouve dans la liste des mots de passe les plus utilisés, Veuillez en choisir un autre. !"
            } else {
                resetPasswordErrorMessage.value = "erreur lors de la réinitialisation de mot de passe !"
            }
        } else {
            resetPasswordErrorMessage.value = "erreur lors de la réinitialisation de mot de passe !"
        }
    } finally {
        loading.value = false
    }
}

</script>

<template>
    <v-container>
        <div class="form-container">
            <h3>Créer votre nouveau mot de passe</h3>
            <p class="error-message">{{ resetPasswordErrorMessage }}</p>
            <v-form class="d-flex flex-column w-100 mt-6" @submit.prevent="resetPassword">
                <div class="form-group">
                    <label for="password">Nouveau mot de passe:</label>
                    <input type="password" id="password" v-model="password" required />
                </div>
                <div class="form-group">
                    <label for="passwordConfirmation">Confirmer le nouveau mot de passe:</label>
                    <input type="password" id="passwordConfirmation" v-model="passwordConfirm" required />
                </div>
                <button type="submit">
                    <div v-if="loading">
                        <v-progress-circular class="loader" indeterminate color="red"></v-progress-circular>
                    </div>
                    <div v-else>
                        Valider
                    </div>
                </button>
            </v-form>
        </div>
    </v-container>
</template>

<style scoped>
.form-container {
    width: 500px;
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

input[type="text"],
input[type="email"],
input[type="password"] {
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