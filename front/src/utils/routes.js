const APP_ROUTES = {
  signup: "signup",
  login: "login",
  home: "home",
  dashboard: {
    name: "dashboard",
    views: {
      admin: {
        name: "admin",
        views: {
          users: "users",
          courses: "courses",
          comments: "comments",
        },
      },
    },
  },
};

export default APP_ROUTES;
