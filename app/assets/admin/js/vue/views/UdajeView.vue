<script setup>
import { ref, onMounted } from 'vue'
import MainService from '../services/MainService.js'

import { useMainStore } from '../store/main.js'
const store = useMainStore()

const separate = ref(true)
const udaje_w = ref(null)

onMounted(() => {
	MainService.getUdaje()
	.then((response) => {
		udaje_w.value = response.data
	})
})
</script>

<template>
<div class="col-12">
	<h2>Hlavné udaje webu {{ separate ? '+ separátne nastavenia' : '' }}</h2>
	<div class="row">
		<div class="col-12" v-if="store.checkUserPermission('Admin:Udaje', 'add')">
			<a n:href="Udaje:add"  title="Pridanie nového údaja" class="btn btn-success">
				<i class="fas fa-file-alt fa-lg"></i> Pridanie nového údaja
			</a>
			<button @click="separate =! separate"  :title="(separate ? 'Skry' : 'Zobraz aj') + 'samostatne nastavenia'" class="btn btn-info ms-1">
				<i class="fas fa-lg"
					:class="'fa-eye' + separate ? '-slash' : ''">
				</i> {{ separate ? 'Skry' : 'Zobraz' }} samostatne nastavenia
			</button>
		</div>
	</div>
	<div class="row">
		<div class="col-12 table-responsive">
			<table class="table table-bordered table-hover table-clenovia">
				<thead class="thead-light">
					<tr>
						<th>Komentár</th>
						<th v-if="store.user.id_user_roles == 5">Názov</th>
						<th>Hodnota</th>
						<th v-if="store.user.id_user_roles == 5">Prístup</th>
						<th v-if="store.user.id_user_roles == 5">Druh</th>
						<th v-if="store.user.id_user_roles == 5">Typ</th>
						<th></th>
					</tr>
				</thead>
				<tbody v-if="udaje_w != null">
					<tr v-for="u in udaje_w" :key="u.id">
						<td>{$u->comment}</td>
						<td v-if="store.user.id_user_roles == 5">{$u->nazov}</td>
						<td n:if="$u->id_udaje_typ == 3" n:class="text-white, $u->text == 1 ? bg-success : bg-danger">
							{$p = $u->text == 1 ? 'Povolene' : 'Zakázané'}
						</td>
						<td n:if="$u->id_udaje_typ == 2">
							{var $ra = ($u->comment|uprav_radio)}
							{$ra[(int)$u->text]}
						</td>
						<td n:if="$u->id_udaje_typ < 2 ">{$u->text}</td>
						<td v-if="store.user.id_user_roles == 5">
							{$u->user_roles->role}
							<div n:if="$u->id_user_main != null">
								Len pre: {$u->user_main->name}
							</div>
						</td>
						<td v-if="store.user.id_user_roles == 5">{ifset $u->id_druh}{$u->druh->presenter}{/ifset}</td>
						<td v-if="store.user.id_user_roles == 5">{$u->udaje_typ->nazov}</td>
						<td class="editacne" n:if="$user->isAllowed('Admin:Udaje', 'edit')">
							<a n:href="Udaje:edit $u->id" title="Editacia údaja {$u->nazov}" role="button" class="btn btn-success btn-sm float-left"><i class="fas fa-pencil-alt"></i></a>
							<a href="{link confirmForm:confirmDelete! id => $u->id, nazov => $u->nazov}"
								title="Vymazanie údaja {$u->nazov}" role="button" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></a>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>

</template>

<style lang="scss" scoped>

</style>