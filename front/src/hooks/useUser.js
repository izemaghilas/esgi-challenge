import { inject, ref, watch } from "vue";

export default function useUser() {
    const { state } = inject("store");
    const userRef = ref({ ...state.user });

    watch(
        () => state.user,
        (newState) => {
            userRef.value = { ...newState };
        },
        { deep: true }
    );

    return userRef.value;
}
