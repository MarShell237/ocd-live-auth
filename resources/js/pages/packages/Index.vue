<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import PackageController from '@/actions/App/Http/Controllers/PackageController';
import NameResourceManager from '@/components/NameResourceManager.vue';
import { index } from '@/routes/packages';

type PackageItem = {
    id: number;
    name: string;
    description: string;
    price: int;
    duration: int;
    offers: array;
    mode: array;
    reservations_count: number;
};

defineProps<{
    packages: PackageItem[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Packages',
                href: index(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Packages" />

    <div
        class="flex flex-1 flex-col gap-6 overflow-x-auto bg-[#F5F2EA] p-4 md:p-6 dark:bg-neutral-950"
    >
        <NameResourceManager
            title="Packages"
            description="Gérez les packages proposés avec les réservations."
            resource-label="package"
            :items="packages"
            :store-action="PackageController.store.form()"
            :update-action="PackageController.update.form"
            :destroy-action="PackageController.destroy.form"
        />
    </div>
</template>
