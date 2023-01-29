import { createApp } from "vue";
import { VueQueryPlugin } from "@tanstack/vue-query";
import { createRouter, createWebHistory } from "vue-router";
import "vuetify/styles";
import { createVuetify } from "vuetify";
import * as components from "vuetify/components";
import * as directives from "vuetify/directives";
import App from "./App.vue";
import { Login, Signup } from "./views";

const baseUrl = import.meta.env.BASE_URL ?? "";
const router = createRouter({
    history: createWebHistory(),
    routes: [
        { path: `${baseUrl}/login`, name: "login", component: Login },
        { path: `${baseUrl}/signup`, name: "signup", component: Signup },
        {
            path: "/:pathMatch(.*)*",
            redirect: { name: "login", replace: true },
        },
    ],
});
const vuetify = createVuetify({
    components,
    directives,
});
createApp(App).use(router).use(VueQueryPlugin).use(vuetify).mount("#app");
