<script setup>
import { reactive, ref } from "vue"
import useApi from "../hooks/useApi"
import useStoreActions from "../hooks/useStoreActions"
import { useRouter } from 'vue-router'
import { APP_ROUTES } from "../utils/constants";
import { getUserRedirectionPage } from "../utils"

const api = useApi()
const storeActions = useStoreActions()
const emailRef = ref("")
const passwordRef = ref("")
const router = useRouter()
const loginError = reactive({
    message: ''
})
const loading = reactive({
    isLoading: false
})

async function login() {
    try {
        loading.isLoading = true
        const data = await api.login(emailRef.value, passwordRef.value)
        if (data.user.isActive === false) {
            loginError.message = "Veuillez confirmer votre compte"
        } else {
            storeActions.login({ token: data.token, ...data.user })
            router.push({ name: getUserRedirectionPage(data.user), replace: true })
        }
    } catch (error) {
        loginError.message = "Email ou mot de passe incorrect"
        console.error("error on login user");
    } finally {
        loading.isLoading = false
    }
}
</script>

<template>
    <v-container>
        <div class="login-form">
            <h1>Se connecter</h1>
            <p class="error-message">{{ loginError.message }}</p>
            <form @submit.prevent="login">
                <div class="form-group">
                    <label for="firstName">Email:</label>
                    <input type="email" id="firstName" v-model="emailRef" required />
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" v-model="passwordRef" required />
                </div>
                <button type="submit">
                    <div v-if="loading.isLoading">
                        <v-progress-circular class="loader" indeterminate color="red"></v-progress-circular>
                    </div>
                    <div v-else>
                        Se connecter
                    </div>
                </button>
            </form>
            <div class="d-flex flex-row justify-space-between w-100 px-2">
                <RouterLink class="login" :to="{ name: APP_ROUTES.signup, replace: true }">S'inscrire</RouterLink>
                <RouterLink class="login" :to="{ name: APP_ROUTES.forgetPassword, replace: true }">Mot de passe oublié</RouterLink>
            </div>
        </div>
    </v-container>
</template>

<style scoped>
.login-form {
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

label {
    font-weight: bold;
    margin-bottom: 10px;
}

.login {
    display: block;
    margin-top: 20px;
    text-align: center;
    text-decoration: none;
    color: #535693;
    padding: 3px 0;
    border-radius: 5px;
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

@media (max-width: 768px) {
    .register-form {
        width: 96%;
    }
}
</style>