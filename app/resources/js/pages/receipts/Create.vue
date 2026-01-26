<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'

defineProps<{
    stores: {
        id: number
        name: string
    }[]
}>()

const form = useForm<{
    image: File | null
    store_id: number | null
}>({
    image: null,
    store_id: null,
})

function handleFileChange(e: Event) {
    const input = e.target as HTMLInputElement | null
    form.image = input && input.files && input.files[0] ? input.files[0] : null
}

function submit(): void {
    if (!form.image || !form.store_id) return

    form.post('/receipts', {
        forceFormData: true,
    })
}
</script>

<template>
    <div class="mx-auto max-w-2xl py-12">
        <h1 class="mb-4 text-3xl font-bold">Budget Receipt Scanner</h1>

        <p class="mb-8 text-gray-600">
            Upload a receipt image to start processing.
        </p>

        <div class="rounded-lg border p-6">
            <form @submit.prevent="submit" class="space-y-4">
                <!-- Store select -->
                <div>
                    <label class="mb-2 block font-medium">
                        Store
                    </label>

                    <select
                        v-model="form.store_id"
                        class="block w-full rounded border p-2 bg-black"
                    >
                        <option value="" disabled>Select store</option>
                        <option
                            v-for="store in stores"
                            :key="store.id"
                            :value="store.id"
                        >
                            {{ store.name }}
                        </option>
                    </select>

                    <div v-if="form.errors.store_id" class="text-sm text-red-600">
                        {{ form.errors.store_id }}
                    </div>
                </div>

                <!-- Image upload -->
                <div>
                    <label class="mb-2 block font-medium">
                        Upload receipt image
                    </label>

                    <input
                        type="file"
                        name="image"
                        accept="image/*"
                        class="mb-2 block w-full border-2 border-amber-600 rounded-2xl"
                        @change="handleFileChange"
                    />

                    <div v-if="form.errors.image" class="text-sm text-red-600">
                        {{ form.errors.image }}
                    </div>
                </div>
                <button
                    type="submit"
                    class="rounded bg-black px-4 py-2 text-white disabled:opacity-50"
                    :disabled="form.processing || !form.image || !form.store_id"
                >
                    Upload
                </button>
            </form>
        </div>
    </div>
</template>
