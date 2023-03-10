export default function useStorage() {
    function get(key) {
        return JSON.parse(localStorage.getItem(key));
    }
    function set(key, value) {
        localStorage.setItem(key, JSON.stringify(value));
    }
    function remove(key) {
        localStorage.removeItem(key);
    }

    return { get, set, remove };
}
