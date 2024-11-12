<script setup>
import SignInForm from "../components/User/SignInForm"
import RegisterForm from "../components/User/RegisterForm.vue";
import ResetPasswordForm from "../components/User/ResetPasswordForm.vue";

import { useMainStore } from '../store/main'
const store = useMainStore()

const props = defineProps({
	viewRegistrationForm: {
		type: String,
		default: "login",
	}
})
</script>

<template>
	<div 
		class="row justify-content-center"
		v-if="store.user == null"
		>
		<div 
			class="col-12 col-md-10 col-lg-6 text-center"
			v-if="props.viewRegistrationForm == 'resetPassword'"
		>
			<h2><i class="fa-solid fa-arrow-right-to-bracket"></i> Reset hesla</h2>
			
			<reset-password-form
				:id="parseInt($route.params.id)"
				:new_password_key="$route.params.new_password_key"
			/>
		</div>
		<div 
			class="col-12  col-lg-6 text-center"
			v-else-if="props.viewRegistrationForm == 'registration'"
		>
			<h2><i class="fa-solid fa-user-plus"></i> Registrácia</h2>
			
			<register-form />
		</div>
		<div 
			class="col-12 col-md-10 col-lg-6 text-center"
			v-else
		>
			<h2><i class="fa-solid fa-arrow-right-to-bracket"></i> Prihlásenie</h2>
			
			<sign-in-form />
		</div>
	</div>
</template>