import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

export function useRole() {
    const page = usePage();
    const role = computed(() => page.props.auth.role);
    const isAdmin = computed(() => role.value === 'admin');
    const isEmployee = computed(() => role.value === 'employee');

    return { role, isAdmin, isEmployee };
}
