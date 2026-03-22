<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
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
import { store } from '@/routes/workshops';

const open = defineModel<boolean>('open', { default: false });

const form = useForm({
    title: '',
    description: '',
    starts_at: '',
    ends_at: '',
    capacity: '',
});

function toDateTimeString(value: string): string {
    return value ? value.replace('T', ' ') + ':00' : '';
}

function submit() {
    form.transform((data) => ({
        ...data,
        starts_at: toDateTimeString(data.starts_at),
        ends_at: toDateTimeString(data.ends_at),
    })).post(store.url(), {
        onSuccess: () => {
            form.reset();
            open.value = false;
        },
    });
}
</script>

<template>
    <Dialog v-model:open="open">
        <DialogContent class="sm:max-w-lg">
            <DialogHeader>
                <DialogTitle>Nuovo Workshop</DialogTitle>
                <DialogDescription>
                    Compila i campi per creare un nuovo workshop.
                </DialogDescription>
            </DialogHeader>

            <form class="space-y-4" @submit.prevent="submit">
                <div class="grid gap-2">
                    <Label for="title">Titolo</Label>
                    <Input
                        id="title"
                        v-model="form.title"
                        required
                        placeholder="Nome del workshop"
                    />
                    <InputError :message="form.errors.title" />
                </div>

                <div class="grid gap-2">
                    <Label for="description">Descrizione</Label>
                    <textarea
                        id="description"
                        v-model="form.description"
                        rows="3"
                        class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-hidden"
                        placeholder="Descrizione del workshop (opzionale)"
                    />
                    <InputError :message="form.errors.description" />
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="starts_at">Inizio</Label>
                        <Input
                            id="starts_at"
                            v-model="form.starts_at"
                            type="datetime-local"
                            required
                        />
                        <InputError :message="form.errors.starts_at" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="ends_at">Fine</Label>
                        <Input
                            id="ends_at"
                            v-model="form.ends_at"
                            type="datetime-local"
                            required
                        />
                        <InputError :message="form.errors.ends_at" />
                    </div>
                </div>

                <div class="grid gap-2">
                    <Label for="capacity">Capacita</Label>
                    <Input
                        id="capacity"
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
                        {{ form.processing ? 'Creazione...' : 'Crea Workshop' }}
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
