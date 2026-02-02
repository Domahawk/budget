<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import api from '@/lib/api';
import type { Store } from '@/types/store';
import { useToastStore } from '@/stores/useToastStore';

const router = useRouter();
const stores = ref<Store[]>([]);
const image = ref<File | null>(null);
const storeId = ref<number | null>(null);
const loading = ref(false);
const errors = reactive<{
	image?: string;
	store_id?: string;
}>({});
const toastStore = useToastStore();

function handleFileChange(e: Event) {
	const input = e.target as HTMLInputElement;
	image.value = input.files?.[0] ?? null;
}

const fetchStores = async () => {
	try {
		const res = await api.get('/stores');
		stores.value = res.data.stores;
	} catch (e) {
		console.log(e);
	}
};

async function submit() {
	errors.image = undefined;
	errors.store_id = undefined;

	if (!image.value) {
		errors.image = 'Image is required';
		toastStore.error(errors.image);
		return;
	}

	if (!storeId.value) {
		errors.store_id = 'Store is required';
		toastStore.error(errors.store_id);
		return;
	}

	const formData = new FormData();
	formData.append('image', image.value);
	formData.append('store_id', String(storeId.value));

	loading.value = true;

	try {
		const { data } = await api.post('/receipts/create', formData, {
			headers: { 'Content-Type': 'multipart/form-data' },
		});

		await router.push({
			name: 'receipt_show',
			params: { id: data.receipt.id },
			state: {
				flash: {
					type: 'success',
					message: 'Receipt created successfully',
				},
			},
		});
	} catch (err: any) {
		if (err.response?.status === 422) {
			const apiErrors = err.response.data.errors ?? {};

			errors.image = apiErrors.image?.[0];
			errors.store_id = apiErrors.store_id?.[0];
		}
	} finally {
		loading.value = false;
	}
}
onMounted(fetchStores);
</script>

<template>
	<div class="mx-auto max-w-2xl py-12">
		<h1 class="mb-4 text-3xl font-bold">Budget Receipt Scanner</h1>

		<p class="mb-8 text-gray-600">
			Upload a receipt image to start processing.
		</p>

		<div class="rounded-lg border p-6">
			<form @submit.prevent="submit" class="space-y-4">
				<div>
					<label class="mb-2 block font-medium"> Store </label>

					<select
						v-model="storeId"
						class="block w-full rounded border bg-black p-2"
					>
						<option value="" disabled>Select store</option>
						<option
							v-for="store in stores"
							:key="store.id"
							:value="store.id"
							class="text-white"
						>
							{{ store.name }}
						</option>
					</select>

					<p v-if="errors.store_id" class="text-sm text-red-600">
						{{ errors.store_id }}
					</p>
				</div>

				<!-- Image upload -->
				<div>
					<label class="mb-2 block font-medium">
						Upload receipt image
					</label>

					<input
						type="file"
						accept="image/*"
						class="mb-2 block w-full rounded-2xl border-2 border-amber-600"
						@change="handleFileChange"
					/>

					<p v-if="errors.image" class="text-sm text-red-600">
						{{ errors.image }}
					</p>
				</div>

				<button
					type="submit"
					class="rounded bg-black px-4 py-2 text-white disabled:opacity-50"
					:disabled="loading || !image || !storeId"
				>
					{{ loading ? 'Uploading…' : 'Upload' }}
				</button>
			</form>
		</div>
	</div>
</template>
