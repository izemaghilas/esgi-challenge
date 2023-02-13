<template>
    <header>
        <nav class="navbar">
            <img style="width: 160px" src="../assets/logo.png" alt="masterclass">
            <ul class="nav-menu">
                <li class="nav-item custom-header-li">
                    <RouterLink class="custom-header-menu-text" :to="APP_ROUTES.home"
                        exact-active-class="active-link">ACCUEIL</RouterLink>
                </li>
                <li class="nav-item custom-header-li">
                    <RouterLink class="custom-header-menu-text" :to="APP_ROUTES.login"
                        exact-active-class="active-link">SE CONNECTER
                    </RouterLink>
                </li>
                <li class="nav-item custom-header-li">
                    <RouterLink class="custom-header-menu-text" :to="APP_ROUTES.signup"
                        exact-active-class="active-link">S'INSCRIRE
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

<script>
import { onMounted } from 'vue';
import { APP_ROUTES } from '../utils/constants';

export default {
    setup() {
        onMounted(() => {
            const hamburger = document.querySelector('.hamburger');
            const navMenu = document.querySelector('.nav-menu');

            hamburger.addEventListener('click', function () {
                hamburger.classList.toggle('active');
                navMenu.classList.toggle('active');
            });
        });

        return { APP_ROUTES }
    },
}
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