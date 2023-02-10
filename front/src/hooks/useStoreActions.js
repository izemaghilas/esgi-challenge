import { inject } from "vue";

export default function useStoreActions() {
    const { actions } = inject("store");

    return { ...actions };
}
