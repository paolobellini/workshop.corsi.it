<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { useDateFormat } from '@vueuse/core';
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

        <CardFooter>
            <Link :href="show.url(workshop.id)" class="w-full">
                <Button class="w-full">Dettagli</Button>
            </Link>
        </CardFooter>
    </Card>
</template>
