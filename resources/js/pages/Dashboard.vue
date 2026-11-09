<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
import { Plus } from '@lucide/vue';
import { computed, ref } from 'vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { dashboard } from '@/routes';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Tableau de bord',
                href: dashboard(),
            },
        ],
    },
});

type ReservationStatus = 'pending' | 'confirmed' | 'cancelled';

type Stat = {
    label: string;
    value: number;
};

type Reservation = {
    id: number;
    reference: string;
    nom: string;
    business: string;
    mode: string;
    package: string;
    creneau: string;
    status: ReservationStatus;
};

type DashboardProps = {
    stats: {
        toValidate: number;
        confirmed: number;
        today: number;
        freeSlots: number;
    };
    reservations: Reservation[];
};

const props = defineProps<DashboardProps>();

const statCards = computed<Stat[]>(() => [
    { label: 'À valider', value: props.stats.toValidate },
    { label: 'Confirmées à venir', value: props.stats.confirmed },
    { label: "Aujourd'hui", value: props.stats.today }
    //{ label: 'Créneaux libres', value: props.stats.freeSlots },
]);

const dateFormatter = new Intl.DateTimeFormat('fr-FR', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
});

const timeFormatter = new Intl.DateTimeFormat('fr-FR', {
    hour: '2-digit',
    minute: '2-digit',
});

function formatCreneau(creneau: string): string {
    const date = new Date(creneau);

    return `${dateFormatter.format(date)} · ${timeFormatter.format(date)}`;
}

const statusStyles: Record<
    ReservationStatus,
    { label: string; class: string }
> = {
    pending: {
        label: 'En attente',
        class: 'border-transparent bg-amber-100 text-amber-800 dark:bg-amber-400/15 dark:text-amber-300',
    },
    confirmed: {
        label: 'Confirmée',
        class: 'border-transparent bg-emerald-100 text-emerald-800 dark:bg-emerald-400/15 dark:text-emerald-300',
    },
    cancelled: {
        label: 'Annulée',
        class: 'border-transparent bg-red-100 text-red-700 dark:bg-red-400/15 dark:text-red-300',
    },
};

const search = ref('');
const statusFilter = ref<'all' | ReservationStatus>('all');

const filteredReservations = computed(() => {
    const term = search.value.trim().toLowerCase();

    return props.reservations.filter((reservation) => {
        const matchesStatus =
            statusFilter.value === 'all' ||
            reservation.status === statusFilter.value;
        const matchesSearch =
            term === '' ||
            `${reservation.reference} ${reservation.nom} ${reservation.business}`
                .toLowerCase()
                .includes(term);

        return matchesStatus && matchesSearch;
    });
});
const confirmReservation = (id) => {
    router.patch(`/reservations/${id}/confirm`, {}, {
        preserveScroll: true, // Évite de remonter en haut de la page après l'action
        onSuccess: () => {
            // Optionnel : actions après le succès (notification, etc.)
        }
    });
};
const cancel = (id) => {
    router.patch(`/reservations/${id}/cancel`, {}, {
        preserveScroll: true, // Évite de remonter en haut de la page après l'action
        onSuccess: () => {
            // Optionnel : actions après le succès (notification, etc.)
        }
    });
};
</script>

<template>
    <Head title="Tableau de bord" />

    <div
        class="flex flex-1 flex-col gap-6 overflow-x-auto bg-[#F5F2EA] p-4 md:p-6 dark:bg-neutral-950"
    >
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h1
                class="text-2xl font-bold tracking-tight text-neutral-900 dark:text-neutral-50"
            >
                Tableau de bord
            </h1>

            <!-- <Button -->
                <!-- type="button" -->
                <!-- class="h-10 rounded-full bg-[#0B7A55] px-5 font-semibold text-white hover:bg-[#096344] dark:bg-emerald-600 dark:hover:bg-emerald-500" -->
            <!-- > -->
                <!-- <Plus class="size-4" /> -->
                <!-- Ajouter des créneaux -->
            <!-- </Button> -->
        </div>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div
                v-for="stat in statCards"
                :key="stat.label"
                class="rounded-xl border border-[#E7E2D8] bg-white p-5 shadow-sm dark:border-neutral-800 dark:bg-neutral-900"
            >
                <p class="text-sm text-neutral-500 dark:text-neutral-400">
                    {{ stat.label }}
                </p>
                <p
                    class="mt-3 text-3xl font-bold text-neutral-900 dark:text-neutral-50"
                >
                    {{ stat.value }}
                </p>
            </div>
        </div>

        <section
            class="rounded-xl border border-[#E7E2D8] bg-white shadow-sm dark:border-neutral-800 dark:bg-neutral-900"
        >
            <header
                class="flex flex-wrap items-center justify-between gap-4 px-6 pt-6 pb-4"
            >
                <h2
                    class="text-xl font-bold text-neutral-900 dark:text-neutral-50"
                >
                    Réservations récentes
                </h2>

                <div
                    class="flex flex-1 flex-wrap items-center justify-end gap-3"
                >
                    <Input
                        v-model="search"
                        type="search"
                        placeholder="Rechercher un nom, un business"
                        aria-label="Rechercher une réservation"
                        class="h-9 w-full rounded-lg border-[#DDD8CE] bg-[#FCFBF8] sm:w-56 dark:border-neutral-700 dark:bg-neutral-900"
                    />

                    <Select v-model="statusFilter">
                        <SelectTrigger
                            aria-label="Filtrer par statut"
                            class="h-9 w-full rounded-lg border-[#DDD8CE] bg-white sm:w-44 dark:border-neutral-700 dark:bg-neutral-900"
                        >
                            <SelectValue placeholder="Tous les statuts" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">Tous les statuts</SelectItem>
                            <SelectItem value="pending">En attente</SelectItem>
                            <SelectItem value="confirmed">Confirmées</SelectItem>
                            <SelectItem value="cancelled">Annulées</SelectItem>
                        </SelectContent>
                    </Select>
                </div>
            </header>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[900px] text-left text-sm">
                    <thead>
                        <tr
                            class="border-y border-[#EDE9E1] dark:border-neutral-800"
                        >
                            <th
                                class="px-6 py-3 text-[11px] font-semibold tracking-wider text-neutral-400 uppercase"
                            >
                                Référence
                            </th>
                            <th
                                class="px-6 py-3 text-[11px] font-semibold tracking-wider text-neutral-400 uppercase"
                            >
                                Entrepreneur
                            </th>
                            <th
                                class="px-6 py-3 text-[11px] font-semibold tracking-wider text-neutral-400 uppercase"
                            >
                                Business
                            </th>
                            <th
                                class="px-6 py-3 text-[11px] font-semibold tracking-wider text-neutral-400 uppercase"
                            >
                                Mode
                            </th>
                            <th
                                class="px-6 py-3 text-[11px] font-semibold tracking-wider text-neutral-400 uppercase"
                            >
                                Package
                            </th>
                            <th
                                class="px-6 py-3 text-[11px] font-semibold tracking-wider text-neutral-400 uppercase"
                            >
                                Créneau
                            </th>
                            <th
                                class="px-6 py-3 text-[11px] font-semibold tracking-wider text-neutral-400 uppercase"
                            >
                                Statut
                            </th>
                            <th class="px-6 py-3">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="reservation in filteredReservations"
                            :key="reservation.id"
                            class="border-b border-[#EDE9E1] last:border-b-0 dark:border-neutral-800"
                        >
                            <td
                                class="px-6 py-4 font-medium whitespace-nowrap text-neutral-900 dark:text-neutral-100"
                            >
                                {{ reservation.reference }}
                            </td>
                            <td
                                class="px-6 py-4 text-neutral-600 dark:text-neutral-300"
                            >
                                {{ reservation.nom }}
                            </td>
                            <td
                                class="px-6 py-4 text-neutral-600 dark:text-neutral-300"
                            >
                                {{ reservation.business }}
                            </td>
                            <td
                                class="px-6 py-4 text-neutral-600 dark:text-neutral-300"
                            >
                                {{ reservation.mode}}
                            </td>
                            <td
                                class="px-6 py-4 text-neutral-600 dark:text-neutral-300"
                            >
                                {{ reservation.package }}
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-neutral-600 dark:text-neutral-300"
                            >
                                {{ formatCreneau(reservation.creneau) }}
                            </td>
                            <td class="px-6 py-4">
                                <Badge
                                    variant="outline"
                                    :class="
                                        statusStyles[reservation.status].class
                                    "
                                >
                                    {{ statusStyles[reservation.status].label }}
                                </Badge>
                            </td>
                            <td class="px-6 py-4 text-right">
                            <div class="flex gap-2" v-if="reservation.status === 'pending'">
                                <Button
                                    type="button"
                                    class="h-8 rounded-full bg-[#0B7A55] px-4 text-white hover:bg-[#096344] dark:bg-emerald-600 dark:hover:bg-emerald-500"
                                    @click="confirmReservation(reservation.id)"
                                >
                                    Confirmer
                                </Button>
                                <Button
                                    type="button"
                                    class="h-8 rounded-full bg-red-600 px-4 text-white hover:bg-red-500 dark:bg-red-600 dark:hover:bg-red-500"
                                    @click="cancel(reservation.id)"
                                >
                                    annuler
                                </Button>
                            </div>
                                <Button
                                    v-else
                                    type="button"
                                    variant="outline"
                                    class="h-8 rounded-full px-4"
                                >
                                    Voir
                                </Button>
                            </td>
                        </tr>
                        <tr v-if="filteredReservations.length === 0">
                            <td
                                colspan="6"
                                class="px-6 py-10 text-center text-neutral-500 dark:text-neutral-400"
                            >
                                Aucune réservation trouvée.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</template>
