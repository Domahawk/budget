<script setup lang="ts">
import type { User } from '@/types';

type Props = {
	user: User;
};

defineProps<Props>();
</script>

<template>
	<div class="grid flex-1 text-left text-sm leading-tight">
		<span class="truncate font-medium">{{ user.username }}</span>
		<span class="truncate text-xs text-muted-foreground">{{
			user.email
		}}</span>
	</div>
</template>
