const APP_ROUTES = {
    signup: "signup",
    login: "login",
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
