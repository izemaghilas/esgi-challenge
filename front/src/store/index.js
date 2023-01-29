import { reactive, readonly } from "vue";

const state = reactive({
    user: null,
});

function signup(user) {}
function login(user) {}
function logout() {}

export default {
    state: readonly(state),
    signup,
    login,
    logout,
};
