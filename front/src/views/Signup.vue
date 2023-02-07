<script setup>
import { ref, reactive } from "vue"
import { useMutation } from '@tanstack/vue-query';
import useApi from '../hooks/useApi';
import { useRouter } from 'vue-router'

const api = useApi()
const router = useRouter()

const registerError = reactive({
    message: ''
})

const mutation = useMutation({
    mutationKey: ['register'],
    mutationFn: function ({ email, password, firstName, lastName }) { return api.register(email, password, firstName, lastName) },
    onSuccess: function (response) {
        if (response.status === 201) {
            router.push('/login')
        }
    },
    onError: function (error) {
        if (error.response.status === 422) {
            registerError.message = "Le mot de passe se retrouve dans la liste des mots de passe les plus utilisés, Veuillez en choisir un autre."
        }
        if (error.response.status === 500) {
            registerError.message = "Le email est déjà utilisé, Veuillez saisir un autre email."
        }

    }
})

const firstNameRef = ref()
const lastNameRef = ref()
const emailRef = ref()
const passwordRef = ref()

function register() {
    mutation.mutate({ email: emailRef.value, password: passwordRef.value, firstName: firstNameRef.value, lastName: lastNameRef.value })
}
</script>

<template>
    <div class="register-form">
        <h1>S'inscrire</h1>
        <p class="error-message">{{ registerError.message }}</p>
        <form @submit.prevent="register">
            <div class="form-group">
                <label for="firstName">Prénom:</label>
                <input type="text" id="firstName" v-model="firstNameRef" required />
            </div>
            <div class="form-group">
                <label for="lastName">Nom:</label>
                <input type="text" id="lastName" v-model="lastNameRef" required />
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" v-model="emailRef" required />
            </div>
            <div class="form-group">
                <label for="password">Mot de passe:</label>
                <input type="password" id="password" v-model="passwordRef" required />
            </div>
            <button type="submit">Submit</button>
        </form>
    </div>
</template>

<style>
.register-form {
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

button[type="submit"] {
    padding: 10px 20px;
    background-color: blue;
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
