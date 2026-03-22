import { usePage } from '@inertiajs/vue3';
import { watch } from 'vue';
import { toast } from 'vue-sonner';

export function useFlashToast(): void {
    const page = usePage();

    watch(
        () => page.props.flash,
        (flash) => {
            if (flash.success) {
                toast.success(flash.success);
            }

            if (flash.error) {
                toast.error(flash.error);
            }
        },
        { immediate: true },
    );
}
