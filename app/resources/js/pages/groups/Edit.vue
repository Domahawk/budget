<script setup lang="ts">
import { toTypedSchema } from '@vee-validate/zod';
import type { AxiosError } from 'axios';
import { Minus } from 'lucide-vue-next';
import { useForm } from 'vee-validate';
import { Field as VeeField } from 'vee-validate';
import { ref } from 'vue';
import * as z from 'zod';
import { groupsApi } from '@/api/groupsApi';
import FormField from '@/components/FormField.vue';
import SearchableComboboxSelect from '@/components/SearchableComboboxSelect.vue';
import type { Item as SelectedItem } from '@/components/SearchableComboboxSelect.vue';
import { Button } from '@/components/ui/button';
import {
	Card,
	CardContent,
	CardFooter,
	CardHeader,
	CardTitle,
} from '@/components/ui/card';
import {
	FieldContent,
	FieldError,
	FieldLabel,
	Field,
} from '@/components/ui/field';
import {
	Select,
	SelectItem,
	SelectTrigger,
	SelectValue,
	SelectContent,
} from '@/components/ui/select';
import { Separator } from '@/components/ui/separator';
import router from '@/router';
import { useErrorStore } from '@/stores/useErrorStore';
import type { User } from '@/types';

const users = ref<User[]>([]);
const selectedUser = ref<User>({} as User);
const errorStore = useErrorStore();
const types = <{ value: 'personal' | 'shared'; label: string }[]>[
	{ value: 'personal', label: 'Personal' },
	{ value: 'shared', label: 'Shared' },
];
const formSchema = z.object({
	name: z
		.string()
		.min(5, 'Needs to be at least 5 characters')
		.max(100, 'Can be maximum 100 characters'),
	type: z.enum(['personal', 'shared']),
});
const { handleSubmit } = useForm({
	validationSchema: toTypedSchema(formSchema),
	initialValues: {
		name: '',
		type: types[0].value,
	},
});

const onSubmit = handleSubmit(async (values) => {
	const payload = {
		...values,
		users: users.value.map((user: User) => user.id),
	};
	try {
		const response = await groupsApi.addGroup(payload);

		await router.push({
			name: 'group_edit',
			params: { id: response.id },
		});
	} catch (error: any) {
		if (error.status === 422) {
			errorStore.capture({
				status: error.response.status,
				message: error.response.data.message,
			} as AxiosError);
		}
		return;
	}
});

const updateSelectedUser = (item: SelectedItem) => {
	const foundUsers = users.value.filter((user: User) => {
		return user.username == item.username;
	});

	if (foundUsers.length < 1) {
		users.value.push(item as unknown as User);
	}
	selectedUser.value = {} as User;
};

const removeUser = (id: number) => {
	users.value = users.value.filter((user: User) => user.id != id);
};
</script>

<template>
	<div class="flex flex-col items-center justify-center gap-5 px-4">
		<Card class="flex w-full max-w-md flex-col">
			<CardHeader class="flex justify-center px-4">
				<CardTitle>Create a group</CardTitle>
			</CardHeader>

			<CardContent>
				<form
					@submit="onSubmit"
					class="flex flex-col items-center space-y-7"
				>
					<FormField
						id="name"
						label="Name"
						type="text"
						placeholder="Group Name"
					/>
					<VeeField v-slot="{ field, errors }" name="type">
						<Field
							orientation="responsive"
							:data-invalid="!!errors.length"
						>
							<FieldContent>
								<FieldLabel for="form-vee-select-language">
									Type
								</FieldLabel>
								<FieldError
									v-if="errors.length"
									:errors="errors"
								/>
							</FieldContent>
							<Select
								:name="field.name"
								:model-value="field.value"
								@update:model-value="field.onChange"
							>
								<SelectTrigger
									id="form-vee-select-type"
									:aria-invalid="!!errors.length"
									class="w-full"
								>
									<SelectValue placeholder="Select" />
								</SelectTrigger>
								<SelectContent position="item-aligned">
									<SelectItem
										v-for="type in types"
										:key="type.value"
										:value="type.value"
									>
										{{ type.label }}
									</SelectItem>
								</SelectContent>
							</Select>
						</Field>
					</VeeField>
					<SearchableComboboxSelect
						label="Users"
						route="/users/search"
						v-model="selectedUser"
						object-id="users"
						@update:model-value="updateSelectedUser"
					/>
					<Separator />
					<div
						v-for="user in users"
						:key="user.id"
						class="flex w-full items-center justify-around"
					>
						<span>{{ user.username }}</span>
						<Button
							@click.prevent="removeUser(user.id)"
							class="bg-red-600"
							><Minus
						/></Button>
					</div>
					<Button type="submit">Submit</Button>
				</form>
			</CardContent>
			<CardFooter class="flex flex-col justify-center px-4 text-sm">
			</CardFooter>
		</Card>
	</div>
</template>
