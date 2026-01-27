<script setup lang="ts">
import { RouterLink } from 'vue-router'
</script>

<template>
    <div class="min-h-screen flex flex-col">
        <!-- Navbar -->
        <header class="border-b bg-white dark:bg-gray-900">
            <nav class="mx-auto max-w-7xl px-6 py-4 flex gap-6">
                <RouterLink
                    :to="{name: 'home'}"
                    class="font-medium"
                    active-class="text-blue-600"
                >
                    Home
                </RouterLink>
                <RouterLink
                    :to="{name: 'receipt_list'}"
                    class="font-medium"
                    active-class="text-blue-600"
                >
                    Receipts
                </RouterLink>
                <RouterLink
                    :to="{name: 'receipt_create'}"
                    class="font-medium"
                    active-class="text-blue-600"
                >
                    Create a Receipt
                </RouterLink>
                <RouterLink
                    :to="{name: 'store_list'}"
                    class="font-medium"
                    active-class="text-blue-600"
                >
                    Stores
                </RouterLink>
                <RouterLink
                    :to="{name: 'store_create'}"
                    class="font-medium"
                    active-class="text-blue-600"
                >
                    Create a Store
                </RouterLink>
            </nav>
        </header>

        <!-- Page content -->
        <main class="flex-1">
            <slot />
        </main>
    </div>
</template>
