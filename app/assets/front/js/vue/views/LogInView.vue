<script>
import SignInForm from "../components/User/SignInForm";

export default {
	components: {
		SignInForm,
	},
}
</script>

<template>
	<div 
		class="row justify-content-center"
		v-if="$store.state.user == null"
		>
		<div class="col-12 col-md-10 col-lg-6 text-center">
			<h2><i class="fas fa-sign-in-alt"></i> Prihlásenie</h2>
			
			<sign-in-form />
		</div>
	</div>
</template>

<style lang="sass" scoped>

</style>