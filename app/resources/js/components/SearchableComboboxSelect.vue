<script setup lang="ts">
import { ref, watchEffect } from 'vue';
import api from '@/lib/api';

export type Item = {
	name?: string;
	username: string;
};

const props = defineProps<{
	label: string;
	route: string;
	modelValue: Item;
	objectId: string;
}>();

const emit = defineEmits<{
	(e: 'update:modelValue', value: Item): any;
}>();

const search = ref('');
const items = ref<Item[]>([]);
const open = ref(false);
let timeout: number | undefined;

watchEffect(() => {
	if (!props.modelValue.name && !props.modelValue.username) {
		search.value = '';
	}
});

async function fetchItems(query: { search?: string; limit?: number }) {
	const { data } = await api.get(props.route, {
		params: {
			search: query.search,
			limit: query.limit,
		},
	});
	items.value = data[props.objectId];

	open.value = items.value.length > 0;
}

function onInput(e: Event) {
	const value = (e.target as HTMLInputElement).value;
	search.value = value;

	if (timeout) clearTimeout(timeout);

	if (!value) {
		items.value = [];
		open.value = false;
		return;
	}

	timeout = window.setTimeout(() => {
		fetchItems({ search: value });
	}, 300);
}

function select(item: Item) {
	emit('update:modelValue', item);
	search.value = item.name ?? item.username;
	open.value = false;
}
</script>

<template>
	<div class="relative w-full">
		<label :for="route"> {{ label }} </label>
		<input
			:id="route"
			type="text"
			class="w-full rounded border px-3 py-2"
			placeholder="Search…"
			:value="search"
			@input="onInput"
			@focusin="fetchItems({ limit: 10 })"
			@focusout="() => (open = false)"
		/>

		<ul
			v-if="open"
			class="absolute z-50 mt-1 max-h-60 w-full overflow-auto rounded border bg-black shadow"
		>
			<li
				v-for="item in items"
				:key="item.id"
				class="cursor-pointer px-3 py-2 hover:bg-muted"
				@mousedown="select(item)"
			>
				{{ item.name ?? item.username }}
			</li>

			<li
				v-if="items.length === 0"
				class="px-3 py-2 text-sm text-muted-foreground"
			>
				No results
			</li>
		</ul>
	</div>
</template>
