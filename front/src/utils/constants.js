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

export { COLORS, APP_ROUTES, ROLES };
