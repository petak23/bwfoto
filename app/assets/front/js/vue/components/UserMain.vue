<script setup>
//import { watch, onMounted } from 'vue'
import { useMainStore } from '../store/main'
const store = useMainStore()

</script>

<template>
	<div v-if="store.user" class="d-flex justify-content-between">
		<div>
			<h5>Vitajte, {{ store.user.name }}</h5>
			<div>E-mail: {{ store.user.email }}</div>
			<div>Úroveň: {{ store.user.user_role }}</div>
			<div>Vytvorenie účtu: {{ store.user.created }}</div>
			<div>Posledné Prihlásenie: {{ store.user.prihlas_teraz }}</div>
			<div class="btn-group">
				<button class="btn btn-success btn-sm">Editácia údajov</button>
				<button class="btn btn-warning btn-sm">Zmena hesla</button>
				<button class="btn btn-danger btn-sm ms-3">Zmazanie účtu</button>
			</div>
		</div>
		<div v-if="store.user.avatar !== null">
			<img :src="store.baseUrl + '/' + store.user.avatar" alt="Profilový obrázok" class="rounded-circle" width="100" height="100">
		</div>
	</div>
	<div class="border-top mt-3 pt-2">
		<h5>Zoznam mojich objednávok</h5>
		<div class="d-flex justify-content-between">
			<p>Nateraz je žiaľ prázdny...</p> 
		</div>
	</div>
</template>

<style lang="scss" scoped>

</style>