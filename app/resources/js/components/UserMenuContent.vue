<script setup lang="ts">
import { LogOut } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import {
	DropdownMenuGroup,
	DropdownMenuItem,
	DropdownMenuLabel,
	DropdownMenuSeparator,
} from '@/components/ui/dropdown-menu';
import UserInfo from '@/components/UserInfo.vue';
import { useAuthStore } from '@/stores/authStore';
import type { User } from '@/types';
import { open } from 'node:fs';
import type { ReceiptItemRow } from '@/types/receiptItemRow';
import type { RowError } from '@/types/rowError';

type Props = {
	user: User;
};

const authStore = useAuthStore();

const handleLogout = () => {
	authStore.logout();
};

const emit = defineEmits<{
	(e: 'open'): void;
}>();
defineProps<Props>();
</script>

<template>
	<DropdownMenuLabel class="p-0 font-normal" @click="emit('open')">
		<RouterLink :to="{ name: 'user_show', params: { id: user.id } }">
			<div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
				<UserInfo :user="user" :show-email="true" />
			</div>
		</RouterLink>
	</DropdownMenuLabel>
	<DropdownMenuSeparator />
	<DropdownMenuGroup> </DropdownMenuGroup>
	<DropdownMenuSeparator />
	<DropdownMenuItem :as-child="true">
		<Button
			class="flex w-full cursor-pointer"
			@click="handleLogout"
			data-test="logout-button"
		>
			<LogOut class="mr-2 h-4 w-4" />
			Log out
		</Button>
	</DropdownMenuItem>
</template>
