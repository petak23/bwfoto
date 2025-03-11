<script setup>
import { ref, watch, computed, onMounted } from 'vue';
import nakupList from '../components/Nakup/nakupList.vue';
import MainService from '../services/MainService.js'

import { useMainStore } from '../store/main.js'
const store = useMainStore()

import { BModal } from 'bootstrap-vue-next';

const users = ref(null)
const a_spravca = ref(0)
const a_spravca_form = ref(0)
const viewSpravcaModal = ref(false)

watch(() => store.udaje_webu, (newValue) => {
	if (newValue != null)
		a_spravca.value = a_spravca_form.value = parseInt(newValue.nakup_spravca)
})

const text_spravca = computed(() => {
	let s = ""
	if (users.value != null) {
		users.value.forEach(i => { if (i.value == a_spravca.value) s = i.text })
	}
	return s  
})

const viewSpravcaForm = () => {
	viewSpravcaModal.value = true
}

const getUsersForSpravca = () => {
	MainService.getUsersForSpravca()
		.then(response => {
			users.value = response.data
		})
		.catch((error) => {
			console.error(error)
		})
}

const handleOk = () => {
	viewSpravcaModal.value = false
	MainService.postSaveUdaj('nakup_spravca', a_spravca_form.value)
		.then(response => {
			if (response.data.result == '200') a_spravca.value = a_spravca_form.value
		})
		.catch((error) => {
			console.error(error)
		})
}

const handleCancel = () => {
	viewSpravcaModal.value = false
	a_spravca_form.value = a_spravca.value
}

onMounted(() => {
	getUsersForSpravca()
	if (store.udaje_webu != null)
		a_spravca.value = a_spravca_form.value = parseInt(store.udaje_webu.nakup_spravca)
})
</script>

<template>
	<div class="col-12 row">
		<div class="col-12 col-md-6">
			<h2>Zoznam objednávok:</h2>
		</div>
		<div class="col-12 col-md-6">
			Aktuálny správca nákupov: {{ text_spravca }}
			<button class="btn btn-sm btn-link"
				@click="viewSpravcaForm()" 
			>
				<i class="fa-solid fa-pencil"></i>
			</button>
			<BModal 
				id="modal-spravca" centered size="xl" 
				title="Zmena oprávnenného správcu nákupov:" 
				no-close-on-backdrop
				no-close-on-esc
				hide-header-close
				modal-class="lightbox-img" ref="modal1fo"
				@ok="handleOk"
				@cancel="handleCancel"
			>
				<div class="form-check" 
					v-for="u in users" :key="u.value">
					<input 
						v-model="a_spravca_form"
						class="form-check-input" type="radio" name="usersRadios" 
						:id="'usersRadios' + u.value" 
						:value="u.value" 
						:checked="a_spravca == u.value">
					<label class="form-check-label" :for="'usersRadios' + u.value">
						{{ u.text }}
					</label>
				</div>
			</BModal>
		</div>
	</div>

	<nakup-list />
</template>
