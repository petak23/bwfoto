<script setup>
import { useMainStore } from '../store/main'
const store = useMainStore()

import LastLogin from '../components/MainFrame/LastLogin.vue'
</script>

<template>
	<div class="col mb-4" v-if="store.user != null">
		<div class="card h-100 last-login">
			<h5 class="card-header">Užívateľské info</h5>
			<div class="card-body">
				<table class="table table-striped table-sm">
					<tbody>
						<tr>
							<th scope="row">Id:</th>
							<td>{{ store.user.id }}</td>
						</tr>
						<tr>
							<th scope="row">Rola:</th>
							<td>{{ store.user.user_role }}</td>
						</tr>
						<tr>
							<th scope="row">Meno a priezvisko:</th>
							<td>{{ store.user.name }}</td>
						</tr>
						<tr>
							<th scope="row">Posledná IP:</th>
							<td>{{ store.user.last_ip }}</td>
						</tr>
						<tr>
							<th scope="row">Registrácia:</th>
							<td>{{ store.user.created }}</td>
						</tr>
						<tr>
							<th scope="row">Posledné prihlásenie:</th>
							<td>{{ store.user.prihlas_teraz }}</td>
						</tr>
						<tr>
							<th scope="row">Počet prihlásení:</th>
							<td>{{ store.user.pocet_pr }}</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>

	<div class="col mb-4">
		<last-login />
	</div>

</template>
