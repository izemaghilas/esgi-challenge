<script setup>
const props = defineProps({
    message: String
})
</script>

<template>
    <v-container class="container">
        <v-icon icon="mdi-alert-circle-outline" size="64"></v-icon>
        <span>{{ props.message }}</span>
    </v-container>
</template>

<style scoped>
.container {
    display: flex;
    flex-direction: column;
    align-items: center;
    row-gap: 10px;
}

.container span {
    font-size: 16px;
    font-weight: 600;
}
</style>