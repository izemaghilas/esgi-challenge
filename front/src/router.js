import { createRouter, createWebHistory } from "vue-router";
import { APP_ROUTES } from "./utils/constants";

const baseUrl = import.meta.env.BASE_URL === "/" ? "" : import.meta.env.BASE_URL;

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
          path: "contributor",
          name: APP_ROUTES.dashboard.views.contributor,
          component: () =>
          import("./views/Contributor.vue"),
        },
        {
          path: `${baseUrl}/reviewer`,
          name: APP_ROUTES.dashboard.views.reviewer,
          component: () => import("./views/Reviewer.vue"),
        },
        {
          path: "admin",
          name: dashboardAdmin.name,
          redirect: `${baseUrl}/dashboard/admin/courses`,
          component: () => import("./views/Admin.vue"),
          children: [
            {
              path: "courses",
              name: dashboardAdmin.views.courses,
              component: () =>
                import("./components/dashboard/admin/Courses.vue"),
            },
            {
              path: "validation-requests",
              name: dashboardAdmin.views.validationRequests,
              component: () => import('./components/dashboard/admin/ValidationRequests.vue')
            },
            {
              path: "comments",
              name: dashboardAdmin.views.comments,
              component: () =>
                import("./components/dashboard/admin/Comments.vue"),
            },
            {
              path: 'be-reviewer',
              name: dashboardAdmin.views.beReviewer,
              component: () => import("./components/dashboard/admin/BeReviewer.vue"),
            },
            {
              path: 'categories',
              name: dashboardAdmin.views.categories,
              component: () => import("./components/dashboard/admin/Categories.vue"),
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
      path: `${baseUrl}/list/:id/:name`,
      name: APP_ROUTES.list,
      component: () => import("./components/user/CourseList.vue"),
    },
    {
      path: `${baseUrl}/verify-registration`,
      name: APP_ROUTES.verifyRegistration,
      component: () => import("./views/VerifyRegistration.vue")
    },
    {
      path: `${baseUrl}/forget-password`,
      name: APP_ROUTES.forgetPassword,
      component: () => import("./views/ForgetPassword.vue")
    },
    {
      path: `${baseUrl}/reset-password`,
      name: APP_ROUTES.resetPassword,
      component: () => import("./views/ResetPassword.vue")
    },
    {
      path: "/:pathMatch(.*)*",
      redirect: { name: "home", replace: true },
    },
]});

export default router;
