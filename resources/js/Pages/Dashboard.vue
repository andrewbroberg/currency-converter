<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/inertia-vue3';
import LiveConversion from "@/Components/LiveConversion.vue";
import ConversionReport from "@/Components/ConversionReport.vue";
</script>

<template>
    <Head title="Currency Converter"/>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Currency Converter</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="px-4 sm:px-6 lg:px-8">
                    <LiveConversion class="mb-8"></LiveConversion>
                    <ConversionReport></ConversionReport>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
