<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'

defineProps<{
    columnTypes: string[]
}>()

const form = useForm({
    name: '',
    columns: ['qty', 'name', 'unit_price', 'total_price'],
})

function submit() {
    form.post('/stores')
}
</script>

<template>
    <div class="mx-auto max-w-xl py-10">
        <h1 class="mb-6 text-2xl font-bold">Add Store</h1>

        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <label class="block font-medium">Store name</label>
                <input
                    v-model="form.name"
                    class="w-full rounded border p-2"
                />
                <div v-if="form.errors.name" class="text-sm text-red-600">
                    {{ form.errors.name }}
                </div>
            </div>

            <div>
                <label class="block font-medium mb-2">Column order</label>

                <div
                    v-for="(col, index) in form.columns"
                    :key="index"
                    class="mb-2 flex items-center gap-2"
                >
                    <span class="w-6 text-sm">{{ index + 1 }}.</span>

                    <select
                        v-model="form.columns[index]"
                        class="flex-1 rounded border p-2"
                    >
                        <option
                            v-for="type in columnTypes"
                            :key="type"
                            :value="type"
                        >
                            {{ type }}
                        </option>
                    </select>
                </div>

                <div v-if="form.errors.columns" class="text-sm text-red-600">
                    {{ form.errors.columns }}
                </div>
            </div>

            <button
                type="submit"
                class="rounded bg-black px-4 py-2 text-white"
                :disabled="form.processing"
            >
                Save store
            </button>
        </form>
    </div>
</template>
