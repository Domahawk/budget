<script setup lang="ts">
import { toTypedSchema } from '@vee-validate/zod';
import { useForm } from 'vee-validate';
import { useRouter } from 'vue-router';
import * as z from 'zod';
import { authApi } from '@/api/authApi';
import FormField from '@/components/FormField.vue';
import NavbarRouterLink from '@/components/NavbarRouterLink.vue';
import { Button } from '@/components/ui/button';
import {
	Card,
	CardContent,
	CardFooter,
	CardHeader,
	CardTitle,
} from '@/components/ui/card';
import { useAuthStore } from '@/stores/authStore';

const router = useRouter();
const authStore = useAuthStore();
const formSchema = z.object({
	username: z.string(),
	password: z.string(),
});
const { handleSubmit, setErrors } = useForm({
	validationSchema: toTypedSchema(formSchema),
	initialValues: {
		username: '',
		password: '',
	},
});

const onSubmit = handleSubmit(async (values) => {
	try {
		authStore.user = await authApi.login(values);

		await router.push({
			name: 'home',
			state: {
				flash: {
					type: 'success',
					message: 'Successfully logged in',
				},
			},
		});
	} catch (error: any) {
		setErrors(error.response.data.errors);
	}
});
</script>

<template>
	<div class="flex justify-center px-4">
		<Card class="flex w-full max-w-md flex-col">
			<CardHeader class="flex justify-center px-4">
				<CardTitle>Log In</CardTitle>
			</CardHeader>

			<CardContent>
				<form
					@submit="onSubmit"
					class="flex flex-col items-center space-y-7"
				>
					<FormField
						id="username"
						label="Username"
						type="text"
						placeholder="Username"
					/>
					<FormField
						id="password"
						label="Password"
						type="password"
						placeholder="Password"
					/>
					<Button type="submit">Submit</Button>
				</form>
			</CardContent>
			<CardFooter class="flex flex-col justify-center px-4 text-sm">
				<span>Dont yet have an account? Why not make it:</span>
				<NavbarRouterLink class="underline" to="register">
					Register
				</NavbarRouterLink>
			</CardFooter>
		</Card>
	</div>
</template>
