<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import { Pencil, Plus, Trash2 } from '@lucide/vue';
import { ref } from 'vue';
import InputError from '@/components/InputError.vue';
import { Badge } from '@/components/ui/badge';
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
import type { RouteFormDefinition } from '@/wayfinder';

type ResourceItem = {
    id: number;
    name: string;
    reservations_count: number;
};

type Props = {
    title: string;
    description: string;
    resourceLabel: string;
    items: ResourceItem[];
    storeAction: RouteFormDefinition<'post'>;
    updateAction: (id: number) => RouteFormDefinition<'post'>;
    destroyAction: (id: number) => RouteFormDefinition<'post'>;
};

defineProps<Props>();

const editingId = ref<number | null>(null);
const itemToDelete = ref<ResourceItem | null>(null);

const startEditing = (id: number): void => {
    editingId.value = id;
};

const stopEditing = (): void => {
    editingId.value = null;
};

const confirmDeletion = (item: ResourceItem): void => {
    itemToDelete.value = item;
};

const closeDeletion = (): void => {
    itemToDelete.value = null;
};

const isUsed = (item: ResourceItem): boolean => item.reservations_count > 0;
</script>

<template>
    <div class="flex flex-col gap-6">
        <div>
            <h1
                class="text-2xl font-bold tracking-tight text-neutral-900 dark:text-neutral-50"
            >
                {{ title }}
            </h1>
            <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">
                {{ description }}
            </p>
        </div>

        <section
            class="rounded-xl border border-[#E7E2D8] bg-white shadow-sm dark:border-neutral-800 dark:bg-neutral-900"
        >
            <div
                class="border-b border-[#EDE9E1] px-6 py-5 dark:border-neutral-800"
            >
                <Form
                    v-bind="storeAction"
                    :options="{ preserveScroll: true }"
                    reset-on-success
                    class="flex flex-col gap-3 sm:flex-row sm:items-start"
                    v-slot="{ errors, processing }"
                >
                    <div class="flex-1">
                        <Input
                            name="name"
                            :placeholder="`Nom du ${resourceLabel}`"
                            :aria-label="`Nom du ${resourceLabel}`"
                            required
                        />
                        <InputError class="mt-2" :message="errors.name" />
                    </div>

                    <Button
                        type="submit"
                        :disabled="processing"
                        class="h-9 rounded-full bg-[#0B7A55] px-4 text-white hover:bg-[#096344] dark:bg-emerald-600 dark:hover:bg-emerald-500"
                    >
                        <Plus class="size-4" />
                        Ajouter
                    </Button>
                </Form>
            </div>

            <div v-if="items.length > 0" class="overflow-x-auto">
                <table class="w-full min-w-[640px] text-left text-sm">
                    <thead>
                        <tr
                            class="border-b border-[#EDE9E1] dark:border-neutral-800"
                        >
                            <th
                                class="px-6 py-3 text-[11px] font-semibold tracking-wider text-neutral-400 uppercase"
                            >
                                Nom
                            </th>
                            <th
                                class="px-6 py-3 text-[11px] font-semibold tracking-wider text-neutral-400 uppercase"
                            >
                                Réservations
                            </th>
                            <th class="px-6 py-3">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="item in items"
                            :key="item.id"
                            class="border-b border-[#EDE9E1] last:border-b-0 dark:border-neutral-800"
                        >
                            <td class="px-6 py-4">
                                <Form
                                    v-if="editingId === item.id"
                                    v-bind="updateAction(item.id)"
                                    :options="{ preserveScroll: true }"
                                    class="flex flex-col gap-2 sm:flex-row sm:items-start"
                                    @success="stopEditing"
                                    v-slot="{ errors, processing }"
                                >
                                    <div class="flex-1">
                                        <Input
                                            name="name"
                                            :default-value="item.name"
                                            :aria-label="`Nom du ${resourceLabel}`"
                                            required
                                        />
                                        <InputError
                                            class="mt-2"
                                            :message="errors.name"
                                        />
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <Button
                                            type="submit"
                                            size="sm"
                                            :disabled="processing"
                                            class="rounded-full bg-[#0B7A55] text-white hover:bg-[#096344] dark:bg-emerald-600 dark:hover:bg-emerald-500"
                                        >
                                            Enregistrer
                                        </Button>
                                        <Button
                                            type="button"
                                            variant="ghost"
                                            size="sm"
                                            class="rounded-full"
                                            @click="stopEditing"
                                        >
                                            Annuler
                                        </Button>
                                    </div>
                                </Form>

                                <span
                                    v-else
                                    class="font-medium whitespace-nowrap text-neutral-900 dark:text-neutral-100"
                                >
                                    {{ item.name }}
                                </span>
                            </td>

                            <td class="px-6 py-4">
                                <Badge variant="secondary" class="font-normal">
                                    {{ item.reservations_count }}
                                    réservation{{
                                        item.reservations_count > 1 ? 's' : ''
                                    }}
                                </Badge>
                            </td>

                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <template v-if="editingId !== item.id">
                                    <Button
                                        type="button"
                                        variant="outline"
                                        size="sm"
                                        class="rounded-full"
                                        @click="startEditing(item.id)"
                                    >
                                        <Pencil class="size-3.5" />
                                        Modifier
                                    </Button>
                                    <Button
                                        type="button"
                                        variant="ghost"
                                        size="sm"
                                        class="rounded-full text-red-600 hover:bg-red-50 hover:text-red-700 dark:text-red-400 dark:hover:bg-red-500/10"
                                        :disabled="isUsed(item)"
                                        :title="
                                            isUsed(item)
                                                ? `Ce ${resourceLabel} est utilisé par des réservations.`
                                                : undefined
                                        "
                                        @click="confirmDeletion(item)"
                                    >
                                        <Trash2 class="size-3.5" />
                                        Supprimer
                                    </Button>
                                </template>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <p
                v-else
                class="px-6 py-10 text-center text-sm text-neutral-500 dark:text-neutral-400"
            >
                Aucun {{ resourceLabel }} pour le moment.
            </p>
        </section>

        <Dialog
            :open="itemToDelete !== null"
            @update:open="(open) => !open && closeDeletion()"
        >
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle>Supprimer ce {{ resourceLabel }} ?</DialogTitle>
                    <DialogDescription>
                        « {{ itemToDelete?.name }} » sera définitivement
                        supprimé.
                    </DialogDescription>
                </DialogHeader>

                <DialogFooter>
                    <Button
                        type="button"
                        variant="outline"
                        @click="closeDeletion"
                    >
                        Annuler
                    </Button>

                    <Form
                        v-if="itemToDelete"
                        v-bind="destroyAction(itemToDelete.id)"
                        :options="{ preserveScroll: true }"
                        @success="closeDeletion"
                        v-slot="{ processing }"
                    >
                        <Button
                            type="submit"
                            variant="destructive"
                            :disabled="processing"
                        >
                            Supprimer
                        </Button>
                    </Form>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>
