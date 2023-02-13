<script setup>
import { ref } from "vue"
import useApi from "../hooks/useApi"
import useStoreActions from "../hooks/useStoreActions"

const api = useApi()
const storeActions = useStoreActions()
const emailRef = ref("")
const passwordRef = ref("")

async function login() {
    try {
        const data = await api.login(emailRef.value, passwordRef.value)
        storeActions.login(data)
    } catch (error) {
        console.error("error on login user");
    }
}
</script>

<template>
    <v-sheet width="300" class="mx-auto">
        <v-form @submit.prevent>
            <v-text-field v-model="emailRef" label="email" type="email"></v-text-field>
            <v-text-field v-model="passwordRef" label="mot de passe" type="password"></v-text-field>
            <v-btn type="submit" block class="mt-2" @click="login">s'identifier</v-btn>
        </v-form>
    </v-sheet>
</template>