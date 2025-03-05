<script setup>
import { ref, onMounted, computed } from "vue";
import MainService from "../services/MainService"
import { BAccordion, BAccordionItem, BButtonGroup, BButton, BModal } from "bootstrap-vue-next";

import { useMainStore } from '../store/main'
const store = useMainStore()
import { useFlashStore } from '../../../../components/FlashMessages/store/flash'
const storeF = useFlashStore()

import VerzieEditForm from "../components/Verzie/VerzieEditForm.vue";

const items = ref(null)
const is_visible = ref(1)

const viewEditModal = ref(false)
const edit_index = ref(null)

const edit = (index) => {
	edit_index.value = index
	viewEditModal.value = true
}

const accChange = (txt) => { 
	is_visible.value = txt.split('-')[1]
	const iterator = items.value.keys();

	for (const key of iterator) {
		if (is_visible.value == items.value[key].id) edit_index.value = key
	}
}

const onSaveEditData = (data) => {
	viewEditModal.value = false
	
	if (data == null) return // Cancel
	
	MainService.postSaveVerzie(data.id, data.cislo, data.text)
	.then((response) => {
		items.value = response.data.data
		if (edit_index.value == -1) { is_visible.value = items.value[0].id }
		store.udaje_webu.last_version = items.value[0]
		storeF.showMessage('Verzia bola uložená.', 'success', 'Podarilo sa...', 5000)
	})
	.catch((error) => {
		console.error(error)
		storeF.showMessage('Pri ukladaní došlo k chybe... Skúste neskôr znovu.', 'danger', 'Chyba', 50000)
	})
}

const del = () => {
	if (window.confirm('Naozaj chceš vymazať?')) {
		MainService.getDeleteVersion(is_visible.value)
		.then((response) => {
			console.log(response.data)
			let stc = parseInt(response.data.status)
			if (stc == 200) {
				items.value = items.value.filter((item) => item.id != is_visible.value)
				store.udaje_webu.last_version = items.value[0]
				storeF.showMessage('Verzia bola zmazaná.', 'success', 'Podarilo sa...', 5000)
			} else if (stc = 404){
				storeF.showMessage('Na mazanie nemáte oprávnenie!', 'danger', 'Chyba', 50000)
			} else {
				storeF.showMessage('Pri mazaní došlo k chybe... Skúste neskôr znovu.', 'danger', 'Chyba', 50000)	
			}
			
		})
		.catch((error) => {
			console.error(error)
			storeF.showMessage('Pri mazaní došlo k chybe... Skúste neskôr znovu.', 'danger', 'Chyba', 50000)
		})
	}
}

const sendInfoEmail = () => {
	MainService.getSendInfoMailVersion(is_visible.value)
	.then((response) => {
		console.log(response.data)
		let stc = parseInt(response.data.status)
		if (stc == 200) {
			storeF.showMessage(response.data.message, 'success', 'Podarilo sa...', 5000)
		} else {
			storeF.showMessage(response.data.message, 'danger', 'Chyba', 50000)	
		}
	})
	.catch((error) => {
		console.error(error)
		storeF.showMessage('Pri odosielaní mailu došlo k chybe... Skúste neskôr znovu.', 'danger', 'Chyba', 50000)
	})
}

const edit_title = computed(() => {
	return edit_index.value != null ? 
		(edit_index.value == -1 ? 'Pridanie verzie' : 'Editácia verzie: ' + items.value[edit_index.value].cislo) : ""
})

const active_item = computed(() => {
	return edit_index.value != -1 ? items.value[edit_index.value] : null
})

onMounted(() => {
	MainService.getAllVersions()
		.then(response => {
			console.log(response.data);

			items.value = response.data
			is_visible.value = items.value[0].id
		})
		.catch((error) => {
			console.error(error);
		})
})
</script>

<template>
<div class="col-12">
	<div class="row">
		<div class="col-12">
			<h2>Verzie:</h2>
			<BButton v-if="store.checkUserPermission('Admin:Verzie')" 
				title="Pridanie verzie" 
				variant="success"
				class="button-right"
				@click="edit(-1)"
			>
				<i class="fas fa-file fa-lg"></i> Pridaj novú verziu
			</BButton>
			{{ is_visible }}
		</div>
	</div>
	<div class="row">
		<div class="col-12">
			<BAccordion @update:model-value="accChange">
				<BAccordionItem v-for="(ve, index) in items" :key="ve.id" :visible="ve.id == is_visible" :id="'ve-' + ve.id">
					<template #title>
						<h5 class="mb-0">
							<strong>{{ ve.cislo }} {{ ve.id }}-{{ index }}</strong>
							<small>&nbsp;|&nbsp;<span class="ver-datum">{{ ve.modified }}</span>&nbsp;|&nbsp;
							<span>(Zadal {{ ve.user }})</span></small>
						</h5>
					</template>
					<BButtonGroup aria-label="Action buttons" v-if="store.checkUserPermission('Api:Verzie')" size="sm">
						<BButton variant="success" @click="edit(index)" :title="'Editácia verzie '+ve.cislo">
							<i class="fas fa-pencil-alt fa-lg"></i> Editacia
						</BButton>
						<BButton variant="primary" @click="sendInfoEmail(index)" :title="'Pošli informačný e-mail o verzii '+ve.cislo">
							<i class="fas fa-envelope fa-lg"></i> Info. e-mail
						</BButton>
						<BButton variant="danger" @click="del(index)" :title="'Vymazanie verzie '+ve.cislo">
							<i class="fas fa-trash-alt fa-lg"></i> Vymaž
						</BButton>
					</BButtonGroup>
					<span v-html="ve.text"></span>
				</BAccordionItem>
			</BAccordion>
		</div>
	</div>			
</div>

<BModal 
	v-model="viewEditModal" 
	:title="edit_title" 
	centered  
	hide-footer
	hide-header-close="" 
	v-if="edit_index != null"
>
	<verzie-edit-form 
		:verzia="active_item"
		@saveVersion="onSaveEditData"
	/>
</BModal>

</template>