<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import CreateWorkshopModal from '@/components/CreateWorkshopModal.vue';
import StatCard from '@/components/StatCard.vue';
import { Button } from '@/components/ui/button';
import WorkshopCard from '@/components/WorkshopCard.vue';
import WorkshopFiltersComponent from '@/components/WorkshopFilters.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { index } from '@/routes/workshops';
import type {
    BreadcrumbItem,
    Paginated,
    Workshop,
    WorkshopFilters,
    WorkshopStats,
} from '@/types';

type Props = {
    workshops: Paginated<Workshop>;
    filters: WorkshopFilters;
    stats: WorkshopStats;
};

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Workshops',
        href: index(),
    },
];

const showCreateModal = ref(false);
const search = ref(props.filters.search ?? '');
const startDate = ref(props.filters.start_date ?? '');
const endDate = ref(props.filters.end_date ?? '');

function applyFilters() {
    router.get(
        index.url(),
        {
            search: search.value || undefined,
            start_date: startDate.value || undefined,
            end_date: endDate.value || undefined,
        },
        { preserveState: true, preserveScroll: true },
    );
}

function resetFilters() {
    search.value = '';
    startDate.value = '';
    endDate.value = '';
    router.get(index.url(), {}, { preserveState: true });
}
</script>

<template>
    <Head title="Workshops" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4">
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-semibold tracking-tight">Workshops</h1>
                <Button
                    class="shadow-sm transition-all duration-200 hover:shadow-md"
                    @click="showCreateModal = true"
                >
                    Nuovo Workshop
                </Button>
            </div>

            <CreateWorkshopModal v-model:open="showCreateModal" />

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <StatCard title="Totale workshop" :value="stats.total" />
                <StatCard title="Completati" :value="stats.completed" />
                <StatCard title="In programma" :value="stats.upcoming" />
                <StatCard
                    title="Iscrizioni totali"
                    :value="stats.total_registrations"
                />
            </div>

            <WorkshopFiltersComponent
                v-model:search="search"
                v-model:start-date="startDate"
                v-model:end-date="endDate"
                @filter="applyFilters"
                @reset="resetFilters"
            />

            <div
                v-if="workshops.data.length > 0"
                class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4"
            >
                <WorkshopCard
                    v-for="workshop in workshops.data"
                    :key="workshop.id"
                    :workshop="workshop"
                />
            </div>

            <div
                v-else
                class="rounded-xl border border-sidebar-border/70 px-4 py-8 text-center text-muted-foreground dark:border-sidebar-border"
            >
                Nessun workshop trovato.
            </div>

            <div
                v-if="workshops.last_page > 1"
                class="flex items-center justify-center gap-1"
            >
                <template v-for="link in workshops.links" :key="link.label">
                    <Link
                        v-if="link.url"
                        :href="link.url"
                        preserve-state
                        preserve-scroll
                    >
                        <Button
                            :variant="link.active ? 'default' : 'outline'"
                            size="sm"
                        >
                            <span v-html="link.label" />
                        </Button>
                    </Link>
                    <Button v-else variant="outline" size="sm" disabled>
                        <span v-html="link.label" />
                    </Button>
                </template>
            </div>
        </div>
    </AppLayout>
</template>
