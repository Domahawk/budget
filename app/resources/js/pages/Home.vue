<script setup lang="ts">
import api from '@/lib/api';

const onClick = async () => {
	await api.get('/error');
};
</script>

<template>
	<div class="mx-auto min-w-full bg-pink-300 py-12">
		<h1 class="mb-4 text-3xl font-bold">Budget Receipt Scanner</h1>
		<button @click="onClick">Click me</button>
		<p class="text-gray-600">Upload receipts and track expenses.</p>
	</div>
</template>
