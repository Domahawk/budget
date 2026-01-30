<script setup lang="ts">
import { computed } from 'vue';
import NavbarRouterLink from '@/components/NavbarRouterLink.vue';
import { SidebarMenuButton } from '@/components/ui/sidebar';
import router from '@/router';
import type { Link } from '@/types/Link';

const props = defineProps<{
	menu: Link;
}>();
const isActive = computed(() => {
	return router.currentRoute.value.name == props.menu.link_name;
});
</script>

<template>
	<SidebarMenuButton as-child :isActive="isActive">
		<NavbarRouterLink :to="props.menu.link_name ?? ''">
			<component :is="props.menu.icon" />
			<h3 class="m-1">{{ props.menu.label }}</h3>
		</NavbarRouterLink>
	</SidebarMenuButton>
</template>

<style scoped></style>
