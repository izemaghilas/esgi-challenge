import { inject, ref, watch } from "vue";
import axios from "axios";

const axiosInstance = axios.create({
    baseURL: import.meta.env.APP_API_URL,
});

function getRequestHeaders(
    options = { token: null, withBody: false, withLdJson: false }
) {
    const { token, withBody, withLdJson } = options;

    return {
        "Content-Type": withBody
            ? withLdJson
                ? "application/ld+json"
                : "application/json"
            : undefined,
        Authorization: token != null ? `Bearer ${token}` : undefined,
    };
}

const apiClient = {
    get: function (url, token = null) {
        return axiosInstance.get(url, {
            headers: getRequestHeaders({ token: token }),
        });
    },

    post: function (
        url,
        options = { data: null, withLdJson: false, token: null }
    ) {
        const { data, withLdJson, token } = options;
        return axiosInstance.post(url, data, {
            headers: getRequestHeaders({
                token: token,
                withBody: data != null,
                withLdJson: withLdJson,
            }),
        });
    },

    put: function (
        url,
        options = { data: null, withLdJson: false, token: null }
    ) {
        const { data, withLdJson, token } = options;
        return axiosInstance.put(url, data, {
            headers: getRequestHeaders({
                token: token,
                withBody: data != null,
                withLdJson: withLdJson,
            }),
        });
    },

    patch: function (
        url,
        options = { data: null, withLdJson: false, token: null }
    ) {
        const { data, withLdJson, token } = options;
        return axiosInstance.patch(url, data, {
            headers: getRequestHeaders({
                token: token,
                withBody: data != null,
                withLdJson: withLdJson,
            }),
        });
    },

    delete: function (url, token = null) {
        return axiosInstance.delete(url, {
            headers: getRequestHeaders({ token: token }),
        });
    },
};

function constructRequestUrl(endpoint, params = null) {
    const keys = params != null ? Object.keys(params) : [];
    return `${endpoint}${
        keys.length > 0 ? keys.map((k) => `${k}=${params[k]}`).join("&") : ""
    }`;
}

export default function useApi() {
    const store = inject("store");
    const userRef = ref(store.state.user);

    watch(store.state.user, (user, prev) => {
        userRef.value = user;
    });

    function login(email, password) {
        return apiClient.post(constructRequestUrl("login"), {
            data: {
                email: email,
                password: password,
            },
            withLdJson: false,
            token: userRef?.value?.token,
        });
    }

    return { signup, login, logout };
}
