<script setup lang="ts">
import SidebarMenuLink from '@/components/SidebarMenuLink.vue';
import {
	Collapsible,
	CollapsibleContent,
	CollapsibleTrigger,
} from '@/components/ui/collapsible';
import {
	SidebarGroupContent,
	SidebarMenuButton,
	SidebarMenuItem,
	SidebarMenuSub,
} from '@/components/ui/sidebar';
import { useCollapsibleMenuStore } from '@/stores/useCollapsibleMenuStore';
import type { Link } from '@/types/Link';

defineProps<{
	menu: Link;
}>();

const collapsibleMenuStore = useCollapsibleMenuStore();
</script>

<template>
	<SidebarMenuItem>
		<Collapsible
			class="group/collapsible"
			:open="collapsibleMenuStore.isCollapsible(menu)"
			@update:open="
				(value: boolean) =>
					collapsibleMenuStore.openCollapsible(value, menu)
			"
		>
			<CollapsibleTrigger as-child>
				<SidebarMenuButton>
					<component :is="menu.icon" />
					<span>{{ menu.label }}</span>
				</SidebarMenuButton>
			</CollapsibleTrigger>
			<CollapsibleContent
				class="overflow-hidden duration-100 data-[state=closed]:animate-collapsible-up data-[state=open]:animate-collapsible-down"
			>
				<SidebarMenuSub>
					<SidebarGroupContent
						v-for="(link, index) in menu.links"
						:key="index"
					>
						<component
							v-if="link.component.__name == 'SidebarMenuMenu'"
							:is="link.component"
							v-bind="{ menu: link }"
						/>
						<SidebarMenuLink v-else :menu="link" />
					</SidebarGroupContent>
				</SidebarMenuSub>
			</CollapsibleContent>
		</Collapsible>
	</SidebarMenuItem>
</template>
