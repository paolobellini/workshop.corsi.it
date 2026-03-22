<script setup lang="ts">
import { RotateCcw, Search, SlidersHorizontal } from 'lucide-vue-next';
import { computed } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';

const search = defineModel<string>('search', { default: '' });
const startDate = defineModel<string>('startDate', { default: '' });
const endDate = defineModel<string>('endDate', { default: '' });

const emit = defineEmits<{
    filter: [];
    reset: [];
}>();

const hasActiveFilters = computed(
    () => search.value !== '' || startDate.value !== '' || endDate.value !== '',
);
</script>

<template>
    <div
        class="rounded-xl border border-sidebar-border/70 shadow-sm transition-all duration-300 dark:border-sidebar-border"
    >
        <div
            class="flex items-center gap-2 border-b border-sidebar-border/70 px-4 py-3 dark:border-sidebar-border"
        >
            <SlidersHorizontal class="size-4 text-muted-foreground" />
            <span class="text-sm font-medium">Filtri</span>
            <span
                v-if="hasActiveFilters"
                class="size-2 rounded-full bg-primary"
            />
        </div>
        <div class="p-4">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end">
                <div class="relative flex-1">
                    <Search
                        class="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                    />
                    <Input
                        v-model="search"
                        class="pl-9"
                        placeholder="Cerca per titolo..."
                        @keyup.enter="emit('filter')"
                    />
                </div>
                <div class="flex flex-1 gap-3">
                    <div class="flex-1">
                        <span
                            class="mb-1 block text-xs text-muted-foreground"
                        >
                            Da
                        </span>
                        <Input v-model="startDate" type="date" />
                    </div>
                    <div class="flex-1">
                        <span
                            class="mb-1 block text-xs text-muted-foreground"
                        >
                            A
                        </span>
                        <Input v-model="endDate" type="date" />
                    </div>
                </div>
                <div class="flex gap-2">
                    <Button
                        class="gap-2 shadow-sm"
                        @click="emit('filter')"
                    >
                        <Search class="size-4" />
                        Filtra
                    </Button>
                    <Button
                        v-if="hasActiveFilters"
                        variant="ghost"
                        size="icon"
                        @click="emit('reset')"
                    >
                        <RotateCcw class="size-4" />
                        <span class="sr-only">Reset</span>
                    </Button>
                </div>
            </div>
        </div>
    </div>
</template>
