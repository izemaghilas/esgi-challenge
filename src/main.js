import { createApp } from "vue";
import "vuetify/styles";
import { createVuetify } from "vuetify";
import * as components from "vuetify/components";
import * as directives from "vuetify/directives";
import Vue3Toasity, { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";
import App from "./App.vue";
import router from "./router";

const vuetify = createVuetify({
    components,
    directives,
});

createApp(App)
    .use(router)
    .use(vuetify)
    .use(Vue3Toasity, {
        autoClose: 3000,
        position: toast.POSITION.BOTTOM_RIGHT,
        limit: 1,
    })
    .mount("#app");
