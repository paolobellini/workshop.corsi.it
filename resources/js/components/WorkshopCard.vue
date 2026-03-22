<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import { useDateFormat } from '@vueuse/core';
import { Clock, Pencil, Trash2, UserMinus, UserPlus, X } from 'lucide-vue-next';
import { ref } from 'vue';
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
import ConfirmDialog from '@/components/ConfirmDialog.vue';
import EditWorkshopModal from '@/components/EditWorkshopModal.vue';
import { useRole } from '@/composables/useRole';
import { destroy, register, show, unregister } from '@/routes/workshops';
import { store as waitingListStore, destroy as waitingListDestroy } from '@/routes/workshops/waiting-list';
import type { Workshop } from '@/types';

const props = defineProps<{
    workshop: Workshop;
}>();

const { isAdmin, isEmployee } = useRole();

const startsAt = useDateFormat(props.workshop.starts_at, 'DD/MM/YYYY HH:mm');
const endsAt = useDateFormat(props.workshop.ends_at, 'DD/MM/YYYY HH:mm');

const showEditModal = ref(false);
const showDeleteDialog = ref(false);
const deleting = ref(false);
const registering = ref(false);
const unregistering = ref(false);
const joiningWaitingList = ref(false);
const leavingWaitingList = ref(false);

function subscribe() {
    registering.value = true;
    router.post(register.url(props.workshop.id), {}, {
        onFinish: () => {
            registering.value = false;
        },
    });
}

function cancelSubscription() {
    unregistering.value = true;
    router.delete(unregister.url(props.workshop.id), {
        onFinish: () => {
            unregistering.value = false;
        },
    });
}

function joinWaitingList() {
    joiningWaitingList.value = true;
    router.post(waitingListStore.url(props.workshop.id), {}, {
        onFinish: () => {
            joiningWaitingList.value = false;
        },
    });
}

function leaveWaitingList() {
    leavingWaitingList.value = true;
    router.delete(waitingListDestroy.url(props.workshop.id), {
        onFinish: () => {
            leavingWaitingList.value = false;
        },
    });
}

function confirmDelete() {
    deleting.value = true;
    router.delete(destroy.url(props.workshop.id), {
        onFinish: () => {
            deleting.value = false;
            showDeleteDialog.value = false;
        },
    });
}
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
            <div v-if="isAdmin" class="flex w-full gap-2">
                <Link :href="show.url(workshop.id)" class="flex-1">
                    <Button class="w-full">Dettagli</Button>
                </Link>
                <Button
                    variant="outline"
                    size="icon"
                    @click="showEditModal = true"
                >
                    <span class="sr-only">Modifica</span>
                    <Pencil class="size-4" />
                </Button>
                <Button
                    variant="outline"
                    size="icon"
                    @click="showDeleteDialog = true"
                >
                    <span class="sr-only">Elimina</span>
                    <Trash2 class="size-4" />
                </Button>
            </div>
            <Button
                v-if="isEmployee && !workshop.is_full && !workshop.is_registered"
                class="w-full gap-2 bg-emerald-600 text-white hover:bg-emerald-700"
                :disabled="registering"
                @click="subscribe"
            >
                <UserPlus class="size-4" />
                {{ registering ? 'Iscrizione...' : 'Iscriviti' }}
            </Button>
            <Button
                v-if="isEmployee && workshop.is_registered"
                variant="destructive"
                class="w-full gap-2"
                :disabled="unregistering"
                @click="cancelSubscription"
            >
                <UserMinus class="size-4" />
                {{ unregistering ? 'Annullamento...' : 'Annulla iscrizione' }}
            </Button>
            <Button
                v-if="isEmployee && workshop.is_full && !workshop.is_registered && !workshop.is_on_waiting_list"
                variant="outline"
                class="w-full gap-2"
                :disabled="joiningWaitingList"
                @click="joinWaitingList"
            >
                <Clock class="size-4" />
                {{ joiningWaitingList ? 'Aggiunta...' : 'Aggiungi alla coda' }}
            </Button>
            <Button
                v-if="isEmployee && workshop.is_on_waiting_list"
                variant="destructive"
                class="w-full gap-2"
                :disabled="leavingWaitingList"
                @click="leaveWaitingList"
            >
                <X class="size-4" />
                {{ leavingWaitingList ? 'Rimozione...' : 'Esci dalla coda' }}
            </Button>
        </CardFooter>
    </Card>

    <EditWorkshopModal
        v-model:open="showEditModal"
        :workshop="workshop"
    />

    <ConfirmDialog
        v-model:open="showDeleteDialog"
        title="Elimina Workshop"
        :description="`Sei sicuro di voler eliminare &quot;${workshop.title}&quot;? Questa azione non può essere annullata.`"
        confirm-label="Elimina"
        :processing="deleting"
        @confirm="confirmDelete"
    />
</template>
