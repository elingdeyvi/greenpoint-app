import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

export function usePermissions() {
    const page = usePage();
    const permissions = computed(() => page.props.auth?.permissions ?? []);
    const can = (perm) => permissions.value.includes(perm);

    return { permissions, can };
}
