<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { groupsApi } from '@/api/groupsApi';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
	Table,
	TableBody,
	TableCell,
	TableHead,
	TableHeader,
	TableRow,
} from '@/components/ui/table';
import router from '@/router';
import { useAuthStore } from '@/stores/authStore';
import type { Group } from '@/types';

const groups = ref<Group[]>([]);
const loading = ref(false);
const authStore = useAuthStore();

async function fetchGroups() {
	loading.value = true;
	const userId = authStore.user?.id;

	if (!userId) {
		// user needs to log in, although, he cannot access page without being logged in
		return;
	}

	try {
		groups.value = await groupsApi.getGroups(userId);
	} finally {
		loading.value = false;
	}
}

const redirectToGroupEdit = (id: number) => {
	router.push({ name: 'group_edit', params: { id: id } });
};

onMounted(fetchGroups);
</script>

<template>
	<div class="mx-auto max-w-4xl py-12">
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
						<TableRow
							v-for="group in groups"
							:key="group.id"
							@click="redirectToGroupEdit(group.id)"
						>
							<TableCell>{{ group.name }}</TableCell>
							<TableCell class="capitalize">
								{{ group.type }}
							</TableCell>
							<!--							<TableCell class="capitalize">-->
							<!--								{{ group.role }}-->
							<!--							</TableCell>-->
						</TableRow>

						<TableRow v-if="!loading && groups.length === 0">
							<TableCell
								colspan="3"
								class="text-center text-muted-foreground"
							>
								No groups found
							</TableCell>
						</TableRow>
					</TableBody>
				</Table>
			</CardContent>
		</Card>
	</div>
</template>
