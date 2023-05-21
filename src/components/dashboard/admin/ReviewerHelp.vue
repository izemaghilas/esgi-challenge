<script setup>
import { ref } from 'vue';

const props = defineProps({
    reviewer: Object,
    onHelp: Function
})
const { reviewer } = props
const onRequestHelpLoading = ref(false)
</script>

<template>
    <v-card class="d-flex flex-row align-center px-3 py-3 my-5 w-100">
        <div class="d-flex flex-row align-center w-25">
            <img src="https://www.pngmart.com/files/22/User-Avatar-Profile-Download-PNG-Isolated-Image.png" width="40"
                height="40" />
            <span class="ml-3">{{ `${reviewer.lastname} ${reviewer.firstname}` }}</span>
        </div>
        <div class="d-flex flex-row justify-end w-75">
            <v-btn color="info" :loading="onRequestHelpLoading"
                @click="() => { onRequestHelpLoading = true; onHelp(reviewer) }">envoyer</v-btn>
        </div>
    </v-card>
</template>