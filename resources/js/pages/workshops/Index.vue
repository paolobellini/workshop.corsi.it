<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { index, show } from '@/routes/workshops';
import type { BreadcrumbItem } from '@/types';

type Workshop = {
    id: number;
    title: string;
    description: string | null;
    starts_at: string;
    ends_at: string;
    capacity: number;
    available_seats: number;
    is_full: boolean;
    registrations_count?: number;
    created_at: string;
    updated_at: string;
};

type PaginatedWorkshops = {
    data: Workshop[];
    links: { url: string | null; label: string; active: boolean }[];
    current_page: number;
    last_page: number;
};

type Filters = {
    search?: string;
    start_date?: string;
    end_date?: string;
};

type Props = {
    workshops: PaginatedWorkshops;
    filters: Filters;
};

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Workshops',
        href: index(),
    },
];

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

function formatDate(dateString: string): string {
    return new Date(dateString).toLocaleDateString('it-IT', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
}
</script>

<template>
    <Head title="Workshops" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4">
            <div
                class="flex flex-col gap-4 rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border"
            >
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="space-y-1">
                        <Label for="search">Cerca</Label>
                        <Input
                            id="search"
                            v-model="search"
                            placeholder="Cerca per titolo..."
                            @keyup.enter="applyFilters"
                        />
                    </div>
                    <div class="space-y-1">
                        <Label for="start_date">Data inizio</Label>
                        <Input
                            id="start_date"
                            v-model="startDate"
                            type="date"
                        />
                    </div>
                    <div class="space-y-1">
                        <Label for="end_date">Data fine</Label>
                        <Input id="end_date" v-model="endDate" type="date" />
                    </div>
                    <div class="flex items-end gap-2">
                        <Button @click="applyFilters">Filtra</Button>
                        <Button variant="outline" @click="resetFilters">
                            Reset
                        </Button>
                    </div>
                </div>
            </div>

            <div
                class="overflow-x-auto rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
            >
                <table class="w-full text-sm">
                    <thead>
                        <tr
                            class="border-b border-sidebar-border/70 dark:border-sidebar-border"
                        >
                            <th class="px-4 py-3 text-left font-medium">
                                Titolo
                            </th>
                            <th class="px-4 py-3 text-left font-medium">
                                Inizio
                            </th>
                            <th class="px-4 py-3 text-left font-medium">
                                Fine
                            </th>
                            <th class="px-4 py-3 text-center font-medium">
                                Capacita
                            </th>
                            <th class="px-4 py-3 text-center font-medium">
                                Posti disponibili
                            </th>
                            <th class="px-4 py-3 text-center font-medium">
                                Stato
                            </th>
                            <th class="px-4 py-3 text-right font-medium">
                                Azioni
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="workshop in workshops.data"
                            :key="workshop.id"
                            class="border-b border-sidebar-border/70 last:border-b-0 dark:border-sidebar-border"
                        >
                            <td class="px-4 py-3 font-medium">
                                {{ workshop.title }}
                            </td>
                            <td class="px-4 py-3">
                                {{ formatDate(workshop.starts_at) }}
                            </td>
                            <td class="px-4 py-3">
                                {{ formatDate(workshop.ends_at) }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                {{ workshop.capacity }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                {{ workshop.available_seats }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <Badge
                                    :variant="
                                        workshop.is_full
                                            ? 'destructive'
                                            : 'default'
                                    "
                                >
                                    {{
                                        workshop.is_full
                                            ? 'Completo'
                                            : 'Disponibile'
                                    }}
                                </Badge>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <Link :href="show.url(workshop.id)">
                                    <Button variant="outline" size="sm">
                                        Dettagli
                                    </Button>
                                </Link>
                            </td>
                        </tr>
                        <tr v-if="workshops.data.length === 0">
                            <td
                                colspan="7"
                                class="px-4 py-8 text-center text-muted-foreground"
                            >
                                Nessun workshop trovato.
                            </td>
                        </tr>
                    </tbody>
                </table>
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
                    <Button
                        v-else
                        variant="outline"
                        size="sm"
                        disabled
                    >
                        <span v-html="link.label" />
                    </Button>
                </template>
            </div>
        </div>
    </AppLayout>
</template>
