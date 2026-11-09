<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import ModeController from '@/actions/App/Http/Controllers/ModeController';
import NameResourceManager from '@/components/NameResourceManager.vue';
import { index } from '@/routes/modes';

type ModeItem = {
    id: number;
    name: string;
    reservations_count: number;
};

defineProps<{
    modes: ModeItem[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Modes',
                href: index(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Modes" />

    <div
        class="flex flex-1 flex-col gap-6 overflow-x-auto bg-[#F5F2EA] p-4 md:p-6 dark:bg-neutral-950"
    >
        <NameResourceManager
            title="Modes"
            description="Gérez les modes disponibles pour les réservations."
            resource-label="mode"
            :items="modes"
            :store-action="ModeController.store.form()"
            :update-action="ModeController.update.form"
            :destroy-action="ModeController.destroy.form"
        />
    </div>
</template>
