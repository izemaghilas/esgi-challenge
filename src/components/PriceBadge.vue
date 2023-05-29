<script setup>
const props = defineProps({
    class: String,
    price: Number
})
const { price } = props
</script>

<template>
    <v-chip :class="class" color="primary" size="small">
        {{ price == null || price === 0 ? 'gratuit' : `${price} €` }}
    </v-chip>
</template>