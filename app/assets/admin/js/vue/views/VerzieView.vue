<script setup>
import { ref, onMounted, computed } from "vue";
import MainService from "../services/MainService"
import { BAccordion, BAccordionItem, BButtonGroup, BButton, BModal } from "bootstrap-vue-next";
import { useMainStore } from '../store/main'
const store = useMainStore()
import VerzieEditForm from "../components/Verzie/VerzieEditForm.vue";

const items = ref(null)
const is_visible = ref(1)

const viewEditModal = ref(false)
const edit_index = ref(null)

const edit = (index) => {
	viewEditModal.value = true
	edit_index.value = index
}

const onSaveEditData = (data) => {
	viewEditModal.value = false
	console.log(data);
	
}

const del = (id) => {}

const sendInfoEmail = (id) => {}

const edit_title = computed(() => {
	return edit_index.value != null ? 
		(edit_index.value == -1 ? 'Pridanie verzie' : 'Editácia verzie: ' + items.value[edit_index.value].cislo) : ""
})

onMounted(() => {
	MainService.getAllVersions()
		.then(response => {
			console.log(response.data);
			
			//console.log(Object.values(response.data))

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
		</div>
	</div>
	<div class="row">
		<div class="col-12">
			<BAccordion>
				<BAccordionItem v-for="(ve, index) in items" :key="ve.id" :visible="ve.id == is_visible">
					<template #title>
						<h5 class="mb-0">
							<strong>{{ ve.cislo }} {{ ve.id }}-{{ index }}</strong>
							<small>&nbsp;|&nbsp;<span class="ver-datum">{{ ve.modified }}</span>&nbsp;|&nbsp;
							<span>(Zadal {{ ve.user }})</span></small>
						</h5>
					</template>
					<BButtonGroup aria-label="Action buttons" v-if="store.checkUserPermission('Admin:Verzie')" size="sm">
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
		:verzia="edit_index != -1 ? items[edit_index] : null"
		@saveVersion="onSaveEditData"
	/>
</BModal>

</template>