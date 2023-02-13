<script setup>
import { inject } from 'vue';

const { state } = inject("store");
</script>

<template>
    <v-container v-if="state.user == null">
        <span class="auth-info">Veuiller vous identifiez pour voir le contenu du cour</span>
    </v-container>
    <slot v-else></slot>
</template>

<style scoped>
.auth-info {
    font-size: 16px;
    font-weight: 600;
}
</style>