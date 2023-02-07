<template>
    <v-app-bar>
        <div class="logo">
            <span>Master Class</span>
        </div>
        <div class="wrapper-avatar">
            <v-avatar class="avatar" size="38" color="info">
                <v-icon icon="mdi-account-circle" size="large" />
            </v-avatar>
        </div>

    </v-app-bar>
</template>

<style scoped>
.logo {
    display: flex;
    flex-direction: row;
    justify-content: center;
    align-items: center;
    font-size: 16px;
    font-weight: bold;
    width: 130px;
    height: 40px;
    margin-left: 50px;
    border-radius: 20px;
}

.wrapper-avatar {
    display: flex;
    flex-direction: row;
    justify-content: flex-end;
    align-items: center;
    width: 85%;
    height: 100%;
}

.avatar {
    cursor: pointer;
}
</style>