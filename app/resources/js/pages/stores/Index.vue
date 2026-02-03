<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import {
	AlertDialog,
	AlertDialogCancel,
	AlertDialogDescription,
	AlertDialogFooter,
	AlertDialogHeader,
	AlertDialogTrigger,
	AlertDialogAction,
	AlertDialogContent,
	AlertDialogTitle,
} from '@/components/ui/alert-dialog';
import api from '@/lib/api';
import { useToastStore } from '@/stores/useToastStore';
import type { Store } from '@/types/store';

const router = useRouter();

// local copy so we can mutate after delete
const stores = ref<Store[]>([]);
const toastStore = useToastStore();

const fetchStores = async () => {
	try {
		const res = await api.get('/stores');
		stores.value = res.data.stores;
	} catch (e) {
		console.log(e);
	}
};

async function deleteStore(id: number) {
	try {
		await api.delete(`/stores/${id}`);
		stores.value = stores.value.filter((s) => s.id !== id);
		toastStore.success('Store deleted successfully.');
	} catch (err) {
		alert('Failed to delete store.');
	}
}

onMounted(fetchStores);
</script>

<template>
	<div class="mx-auto max-w-2xl py-10">
		<div class="mb-6 flex items-center justify-between">
			<h1 class="text-2xl font-bold">Stores</h1>

			<button
				class="rounded bg-black px-4 py-2 text-white"
				@click="router.push('/stores/create')"
			>
				Add store
			</button>
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

				<div class="text-sm text-gray-500">
					Receipts: {{ store.receipts_count }}
				</div>
				<AlertDialog>
					<AlertDialogTrigger>
						<button
							class="mt-1 text-sm text-red-600 hover:underline"
						>
							Delete
						</button>
					</AlertDialogTrigger>
					<AlertDialogContent>
						<AlertDialogHeader>
							<AlertDialogTitle>
								Are you sure you want to delete
								{{ store.name }} store?
							</AlertDialogTitle>
						</AlertDialogHeader>
						<AlertDialogDescription>
							This action cannot be undone and will delete the
							store completely.
						</AlertDialogDescription>
						<AlertDialogFooter>
							<AlertDialogAction @click="deleteStore(store.id)">
								Delete
							</AlertDialogAction>
							<AlertDialogCancel>Cancel</AlertDialogCancel>
						</AlertDialogFooter>
					</AlertDialogContent>
				</AlertDialog>
			</div>
		</div>
	</div>
</template>
