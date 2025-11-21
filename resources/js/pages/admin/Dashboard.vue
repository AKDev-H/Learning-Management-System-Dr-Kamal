<script setup lang="ts">
import AdminLayout from '@/layouts/admin/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Admin Dashboard" />

    <AdminLayout
        :breadcrumbs="[
            { title: 'Dashboard' },
        ]"
    >
        <div class="space-y-6 p-6">
            <div>
                <h1 class="text-3xl font-bold tracking-tight">Admin Dashboard</h1>
                <p class="text-muted-foreground">
                    Welcome to the admin dashboard
                </p>
            </div>
        </div>
    </AdminLayout>
</template>

