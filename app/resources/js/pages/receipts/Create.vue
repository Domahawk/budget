<script setup lang="ts">
import { computed, ref } from 'vue';
import { useRouter } from 'vue-router';
import SearchableComboboxSelect from '@/components/SearchableComboboxSelect.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import api from '@/lib/api';
import { useAuthStore } from '@/stores/authStore';
import type { Group } from '@/types';
import type { Store } from '@/types/store';

const router = useRouter();
const selectedStore = ref<Store>({} as Store);
const selectedGroup = ref<Group>({} as Group);
const authStore = useAuthStore();
const groupRoute = computed(() => {
	return `/users/${authStore.user.id}/groups`;
});
const image = ref<File | null>(null);
const loading = ref(false);

function handleFileChange(e: Event) {
	const input = e.target as HTMLInputElement;
	image.value = input.files?.[0] ?? null;
}

const onSubmit = async () => {
	if (!image.value) {
		return;
	}

	const formData = new FormData();
	formData.append('image', image.value);
	formData.append('store_id', selectedStore.value.id);
	formData.append('group_id', selectedGroup.value.id);

	loading.value = true;

	try {
		const { data } = await api.post('/receipts/create', formData);
		await router.push({
			name: 'receipt_show',
			params: { id: data.receipt.id },
		});
	} catch {
	} finally {
		loading.value = false;
	}
};
</script>

<template>
	<div class="flex justify-center py-12">
		<Card class="w-full max-w-2xl">
			<CardHeader>
				<CardTitle>Upload receipt</CardTitle>
			</CardHeader>

			<CardContent>
				<form @submit.prevent="onSubmit" class="space-y-6">
					<SearchableComboboxSelect
						label="Store"
						route="/stores"
						v-model="selectedStore"
						objectId="stores"
					/>
					<SearchableComboboxSelect
						label="Group"
						:route="groupRoute"
						v-model="selectedGroup"
						objectId="groups"
					/>
					<div class="space-y-2">
						<label class="text-sm font-medium">Receipt image</label>
						<input
							type="file"
							accept="image/*"
							class="block w-full rounded-md border p-2"
							@change="handleFileChange"
						/>
					</div>

					<Button type="submit" :disabled="loading">
						{{ loading ? 'Uploading…' : 'Upload receipt' }}
					</Button>
				</form>
			</CardContent>
		</Card>
	</div>
</template>
