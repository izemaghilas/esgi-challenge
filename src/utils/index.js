import { ROLES } from "./constants";

export function getUserRole(user) {
    if (user.roles.includes("ROLE_ADMIN")) {
        return ROLES["admin"];
    } else if (user.roles.includes("ROLE_REVIEWER")) {
        return ROLES["reviewer"];
    } else if (user.roles.includes("ROLE_CONTRIBUTOR")) {
        return ROLES["contributor"];
    }
    return ROLES["user"];
}

export function getUserRedirectionPage(user) {
    return getUserRole(user).homepage
}