<script setup lang="ts">
import { HouseIcon, Store, ListPlus } from 'lucide-vue-next';
import { Receipt } from 'lucide-vue-next';
import type { Component } from 'vue';
import NavbarRouterLink from '@/components/NavbarRouterLink.vue';
import SidebarMenuLink from '@/components/SidebarMenuLink.vue';
import SidebarMenuMenu from '@/components/SidebarMenuMenu.vue';
import {
	Collapsible,
	CollapsibleTrigger,
	CollapsibleContent,
} from '@/components/ui/collapsible';
import {
	Sidebar,
	SidebarContent,
	SidebarFooter,
	SidebarGroup,
	SidebarGroupContent,
	SidebarGroupLabel,
	SidebarHeader,
	SidebarInset,
	SidebarMenu,
	SidebarMenuAction,
	SidebarMenuButton,
	SidebarMenuItem,
	SidebarMenuSub,
	SidebarProvider,
	SidebarRail,
	SidebarTrigger,
} from '@/components/ui/sidebar';
import { useCollapsibleMenuStore } from '@/stores/useCollapsibleMenuStore';
import type { Link } from '@/types/Link';

const collapsibleControl = useCollapsibleMenuStore();

const links: Link[] = [
	{
		link_name: 'home',
		label: 'Home',
		icon: HouseIcon,
		component: SidebarMenuLink,
	},
	{
		label: 'Receipt',
		icon: Receipt,
		component: SidebarMenuMenu,
		links: [
			{
				link_name: 'receipt_list',
				label: 'Receipts',
				icon: Receipt,
				component: SidebarMenuLink,
			},
			{
				link_name: 'receipt_create',
				label: 'Create Receipt',
				icon: ListPlus,
				component: SidebarMenuLink,
			},
		],
	},
	{
		label: 'Store',
		icon: Store,
		component: SidebarMenuMenu,
		links: [
			{
				link_name: 'store_list',
				label: 'Stores',
				icon: ListPlus,
				component: SidebarMenuMenu,
				links: [
					{
						link_name: 'store_list',
						label: 'Sissy',
						icon: ListPlus,
						component: SidebarMenuLink,
						links: [],
					},
				],
			},
			{
				link_name: 'store_create',
				label: 'Create Store',
				icon: ListPlus,
				component: SidebarMenuMenu,
				links: [
					{
						link_name: 'store_list',
						label: 'Sissy',
						icon: ListPlus,
						component: SidebarMenuLink,
						links: [],
					},
				],
			},
		],
	},
];
</script>

<template>
	<div class="flex min-h-screen min-w-screen">
		<SidebarProvider>
			<Sidebar variant="floating">
				<SidebarHeader>
					<SidebarMenu>
						<SidebarMenuItem>
							<SidebarMenuButton>
								<NavbarRouterLink to="home">
									<h1>Budget Hub</h1>
								</NavbarRouterLink>
							</SidebarMenuButton>
						</SidebarMenuItem>
					</SidebarMenu>
				</SidebarHeader>
				<SidebarContent>
					<SidebarGroup>
						<SidebarGroupLabel>Menu</SidebarGroupLabel>
						<SidebarGroupContent
							v-for="(item, index) in links"
							:key="index"
						>
							<component
								:is="item.component"
								v-bind="{ menu: item }"
							/>
						</SidebarGroupContent>
					</SidebarGroup>
				</SidebarContent>
				<SidebarFooter>
					<h2>User things whatever</h2>
				</SidebarFooter>
			</Sidebar>
			<main class="flex-1">
				<SidebarTrigger />
				<slot />
			</main>
		</SidebarProvider>
	</div>
</template>
