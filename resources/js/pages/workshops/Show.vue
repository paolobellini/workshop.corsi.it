<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';
import { Badge } from '@/components/ui/badge';
import AppLayout from '@/layouts/AppLayout.vue';
import { index, show } from '@/routes/workshops';
import type { BreadcrumbItem, Workshop } from '@/types';

type Props = {
    workshop: { data: Workshop };
};

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Workshops',
        href: index(),
    },
    {
        title: props.workshop.data.title,
        href: show(props.workshop.data.id),
    },
];

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
    <Head :title="workshop.data.title" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4">
            <Link
                :href="index()"
                class="flex items-center gap-1 text-sm text-muted-foreground transition-colors hover:text-foreground"
            >
                <ArrowLeft class="size-4" />
                Torna ai workshop
            </Link>

            <h1 class="text-xl font-semibold tracking-tight">
                {{ workshop.data.title }}
            </h1>

            <div
                class="rounded-xl border border-sidebar-border/70 p-6 dark:border-sidebar-border"
            >
                <div class="mb-4 flex items-start justify-between">
                    <h2 class="text-lg font-semibold tracking-tight">
                        {{ workshop.data.title }}
                    </h2>
                    <Badge
                        :variant="
                            workshop.data.is_full ? 'destructive' : 'default'
                        "
                    >
                        {{ workshop.data.is_full ? 'Completo' : 'Disponibile' }}
                    </Badge>
                </div>

                <p
                    v-if="workshop.data.description"
                    class="mb-6 text-sm text-muted-foreground"
                >
                    {{ workshop.data.description }}
                </p>

                <dl class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <dt class="text-sm text-muted-foreground">Inizio</dt>
                        <dd class="font-medium">
                            {{ formatDate(workshop.data.starts_at) }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm text-muted-foreground">Fine</dt>
                        <dd class="font-medium">
                            {{ formatDate(workshop.data.ends_at) }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm text-muted-foreground">Capacita</dt>
                        <dd class="font-medium">
                            {{ workshop.data.capacity }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm text-muted-foreground">
                            Posti disponibili
                        </dt>
                        <dd class="font-medium">
                            {{ workshop.data.available_seats }}
                        </dd>
                    </div>
                </dl>
            </div>

            <div
                v-if="workshop.data.registrations"
                class="rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
            >
                <div
                    class="border-b border-sidebar-border/70 px-6 py-4 dark:border-sidebar-border"
                >
                    <h3 class="font-medium">
                        Partecipanti ({{ workshop.data.registrations.length }})
                    </h3>
                </div>

                <table class="w-full text-sm">
                    <thead>
                        <tr
                            class="border-b border-sidebar-border/70 dark:border-sidebar-border"
                        >
                            <th class="px-6 py-3 text-left font-medium">
                                Nome
                            </th>
                            <th class="px-6 py-3 text-left font-medium">
                                Email
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="registration in workshop.data.registrations"
                            :key="registration.id"
                            class="border-b border-sidebar-border/70 last:border-b-0 dark:border-sidebar-border"
                        >
                            <td class="px-6 py-3">{{ registration.name }}</td>
                            <td class="px-6 py-3">{{ registration.email }}</td>
                        </tr>
                        <tr v-if="workshop.data.registrations.length === 0">
                            <td
                                colspan="2"
                                class="px-6 py-8 text-center text-muted-foreground"
                            >
                                Nessun partecipante registrato.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>
