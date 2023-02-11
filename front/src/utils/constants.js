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

const ROLES = {
  admin: {
    label: "administrateur",
    value: "ROLE_ADMIN",
  },
  reviewer: {
    label: "examinateur",
    value: "ROLE_REVIEWER",
  },
  contributor: {
    label: "contributeur",
    value: "ROLE_CONTRIBUTOR",
  },
  user: {
    label: "utilisateur",
    value: "ROLE_USER",
  },
};

export { APP_ROUTES, ROLES };
