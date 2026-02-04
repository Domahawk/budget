<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { useRoute } from 'vue-router';
import { usersApi } from '@/api/usersApi';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import {
	Table,
	TableBody,
	TableCell,
	TableHead,
	TableHeader,
	TableRow,
} from '@/components/ui/table';
import type { Budget, Group, User } from '@/types';

const user = ref<User>({} as User);
const budgets = ref<Budget[]>([]);

const getUser = async () => {
	const id = <string>useRoute().params.id;
	const data = await usersApi.getUser(id);

	user.value = data.user;
	user.value.groups.forEach((group: Group) => {
		budgets.value.push(group.budgets);
	});
};

onMounted(() => {
	getUser();
});
</script>

<template>
	<div class="mx-auto max-w-5xl space-y-8">
		<!-- Basic info -->
		<Card>
			<CardHeader>
				<CardTitle>Basic information</CardTitle>
			</CardHeader>
			<CardContent class="space-y-2">
				<div class="flex justify-between">
					<span class="text-muted-foreground">Username</span>
					<span>{{ user.username }}</span>
				</div>
				<div class="flex justify-between">
					<span class="text-muted-foreground">Email</span>
					<span>{{ user.email }}</span>
				</div>
				<div class="flex justify-between">
					<span class="text-muted-foreground">Member since</span>
					<span>{{ user.created_at }}</span>
				</div>
			</CardContent>
		</Card>

		<!-- Groups -->
		<Card>
			<CardHeader>
				<CardTitle>Groups</CardTitle>
			</CardHeader>
			<CardContent>
				<Table>
					<TableHeader>
						<TableRow>
							<TableHead>Name</TableHead>
							<TableHead>Type</TableHead>
							<TableHead>Role</TableHead>
						</TableRow>
					</TableHeader>
					<TableBody>
						<TableRow v-for="group in user.groups" :key="group.id">
							<TableCell>{{ group.name }}</TableCell>
							<TableCell class="capitalize">{{
								group.type
							}}</TableCell>
							<TableCell class="capitalize">{{
								group.user_role
							}}</TableCell>
						</TableRow>
					</TableBody>
				</Table>
			</CardContent>
		</Card>

		<!--		 Budgets -->
		<Card>
			<CardHeader>
				<CardTitle>Budgets</CardTitle>
			</CardHeader>
			<CardContent>
				<Table>
					<TableHeader>
						<TableRow>
							<TableHead>Name</TableHead>
							<TableHead>Group</TableHead>
							<TableHead class="text-right">Limit</TableHead>
							<TableHead>Period</TableHead>
						</TableRow>
					</TableHeader>
					<TableBody v-if="budgets.length > 0">
						<TableRow v-for="budget in budgets" :key="budget.id">
							<TableCell>{{ budget?.name }}</TableCell>
							<TableCell>{{ budget?.group?.name }}</TableCell>
							<TableCell class="text-right">
								{{ budget?.limit }}
							</TableCell>
							<TableCell>
								{{ budget?.starts_at }} –
								{{ budget?.ends_at ?? '—' }}
							</TableCell>
						</TableRow>
					</TableBody>
				</Table>
			</CardContent>
		</Card>
	</div>
</template>
