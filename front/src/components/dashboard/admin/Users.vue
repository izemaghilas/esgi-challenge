<script setup>
import { ref, onMounted, computed } from "vue"
import useApi from "../../../hooks/useApi"
import Loader from "../../Loader.vue"
import { getUserRole } from "../../../utils"
import { ROLES } from "../../../utils/constants"

const api = useApi()
const users = ref([])
const loading = ref(false)
const dialog = ref(false)

const userId = ref("")
const firstname = ref("")
const lastname = ref("")
const role = ref()

onMounted(async () => {
    try {
        loading.value = true
        users.value = await api.getAllUsers()
    } catch (error) {
        console.error("error fetching users");
    } finally {
        loading.value = false
    }
})

const userRoles = computed(() => {
    return Object.values(ROLES);
})

function handleEditButtonClick(user) {
    dialog.value = true
    userId.value = user.id
    firstname.value = user.firstname
    lastname.value = user.lastname
    role.value = getUserRole(user)
}

async function editUser() {
    try {
        loading.value = true
        const editedUser = await api.editUser(userId.value, {
            firstname: firstname.value,
            lastname: lastname.value,
            role: role.value
        })
        users.value = users.value.map(u => {
            if (u.id === editedUser.id) {
                return editedUser
            }
            return u
        })
        // reset values
        userId.value = ""
        firstname.value = ""
        lastname.value = ""
        role.value = ""
        dialog.value = false
    } catch (error) {
        console.error("error on editing user");
    } finally {
        loading.value = false
    }
}

async function removeUser(user) {
    try {
        loading.value = true
        await api.removeUser(user.id)
        users.value = [...users.value.filter(u => u.id !== user.id)]
    } catch (error) {
        console.error("error on removing user");
    } finally {
        loading.value = false
    }
}
</script>

<template>
    <v-container class="container">
        <Loader v-if="loading" />
        <template v-else>
            <v-container class="no-users-container" v-if="users.length === 0">
                <v-icon icon="mdi-alert-circle-outline" size="64"></v-icon>
                <span>pas d'utilisateurs</span>
            </v-container>
            <v-card class="card-user" v-for="user in users" :key="user.id">
                <div class="user-informations">
                    <span class="user-name">{{ user.lastname }} {{ user.firstname }}</span>
                    <span class="user-role-email">{{ user.email }}</span>
                    <span class="user-role-email">{{ getUserRole(user).label }}</span>
                </div>
                <div class="card-user-tools">
                    <v-btn color="info" @click="handleEditButtonClick(user)">modifier</v-btn>
                    <v-btn color="error" @click="removeUser(user)">supprimer</v-btn>
                </div>
            </v-card>
            <v-dialog v-model="dialog">
                <v-sheet class="mx-auto form-container">
                    <v-form @submit.prevent>
                        <v-text-field v-model="lastname" label="nom"></v-text-field>
                        <v-text-field v-model="firstname" label="prénom"></v-text-field>
                        <v-select v-model="role" label="role" :items="userRoles" item-title="label" item-value="value">
                        </v-select>
                        <v-btn color="info" @click="editUser">envoyer</v-btn>
                    </v-form>
                </v-sheet>
            </v-dialog>
        </template>
    </v-container>
</template>

<style scoped>
.container {
    display: flex;
    flex-direction: column;
    row-gap: 30px;
    padding: 0;
}

.no-users-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    row-gap: 10px;
}

.no-users-container span {
    font-size: 16px;
    font-weight: 600;
}

.card-user {
    display: flex;
    flex-direction: row;
    align-items: center;
    height: 100px;
    padding-top: 20px;
    padding-bottom: 20px;
}

.user-informations {
    display: flex;
    flex-direction: column;
    row-gap: 5px;
    margin-left: 20px;
    width: 40%
}

.user-name {
    font-size: 18px;
    font-weight: 600;
}

.user-role-email {
    font-size: 12px;
    font-weight: 600;
}

.card-user-tools {
    display: flex;
    flex-direction: row;
    justify-content: flex-end;
    column-gap: 20px;
    width: 55%;
}

.form-container {
    width: 50%;
    padding: 50px;
}
</style>