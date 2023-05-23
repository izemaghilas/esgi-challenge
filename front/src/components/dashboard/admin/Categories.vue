<script setup>
import { ref, onMounted } from 'vue';
import { toast } from 'vue3-toastify'
import useApi from '../../../hooks/useApi';
import Loader from '../../Loader.vue';

const api = useApi()
const categories = ref([])
const loading = ref(false)

const dialog = ref(false)
const createCategoryForm = ref(false)
const categoryTitle = ref()
const createCategoryLoading = ref(false)

onMounted(async () => {
    loading.value = true
    try {
        categories.value = await api.getAllCategories()
    } catch (error) {
        console.error("error fetching categories")
    } finally {
        loading.value = false
    }
})

async function createCategory() {
    if (createCategoryForm.value) {
        createCategoryLoading.value = true
        try {
            const category = await api.createCategory(categoryTitle.value)
            categories.value = [...categories.value, category]
        } catch (error) {
            console.error("error creating category")
            toast('erreur lors de la création de la catérorie', { type: 'error' })
        } finally {
            createCategoryForm.value = false
            createCategoryLoading.value = false
            dialog.value = false
        }
    }
}
</script>

<template>
    <v-container class="d-flex flex-column p-0 h-100">
        <Loader v-if="loading" />
        <div class="d-flex flex-column" v-else>
            <div>
                <v-btn color="info" @click="dialog = true">nouvelle catégorie</v-btn>
                <v-spacer></v-spacer>
            </div>
            <v-card class="mt-10">
                <v-list density="compact" lines="two">
                    <v-list-item v-for="category in categories" :key="category.id">
                        <v-list-item-title class="font-weight-bold"
                            v-text="category.title.charAt(0).toUpperCase() + category.title.slice(1)"></v-list-item-title>
                        <v-divider class="mt-2" color="#000" thickness="2"></v-divider>
                    </v-list-item>
                </v-list>
            </v-card>
        </div>
        <v-dialog class="align-center mx-auto" v-model="dialog">
            <v-sheet class="d-flex flex-column align-center mx-auto w-50 px-5">
                <v-form class="w-100" v-model="createCategoryForm" @submit.prevent="createCategory">
                    <v-container>
                        <v-text-field label="Titre" id="titre" v-model.trim="categoryTitle"
                            :rules="[value => !!value || 'Veuillez saisir un titre']"></v-text-field>
                        <div class="d-flex flex-row justify-end mt-5 w-100">
                            <v-btn color="info" type="submit" :loading="createCategoryLoading">créer</v-btn>
                        </div>
                    </v-container>
                </v-form>
            </v-sheet>

        </v-dialog>
    </v-container>
</template>