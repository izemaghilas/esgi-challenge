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
      path: `${baseUrl}/logout`,
      name: APP_ROUTES.logout,
      component: () => import("./views/Logout.vue"),
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
              component: () => import("./components/dashboard/admin/Users.vue"),
            },
            {
              path: "courses",
              name: dashboardAdmin.views.courses,
              component: () =>
                import("./components/dashboard/admin/Courses.vue"),
            },
            {
              path: "comments",
              name: dashboardAdmin.views.comments,
              component: () =>
                import("./components/dashboard/admin/Comments.vue"),
            },
          ],
        },
      ],
    },
    {
      path: `${baseUrl}/home`,
      name: APP_ROUTES.home,
      component: () => import("./views/Home.vue"),
    },
    {
      path: `${baseUrl}/course/:id`,
      name: APP_ROUTES.course,
      component: () => import("./components/user/CourseDetail.vue"),
    },
    {
      path: `${baseUrl}/list/:id`,
      name: APP_ROUTES.list,
      component: () => import("./components/user/CourseList.vue"),
    },
    {
      path: "/:pathMatch(.*)*",
      redirect: { name: "home", replace: true },
    },
    {
      path: `${baseUrl}/reviewer`,
      name: APP_ROUTES.reviewer,
      component: () => import("./views/Reviewer.vue"),
    },
  
    

     ]});

export default router;
