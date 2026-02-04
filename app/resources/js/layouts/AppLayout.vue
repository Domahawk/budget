<script setup lang="ts">
import { HouseIcon, Store, List, Plus } from 'lucide-vue-next';
import { Receipt } from 'lucide-vue-next';
import NavbarRouterLink from '@/components/NavbarRouterLink.vue';
import NavUser from '@/components/NavUser.vue';
import SidebarMenuLink from '@/components/SidebarMenuLink.vue';
import SidebarMenuMenu from '@/components/SidebarMenuMenu.vue';

import { Button } from '@/components/ui/button';
import {
	Sidebar,
	SidebarContent,
	SidebarFooter,
	SidebarGroup,
	SidebarGroupContent,
	SidebarGroupLabel,
	SidebarHeader,
	SidebarMenu,
	SidebarMenuButton,
	SidebarMenuItem,
	SidebarProvider,
	SidebarTrigger,
} from '@/components/ui/sidebar';
import Sonner from '@/components/ui/sonner/Sonner.vue';
import { useAuthStore } from '@/stores/authStore';
import type { Link } from '@/types/Link';

const authStore = useAuthStore();

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
				icon: List,
				component: SidebarMenuLink,
			},
			{
				link_name: 'receipt_create',
				label: 'Create Receipt',
				icon: Plus,
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
				icon: List,
				component: SidebarMenuLink,
			},
			{
				link_name: 'store_create',
				label: 'Create Store',
				icon: Plus,
				component: SidebarMenuLink,
			},
		],
	},
];
</script>

<template>
	<div class="flex min-h-screen w-full flex-col items-center justify-center">
		<SidebarProvider>
			<Sidebar v-if="authStore.isAuthenticated" variant="floating">
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
				<SidebarFooter
					v-if="authStore.isAuthenticated"
					class="flex items-center"
				>
					<NavUser />
				</SidebarFooter>
			</Sidebar>
			<main class="flex-1">
				<SidebarTrigger />
				<slot />
			</main>
		</SidebarProvider>
		<Sonner position="top-center" rich-colors theme="dark" />
	</div>
</template>
