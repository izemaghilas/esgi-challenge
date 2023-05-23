const COLORS = {
  main: "#251d5d",
  secondary: "#f4a118",
  third: "#2e5a9e",
  brown: "#8f7667",
  white: "#ffffff",
  red: "#ff0000",
  green: "#00ff00",
  blue: "#0000ff",
  black: "#000000",
  grey: "#808080",
};

const APP_ROUTES = {
  signup: "signup",
  login: "login",
  logout: "logout",
  home: "home",
  course: "course",
  list: "list",
  dashboard: {
    name: "dashboard",
    views: {
      contributor:"contributor",
      reviewer: "reviewer",
      admin: {
        name: "admin",
        views: {
          courses: "courses",
          validationRequests: "validation-requests",
          beReviewer: "be-reviewer",
          comments: "comments",
          categories: "categories",
        },
      },
    },
  },
  verifyRegistration: "verify-registration",
  forgetPassword: "forget-password",
  resetPassword: "reset-password",
};

const ROLES = {
  admin: {
    label: "administrateur",
    value: "ROLE_ADMIN",
    homepage: "admin",
  },
  reviewer: {
    label: "examinateur",
    value: "ROLE_REVIEWER",
    homepage: "reviewer",
  },
  contributor: {
    label: "contributeur",
    value: "ROLE_CONTRIBUTOR",
    homepage: "contributor",
  },
  user: {
    label: "utilisateur",
    value: "ROLE_USER",
    homepage: "home",
  },
};

export { COLORS, APP_ROUTES, ROLES };
