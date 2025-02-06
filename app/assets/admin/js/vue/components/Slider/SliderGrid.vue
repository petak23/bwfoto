<script setup>
/**
 * Komponenta pre vypísanie a spracovanie obrázkov slider-a.
 * Posledna zmena 06.02.2025
 *
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2025 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.2
 */
import { ref, onMounted } from 'vue'
import MainService from '../../services/MainService'
import { BModal } from 'bootstrap-vue-next'
import textCell from '../Grid/TextCell.vue'
import { useFlashStore } from '../../../../../components/FlashMessages/store/flash'
const storeF = useFlashStore()
import { Sortable } from "sortablejs-vue3"

const props = defineProps({
	basePath: {
		type: String,
		required: true,
	},
	sliderDir: {
		type: String,
		required: true,
	},
})

const items = ref([])
const id_p = ref(1)
const loading = ref(0)     // Načítanie údajov 0 - nič, 1 - načítavanie, 2 - chyba načítania
const error_msg = ref('')  // Chybová hláška
const viewModalImg = ref(false)

const deleteSlider = (id) => {
	if (window.confirm('Naozaj chceš vymazať obrázok123 slider-a?')) {
		MainService.getSliderDelete(id)
			.then((response) => {
				if (response.data.data == "OK") {
					items.value = items.value.filter((item) => item.id !== id);
					storeF.showMessage('Položka bola vymazaná.', 'success', 'Podarilo sa...', 5000)
				}
			})
			.catch((error) => {
				console.error(error)
				storeF.showMessage('Došlo k chybe a položka nebola vymazaná!', 'danger', 'Chyba', 15000)
			});
	}
}

const openmodal = (index) => {
	id_p.value = index;
	viewModalImg.value = true
}

const imgUrl = () => {
	return items.value[id_p.value] === undefined
		? "" : props.basePath + "/" + items.value[id_p.value].main_file // ????
}

const imgName = () => {
	return items.value[id_p.value] === undefined
		? "" : items.value[id_p.value].name;
}

const loadItems = () => { // Načítanie údajov priamo z DB
	loading.value = 1
	items.value = []
	MainService.getSliderAll()
		.then((response) => {
			items.value = Object.values(response.data)
			loading.value = 0
		})
		.catch((error) => {
			error_msg.value = 'Nepodarilo sa načítať údaje do tabuľky slideru. <br/>Možná príčina: ' + error
			loading.value = 2
			storeF.showMessage(error_msg.value, 'danger', 'Chyba', 15000)
			console.error(error)
		})
}

const moveArticle = (ai) => {
	let from = ai.oldIndex
	let to = ai.newIndex
	let out = []
	for (let i = 0; i < items.value.length; i++) {
		out.push(items.value[i].id)
	}
	// https://www.codegrepper.com/code-examples/javascript/change+index+order+in+array+javascript
	let element = out[from]
	out.splice(from, 1)
	out.splice(to, 0, element)
	MainService.postSliderSaveOrderVerzie(out)
		.then(function (response) {
			if (response.data.result == 'OK') {
				storeF.showMessage('Poradie bolo zmenené!', 'success', 'OK', 5000)
			}
		})
		.catch((error) => {
			console.error(error)
		})
}

const onSaveData = (d) => {
	MainService.postSliderUpdate(d.id, { nadpis: d.text })
	.then(function (response) {
		items.value[d.id].nadpis = d.text
		storeF.showMessage('Uloženie v poriadku.', 'success', 'Podarilo sa...', 5000)
	})
	.catch((error) => {
		console.error(error)
		storeF.showMessage('Pri uklasaní došlo k chybe: ' + error, 'danger', 'Chyba', 15000)
	})
}

onMounted(() => {
	// Načítanie údajov priamo z DB
	loadItems()
})
</script>

<template>
	<div>
		<table class="table table-bordered table-striped" v-if="loading == 0">
			<caption class="bg-secondary text-white py-1">
				<div class="d-flex justify-content-between">
					<div class="px-2">Počet obrázkov: {{ items.length }}</div>
				</div>
			</caption>
			<thead class="thead-dark">
				<tr>
					<th style="width: 15rem">Obrázok</th>
					<th>Súbor</th>
					<th>Nadpis</th>
					<th>Zobrazenie</th>
					<th class="action-col">Akcie</th>
				</tr>
			</thead>
			<Sortable
				:list="items"
				item-key="id"
				tag="tr"
				class="row"
				:options="{ handle: '.handle' }"
				@end="(event) => moveArticle(event)"
			>
				<template #item="{ element, index }">
					<td class="text-center align-middle">
						<img
							:src="basePath + '/' + props.sliderDir + element.subor"
							:alt="element.subor"
							class="img-thumbnail"
							@click="openmodal(index)"
						/>
					</td>
					<td class="text-right align-middle"><small>{{ element.subor }}</small></td>
					<!--td>{{ element.nadpis !== null ? element.nadpis : 'Bez nadpisu' }}</td-->
					<text-cell
						:value="element.nadpis"
						:id="element.id"
						@saveData="onSaveData"
					/>
					<td>{{ element.zobrazenie !== null ? element.zobrazenie : 'Vždy okrem...' }}</td>
					<td class="align-middle">
						<button class="btn btn-sm btn-secondary handle">
							<i class="fas fa-arrows-alt-v"></i>
						</button>
						<a :href="basePath + '/administration/slider/edit?id=' + element.id" 
							title="Edituj slider" class="btn btn-sm btn-default btn-secondary">
							<span class="fa fa-pencil-alt"></span>
						</a>
						<button
							type="button"
							class="btn btn-danger btn-sm"
							title="Zmaž"
							@click="deleteSlider(element.id)"
						>
							<i class="fa-solid fa-trash-can"></i>
						</button>
					</td>
				</template>
			</Sortable>
		</table>

		<div class="alert alert-danger" v-if="loading == 2" v-html="error_msg"></div>
		<div class="d-flex align-items-center" v-if="loading == 1">
			<strong>Nahrávam...</strong>
			<div class="spinner-border ml-auto" role="status" aria-hidden="true"></div>
		</div>
		<BModal
			centered
			size="xl"
			ok-only
			hide-header
			hide-footer
			dialog-class="slider-dialog"
			v-modal="viewModalImg"
		>
			<img :src="imgUrl()" :alt="imgName()" @click="viewModalImg = false" />
		</BModal>
	</div>
</template>

<style lang="scss" scoped>
.action-col {
	min-width: 40px;
}
button {
	margin-left: 0.1em;
}
#modal-slider {
	.slider-dialog {
		max-width: 95vw !important;
	}

	.modal-body img {
		max-width: 100%;
	}
}
</style>
