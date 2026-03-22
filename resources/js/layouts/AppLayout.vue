<script setup lang="ts">
import { Toaster } from 'vue-sonner';
import { useFlashToast } from '@/composables/useFlashToast';
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItem } from '@/types';

type Props = {
    breadcrumbs?: BreadcrumbItem[];
};

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

useFlashToast();
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <slot />
    </AppLayout>

    <Toaster
        position="top-right"
        :duration="4000"
        rich-colors
        close-button
        :toast-options="{ style: { zIndex: '50' } }"
        :style="{ '--offset': '16px', zIndex: 50 }"
    />
</template>
