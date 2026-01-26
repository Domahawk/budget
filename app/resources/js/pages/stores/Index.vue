<script setup lang="ts">
import { router } from '@inertiajs/vue3';

defineProps<{
    stores: {
        id: number
        name: string
        receipts_count: number
    }[]
}>()

// In your component methods
function deleteRecord(id: number) {
    router.delete(`/stores/${id}`, {
        onBefore: () => confirm('Are you sure you want to delete this item?'),
        onSuccess: () => {
            // Optional: Handle success, e.g., show a toast or update state
        },
        onError: () => {
            // Optional: Handle error
        },
    });
}
</script>

<template>
    <div class="mx-auto max-w-2xl py-10">
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-bold">Stores</h1>

            <a
                href="/stores/create"
                class="rounded bg-black px-4 py-2 text-white"
            >
                Add store
            </a>
        </div>

        <div v-if="stores.length === 0" class="text-gray-600">
            No stores created yet.
        </div>

        <div
            v-for="store in stores"
            :key="store.id"
            class="flex items-center justify-between border-b py-3"
        >
            <div>
                <div class="font-medium">
                    {{ store.name }}
                </div>
                <button @click="deleteRecord(store.id)">
                    Delete
                </button>
            </div>

            <!-- Placeholder for future actions -->
            <div class="text-sm text-gray-400">
                Schema defined
            </div>
        </div>
    </div>
</template>
