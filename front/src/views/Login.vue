<script setup>
import { ref } from "vue"
import { useMutation } from '@tanstack/vue-query';
import useApi from '../hooks/useApi';

const api = useApi()
const mutation = useMutation({
    mutationKey: ['login'],
    mutationFn: function ({ email, password }) { return api.login(email, password) }
})

const emailRef = ref()
const passwordRef = ref()

function login() {
    mutation.mutate({ email: emailRef.value, password: passwordRef.value })
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