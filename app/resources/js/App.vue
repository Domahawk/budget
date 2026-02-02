<script setup lang="ts">
import { watch } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { useErrorStore } from '@/stores/useErrorStore';
import { useToastStore } from '@/stores/useToastStore';

const errorStore = useErrorStore();
const toastStore = useToastStore();

watch(
	() => errorStore.lastError,
	(err) => {
		if (!err) return;

		toastStore.error(err.message);
		errorStore.clear();
	}
);
</script>
<template>
	<AppLayout class="w-full">
		<router-view />
	</AppLayout>
</template>
