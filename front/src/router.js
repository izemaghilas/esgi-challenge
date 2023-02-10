import { createRouter, createWebHistory } from "vue-router";
import { APP_ROUTES } from "./utils/constants";

const baseUrl = import.meta.env.BASE_URL ?? "";

const { admin: dashboardAdmin } = APP_ROUTES.dashboard.views;

const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: `${baseUrl}/login`,
            name: APP_ROUTES.login,
            component: () => import("./views/Login.vue"),
        },
        {
            path: `${baseUrl}/signup`,
            name: APP_ROUTES.signup,
            component: () => import("./views/Signup.vue"),
        },
        {
            path: `${baseUrl}/dashboard`,
            name: APP_ROUTES.dashboard.name,
            component: () => import("./views/Dashboard.vue"),
            children: [
                {
                    path: "admin",
                    name: dashboardAdmin.name,
                    redirect: `${baseUrl}/dashboard/admin/users`,
                    component: () => import("./views/Admin.vue"),
                    children: [
                        {
                            path: "users",
                            name: dashboardAdmin.views.users,
                            component: () =>
                                import(
                                    "./components/dashboard/admin/Users.vue"
                                ),
                        },
                        {
                            path: "courses",
                            name: dashboardAdmin.views.courses,
                            component: () =>
                                import(
                                    "./components/dashboard/admin/Courses.vue"
                                ),
                        },
                        {
                            path: "comments",
                            name: dashboardAdmin.views.comments,
                            component: () =>
                                import(
                                    "./components/dashboard/admin/Comments.vue"
                                ),
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
