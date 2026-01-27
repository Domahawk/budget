<script setup lang="ts">
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/lib/api'

const router = useRouter()

/* ------------------------------------------
   State
------------------------------------------ */
const name = ref('')
const loading = ref(false)

const errors = reactive<{
    name?: string
}>({})

/* ------------------------------------------
   Submit
------------------------------------------ */
async function submit() {
    errors.name = undefined

    if (!name.value.trim()) {
        errors.name = 'Store name is required'
        return
    }

    loading.value = true

    try {
        const { data } = await api.post('/stores/create', {
            name: name.value,
        })

        // assuming API returns created store
        await router.push({name: 'store_list'})
    } catch (err: any) {
        if (err.response?.status === 422) {
            const apiErrors = err.response.data.errors ?? {}
            errors.name = apiErrors.name?.[0]
        }
    } finally {
        loading.value = false
    }
}
</script>

<template>
    <div class="mx-auto max-w-xl py-10">
        <h1 class="mb-6 text-2xl font-bold">
            Add Store
        </h1>

        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <label class="block font-medium">
                    Store name
                </label>

                <input
                    v-model="name"
                    class="w-full rounded border p-2 bg-black"
                    placeholder="e.g. Kaufland"
                />

                <p v-if="errors.name" class="text-sm text-red-600">
                    {{ errors.name }}
                </p>
            </div>

            <button
                type="submit"
                class="rounded bg-black px-4 py-2 text-white disabled:opacity-50"
                :disabled="loading || !name.trim()"
            >
                {{ loading ? 'Saving…' : 'Save store' }}
            </button>
        </form>
    </div>
</template>
