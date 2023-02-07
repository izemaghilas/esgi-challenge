import { createRouter, createWebHistory } from "vue-router";
import { Login, Signup } from "./views";
import APP_ROUTES from "./utils/routes";

const baseUrl = import.meta.env.BASE_URL ?? "";

const { admin: dashboardAdmin } = APP_ROUTES.dashboard.views;

const router = createRouter({
    history: createWebHistory(),
    routes: [
        { path: `${baseUrl}/login`, name: APP_ROUTES.login, component: Login },
        {
            path: `${baseUrl}/signup`,
            name: APP_ROUTES.signup,
            component: Signup,
        },
        {
            path: `${baseUrl}/dashboard`,
            name: APP_ROUTES.dashboard.name,
            component: null,
            children: [
                {
                    path: "admin",
                    name: dashboardAdmin.name,
                    redirect: `${baseUrl}/dashboard/admin/users`,
                    component: null,
                    children: [
                        {
                            path: "users",
                            name: dashboardAdmin.views.users,
                            component: null,
                        },
                        {
                            path: "courses",
                            name: dashboardAdmin.views.courses,
                            component: null,
                        },
                        {
                            path: "comments",
                            name: dashboardAdmin.views.comments,
                            component: null,
                        },
                    ],
                },
            ],
        },
        {
            path: "/:pathMatch(.*)*",
            redirect: { name: "login", replace: true },
        },
    ],
});

export default router;
