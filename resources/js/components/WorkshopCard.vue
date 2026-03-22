<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { useDateFormat } from '@vueuse/core';
import { Clock, Pencil, Trash2, UserPlus } from 'lucide-vue-next';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { show } from '@/routes/workshops';
import type { Workshop } from '@/types';

const props = defineProps<{
    workshop: Workshop;
}>();

const startsAt = useDateFormat(props.workshop.starts_at, 'DD/MM/YYYY HH:mm');
const endsAt = useDateFormat(props.workshop.ends_at, 'DD/MM/YYYY HH:mm');
</script>

<template>
    <Card
        class="flex flex-col justify-between shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg"
    >
        <CardHeader>
            <div class="flex items-start justify-between gap-2">
                <CardTitle class="text-lg">
                    {{ workshop.title }}
                </CardTitle>
                <Badge
                    :variant="workshop.is_full ? 'destructive' : 'default'"
                    class="shrink-0"
                >
                    {{ workshop.is_full ? 'Completo' : 'Disponibile' }}
                </Badge>
            </div>
            <CardDescription v-if="workshop.description">
                {{ workshop.description }}
            </CardDescription>
        </CardHeader>

        <CardContent>
            <dl class="grid grid-cols-2 gap-3 text-sm">
                <div>
                    <dt class="text-muted-foreground">Inizio</dt>
                    <dd class="font-medium">
                        {{ startsAt }}
                    </dd>
                </div>
                <div>
                    <dt class="text-muted-foreground">Fine</dt>
                    <dd class="font-medium">
                        {{ endsAt }}
                    </dd>
                </div>
                <div>
                    <dt class="text-muted-foreground">Capacita</dt>
                    <dd class="font-medium">{{ workshop.capacity }}</dd>
                </div>
                <div>
                    <dt class="text-muted-foreground">Posti disponibili</dt>
                    <dd class="font-medium">{{ workshop.available_seats }}</dd>
                </div>
            </dl>
        </CardContent>

        <CardFooter class="flex-col gap-2">
            <div class="flex w-full gap-2">
                <Link :href="show.url(workshop.id)" class="flex-1">
                    <Button class="w-full">Dettagli</Button>
                </Link>
                <Button variant="outline" size="icon">
                    <span class="sr-only">Modifica</span>
                    <Pencil class="size-4" />
                </Button>
                <Button variant="outline" size="icon">
                    <span class="sr-only">Elimina</span>
                    <Trash2 class="size-4" />
                </Button>
            </div>
            <Button
                class="w-full gap-2 bg-emerald-600 text-white hover:bg-emerald-700"
            >
                <UserPlus class="size-4" />
                Iscriviti
            </Button>
            <Button variant="outline" class="w-full gap-2">
                <Clock class="size-4" />
                Aggiungi alla coda
            </Button>
        </CardFooter>
    </Card>
</template>
