<script setup lang="ts">
import { toTypedSchema } from '@vee-validate/zod';
import type { AxiosError } from 'axios';
import { useForm } from 'vee-validate';
import { useRouter } from 'vue-router';
import * as z from 'zod';
import { authApi } from '@/api/authApi';
import FormField from '@/components/FormField.vue';
import { Button } from '@/components/ui/button';
import {
	Card,
	CardContent,
	CardFooter,
	CardHeader,
	CardTitle,
} from '@/components/ui/card';
import { useErrorStore } from '@/stores/useErrorStore';
import NavbarRouterLink from '@/components/NavbarRouterLink.vue';

const router = useRouter();
const errorStore = useErrorStore();
const formSchema = z
	.object({
		username: z
			.string()
			.min(5, 'Needs to be at least 5 characters')
			.max(100, 'Can be maximium 100 characters'),
		email: z
			.string()
			.email('Please enter a valid email address')
			.max(255, 'Exceeded maximum email address length'),
		password: z
			.string()
			.min(8, 'Password must be at least 8 characters')
			.max(255, 'Password cannot be that large')
			.regex(
				/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/,
				'Password must have at least 1 uppercase letter, 1 lowercase letter, 1 number and 1 special character'
			),
		confirm: z
			.string()
			.min(8, 'Password must be at least 8 characters')
			.max(255, 'Password cannot be that large')
			.regex(
				/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/,
				'Password must have at least 1 uppercase letter, 1 lowercase letter, 1 number and 1 special character'
			),
	})
	.refine((data) => data.password === data.confirm, {
		message: "Passwords don't match",
		path: ['confirm'], // path of error
	});

const { handleSubmit } = useForm({
	validationSchema: toTypedSchema(formSchema),
	initialValues: {
		username: '',
		email: '',
		password: '',
		confirm: '',
	},
});

const onSubmit = handleSubmit(async (values) => {
	try {
		await authApi.register(values);

		await router.push({
			name: 'login',
			state: {
				flash: {
					type: 'success',
					message: 'Account created successfully',
				},
			},
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
</script>

<template>
	<div class="flex justify-center px-4">
		<Card class="flex w-full max-w-md flex-col">
			<CardHeader class="flex justify-center px-4">
				<CardTitle>Create an account</CardTitle>
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
						id="email"
						label="Email"
						type="email"
						placeholder="Email"
					/>
					<FormField
						id="password"
						label="Password"
						type="password"
						placeholder="Password"
					/>
					<FormField
						id="confirm"
						label="Confirm Password"
						type="password"
						placeholder="Confirm Password"
					/>
					<Button type="submit">Submit</Button>
				</form>
			</CardContent>
			<CardFooter class="flex flex-col justify-center px-4 text-sm">
				<span>Already have an account?</span>
				<NavbarRouterLink class="underline" to="login">
					Log In
				</NavbarRouterLink>
			</CardFooter>
		</Card>
	</div>
</template>
