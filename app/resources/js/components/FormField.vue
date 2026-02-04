<script setup lang="ts">
import { Field as VeeField } from 'vee-validate';
import {
	Field,
	FieldError,
	FieldGroup,
	FieldLabel,
} from '@/components/ui/field';
import { Input } from '@/components/ui/input';

const props = defineProps<{
	id: string;
	label: string;
	placeholder: string;
	type: string;
}>();
</script>

<template>
	<FieldGroup>
		<VeeField v-slot="{ field, errors }" :name="props.id">
			<Field :data-invalid="!!errors.length">
				<FieldLabel>{{ props.label }}</FieldLabel>
				<Input
					:id="props.id"
					v-bind="field"
					:placeholder="props.placeholder"
					:aria-invalid="!!errors.length"
					:type="props.type"
				/>
				<FieldError v-if="errors.length" :errors="errors"></FieldError>
			</Field>
		</VeeField>
	</FieldGroup>
</template>
