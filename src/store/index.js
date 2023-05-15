import { reactive, readonly } from "vue";
import useStorage from "../hooks/useStorage";

const storage = useStorage();

const state = reactive({
    user: storage.get("user") ?? null,
});

function signup(user) {}
function login(data) {
    state.user = data;
    storage.set("user", state.user);
}
function logout() {
    state.user = null
    storage.remove("user")
}

export default {
    state: readonly(state),
    actions: {
        signup,
        login,
        logout,
    },
};
