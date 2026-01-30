<script setup lang="ts">
import { RouterLink } from 'vue-router';

const props = defineProps<{
	to: string;
}>();
</script>

<template>
	<RouterLink
		:to="{ name: props.to }"
		class="m-2 flex items-center justify-baseline font-medium"
	>
		<slot></slot>
	</RouterLink>
</template>

<style scoped></style>
