<template>
    <div>
        <v-parallax class="banner"
            src="https://images.unsplash.com/photo-1508108712903-49b7ef9b1df8?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=2938&q=80">
            <div class="banner-content d-flex flex-column justify-center align-center text-white">
                <h1 class="glass">
                    MASTER CLASS
                </h1>
                <h3 class="subTitle">
                    Explorez l'excellence!
                </h3>

            </div>
        </v-parallax>
        <p style="text-align: center; margin-top: 30px; font-size: 20px;">
            Découvrez notre sélection de cours d'exception
            <br>
            Enrichissez vos compétences et votre inventivité grâce à notre plateforme de pointe,
            Masterclass.
        </p>
        <Courses heading="Les cours mis à jour récemment" />
    </div>
</template>

<script setup>
import Courses from '../components/user/Courses.vue'

</script>

<style scoped>
.banner {
    height: 100vh;
    width: 100%;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
}

.banner-content {
    margin-top: 20%;
}

.glass {
    font-size: 60px;
    font-weight: lighter;
    padding: 1rem 2rem;
    color: #6f6f6f;
}

.subTitle {
    margin-top: 10px;
    font-size: 30px;
    font-weight: lighter;
    color: #616161;
    border-bottom: 1px dashed #616161;
}

@media screen and (max-width: 600px) {
    .glass {
        font-size: 10vw;
    }
}

@supports (-webkit-backdrop-filter: none) or (backdrop-filter: none) {
    .glass {
        -webkit-backdrop-filter: blur(10px);
        backdrop-filter: blur(10px);
        background-color: rgba(255, 255, 255, 0.5);
    }
}
</style>