<script setup>
import { useField } from 'vee-validate';
import { BForm, BFormGroup, BFormInput, BFormInvalidFeedback, BButton, BCard, BCollapse } from 'bootstrap-vue-next'

const props = defineProps({
  name: String,
  type: String,

  description: { // Doplnkový popis pod imput-om
    type: String,
    default: null
  },
  label: {
    type: String,
    default: ""
  }

});

// The `name` is returned in a function because we want to make sure it stays reactive
// If the name changes you want `useField` to be able to pick it up
const { value, errorMessage } = useField(() => props.name);
</script>

<template>
  <input v-model="value" :type="type || 'text'" />
  {{ errorMessage }}

  <BFormGroup 
			:label="props.label"  
			:state="emailState"
			:valid-feedback="emailValidFeedback"
			:invalid-feedback="emailInvalidFeedback"
			label-cols-sm="4"
			label-cols-lg="3"
			content-cols-sm
			content-cols-lg="7"
		>
			<template #description v-if="props.description != null">
				<small class="text-light">{{ props.description }}</small>
			</template>
			<BFormInput
				id="basket-email"
				class="form-control"
				v-model="email"
				type="email"
				placeholder="Zadajte email"
				@blur="validateEmailExistence"
				@input="debouncedValidateEmailExistence"
				:state="emailState"
				:disabled="store.user != null"
				:class="store.user != null ? 'disabled' : ''"
			/>
			<div v-if="isLoading" class="animate-pulse">Kontrolujem email...</div>
			<div v-if="test_email === 2" class="form-text alert alert-warning px-2">
				Vašu e-mailovú adresu sme našli v databáze. 
				Prosím, najprv sa prihláste a potom pokračujte v nákupe. <br />
				<RouterLink to="login" v-if="test_email === 2" class="btn btn-success mt-2">
					{{ store.texts.log_in }}
				</RouterLink>
			</div>
		</BFormGroup>

</template>