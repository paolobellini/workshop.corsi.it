<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { watch } from 'vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { update } from '@/routes/workshops';
import type { Workshop } from '@/types';

const props = defineProps<{
    workshop: Workshop;
}>();

const open = defineModel<boolean>('open', { default: false });

function toDateTimeLocal(value: string): string {
    return value ? value.replace(' ', 'T').slice(0, 16) : '';
}

function toDateTimeString(value: string): string {
    return value ? value.replace('T', ' ') + ':00' : '';
}

const form = useForm({
    title: props.workshop.title,
    description: props.workshop.description ?? '',
    starts_at: toDateTimeLocal(props.workshop.starts_at),
    ends_at: toDateTimeLocal(props.workshop.ends_at),
    capacity: String(props.workshop.capacity),
});

watch(open, (isOpen) => {
    if (isOpen) {
        form.title = props.workshop.title;
        form.description = props.workshop.description ?? '';
        form.starts_at = toDateTimeLocal(props.workshop.starts_at);
        form.ends_at = toDateTimeLocal(props.workshop.ends_at);
        form.capacity = String(props.workshop.capacity);
        form.clearErrors();
    }
});

function submit() {
    form.transform((data) => ({
        ...data,
        starts_at: toDateTimeString(data.starts_at),
        ends_at: toDateTimeString(data.ends_at),
    })).put(update.url(props.workshop.id), {
        onSuccess: () => {
            open.value = false;
        },
    });
}
</script>

<template>
    <Dialog v-model:open="open">
        <DialogContent class="sm:max-w-lg">
            <DialogHeader>
                <DialogTitle>Modifica Workshop</DialogTitle>
                <DialogDescription>
                    Modifica i dettagli del workshop.
                </DialogDescription>
            </DialogHeader>

            <form class="space-y-4" @submit.prevent="submit">
                <div class="grid gap-2">
                    <Label for="edit-title">Titolo</Label>
                    <Input
                        id="edit-title"
                        v-model="form.title"
                        required
                        placeholder="Nome del workshop"
                    />
                    <InputError :message="form.errors.title" />
                </div>

                <div class="grid gap-2">
                    <Label for="edit-description">Descrizione</Label>
                    <textarea
                        id="edit-description"
                        v-model="form.description"
                        rows="3"
                        class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-hidden"
                        placeholder="Descrizione del workshop (opzionale)"
                    />
                    <InputError :message="form.errors.description" />
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="edit-starts_at">Inizio</Label>
                        <Input
                            id="edit-starts_at"
                            v-model="form.starts_at"
                            type="datetime-local"
                            required
                        />
                        <InputError :message="form.errors.starts_at" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="edit-ends_at">Fine</Label>
                        <Input
                            id="edit-ends_at"
                            v-model="form.ends_at"
                            type="datetime-local"
                            required
                        />
                        <InputError :message="form.errors.ends_at" />
                    </div>
                </div>

                <div class="grid gap-2">
                    <Label for="edit-capacity">Capacita</Label>
                    <Input
                        id="edit-capacity"
                        v-model="form.capacity"
                        type="number"
                        min="1"
                        max="500"
                        required
                        placeholder="Numero massimo di partecipanti"
                    />
                    <InputError :message="form.errors.capacity" />
                </div>

                <DialogFooter>
                    <Button
                        type="button"
                        variant="outline"
                        @click="open = false"
                    >
                        Annulla
                    </Button>
                    <Button type="submit" :disabled="form.processing">
                        {{ form.processing ? 'Salvataggio...' : 'Salva' }}
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
