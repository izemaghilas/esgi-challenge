<template>
    <header>
        <nav class="navbar">
            <RouterLink :to="APP_ROUTES.home">
                <img style="width: 160px" src="../assets/logo.png" alt="masterclass">
            </RouterLink>
            <ul class="nav-menu">
                <li class="nav-item custom-header-li" v-for="link in links" :key="link.label">
                    <RouterLink class="custom-header-menu-text" :to="{ name: link.to, replace: true }"
                        active-class="active-link">
                        {{ link.label }}
                    </RouterLink>
                </li>
            </ul>
            <div class="hamburger">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </div>
        </nav>
    </header>
</template>

<script setup>
import { onMounted, computed, inject } from 'vue';
import { APP_ROUTES, ROLES } from '../utils/constants';
import { getUserRole } from '../utils';

const { state } = inject("store");

onMounted(() => {
    const hamburger = document.querySelector('.hamburger');
    const navMenu = document.querySelector('.nav-menu');

    hamburger.addEventListener('click', function () {
        hamburger.classList.toggle('active');
        navMenu.classList.toggle('active');
    });
});

const links = computed(() => {
    if (state.user != null) {
        const userRole = getUserRole(state.user)
        return {
            [APP_ROUTES.home]: {
                label: "ACCUEIL",
                to: APP_ROUTES.home
            },
            ...(userRole.value != ROLES.user.value && { [userRole.homepage]: { label: "MON ESPACE", to: userRole.homepage } }),
            [APP_ROUTES.logout]: {
                label: "DECONNEXION",
                to: APP_ROUTES.logout
            },
        }
    }
    return {
        [APP_ROUTES.home]: {
            label: "ACCUEIL",
            to: APP_ROUTES.home
        },
        [APP_ROUTES.login]: {
            label: "SE CONNECTER",
            to: APP_ROUTES.login
        },
        [APP_ROUTES.signup]: {
            label: "S'INSCRIRE",
            to: APP_ROUTES.signup
        },
    }
})


</script>

<style scoped>
.active-link {
    border-bottom: 2px solid #f4a118;
    padding-bottom: 5px;
}

* {
    padding: 0;
    margin: 0;
    box-sizing: border-box;
}

header {
    background-color: #ffffff;
}

.custom-header-li {
    list-style: none;
}

.navbar {
    min-height: 70px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 24px;
    color: #f4a118;
    background-color: #251d5d;
    z-index: 1000;
}

.custom-header-menu-text {
    color: #f4a118;
    text-decoration: none;
}

.nav-menu {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 60px;
}

.nav-branding {
    text-align: center;
    color: #f4a118;
}

.nav-link {
    transition: 0.7s ease;
}

.nav-link:hover {
    border-bottom: 3px solid #f4a118;
    font-size: 20px;
    transition: 0.5s;
    color: #f4a118;
}

.hamburger {
    display: none;
    cursor: pointer;
}

.bar {
    display: block;
    width: 25px;
    height: 3px;
    margin: 5px auto;
    -webkit-transition: all 0.3s ease-in-out;
    transition: all 0.3s ease-in-out;
    background-color: #f4a118;
}

@media(max-width:767px) {
    .hamburger {
        display: block;
    }

    .hamburger.active .bar:nth-child(2) {
        opacity: 0;
    }

    .hamburger.active .bar:nth-child(1) {
        transform: translateY(8px) rotate(45deg);
    }

    .hamburger.active .bar:nth-child(3) {
        transform: translateY(-8px) rotate(-45deg);
    }

    .nav-menu {
        position: fixed;
        z-index: 100;
        left: -100%;
        top: 70px;
        gap: 0;
        flex-direction: column;
        background-color: #373397;
        width: 100%;
        text-align: center;
        transition: 0.3s;
    }

    .nav-item {
        margin: 16px 0;
    }

    .nav-menu.active {
        left: 0;
    }

}
</style>