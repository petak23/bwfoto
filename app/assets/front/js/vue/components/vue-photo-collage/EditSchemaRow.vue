<script setup>
/** 
 * Component EditSchemaRow
 * Posledná zmena(last change): 28.11.2024
 *
 * @author Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @copyright Copyright (c) 2021 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.0.3
 */
import { ref, watch, onMounted } from 'vue'
import { BCard, BCardHeader, BCardBody, BCardText, BButton, BCollapse, BFormInput } from 'bootstrap-vue-next'

const props = defineProps({
	row: {
		type: Object,
		default: {},
	},
	id_part: {
		type: Number,
		default: 0,
		required: true,
	}
})

const row_str = ref({
	max_width: null,
	schema: null,
	height: null,
	padding: null,
	widerPhotoId: null,
})
const row_len = ref({
	schema: 0,
	height: 0,
	padding: 0,
	widerPhotoId: 0,
})
const row_state = ref({
	max_width: null,
	schema: null,
	height: null,
	padding: null,
	widerPhotoId: null,
})
const row_state_n = ref(null)
const row_len_mid = ref(0) // Hodnota, ktorú by mali mať všetky časti row_len
const changed = ref(false)

const setRow_str = () => {
	row_str.value = {
		max_width: props.row.max_width,
		schema: props.row.schema.toString(),
		height: props.row.height.toString(),
		padding: props.row.padding.toString(),
		widerPhotoId: props.row.widerPhotoId.toString(),
	}
	row_len.value = {
		schema: row_str.value.schema.split(",").length - 1,
		height: row_str.value.height.split(",").length - 1,
		padding: row_str.value.padding.split(",").length - 1,
		widerPhotoId: row_str.value.widerPhotoId.split(",").length - 1,
	}
	changed.value = false
}
const onSaveRow = (event) => {
	event.preventDefault()
	if (event.srcElement.classList.contains('schema-row-save-' + props.id_part)) {
		let tmp = {
			schema: row_str.value.schema.split(",").map(x => parseInt(x)), // Aby som z textových položiek urobil čísla
			height: row_str.value.height.split(",").map(x => parseInt(x)),
			padding: row_str.value.padding.split(",").map(x => parseInt(x)),
			widerPhotoId: row_str.value.widerPhotoId.split(",").map(x => parseInt(x)),
			max_width: parseInt(row_str.value.max_width),
		}
		// TODO
		//this.$root.$emit("schema-changed", [{ 'id_part': props.id_part, 'data': tmp}])
	}
}
const onCancelRow = (event) => {
	event.preventDefault()
	if (event.srcElement.classList.contains('schema-row-cancel-'+props.id_part)) {
		setRow_str()
		setTimeout(() => {
			changed.value = false
			row_state_n.value = null
		}, 250)
	}
}
const row_items_count = () => {
	row_len_mid.value = (row_len.value.schema + row_len.value.height + row_len.value.padding + row_len.value.widerPhotoId) / 4
}
/* str Testovaný reťazec
 * with_sign Rozlíšenie či môžu byť čísla aj záporné */
const testArray = (str, with_sign = false) => {
	let a = str.trim().split(",")	// Odstráň medzery zo zač. a konca a rozlož do poľa
	let result = true
	// Regulárny výraz pre testovanie reťazca:
	const reg = with_sign ? /^[-]?(\d){1,4}$/ : // test: na zač. môže byť "-" a potom už len 1 až 4 čísla
													/^(\d){1,4}$/				// test: len 1 až 4 čísla
	a.map((i) => {	//Otestuj všetky položky poľa
		if (!reg.test(i)) result = false
	})
	return result ? a.length - 1 : 0	// Ak v poriadku vráť dĺžku poľa inak 0
}

watch(() => props.row, () => {
	setRow_str()
	row_items_count()
})
watch(row_str.value.max_width, () => {
	changed.value = true
})
watch(row_str.value.schema, () => {
	changed.value = true
	row_len.value.schema = this.testArray(row_str.value.schema)
	row_state_n.value = row_len.value.schema == row_len_mid.value
})
watch(row_str.value.height, () => {
	if (row_str.value.height !== null) changed.value = true
	row_len.value.height = testArray(row_str.value.height)
	row_state_n.value = row_len.value.height == row_len_mid.value
})
watch(row_str.value.padding, () => {
	if (row_str.value.padding !== null) changed.value = true
	row_len.value.padding = testArray(row_str.value.padding)
	row_state_n.value = row_len.value.padding == row_len_mid.value
})
watch(row_str.value.widerPhotoId, () => {
	if (row_str.value.widerPhotoId !== null) changed.value = true
	row_len.value.widerPhotoId = testArray(row_str.value.widerPhotoId, true)
	row_state_n.value = row_len.value.widerPhotoId == row_len_mid.value
})

onMounted(() => {
	setRow_str()
		
	row_items_count()

	setTimeout(() => {
		changed.value = false
		row_state.value = {
			schema: null,
			height: null,
			padding: null,
			widerPhotoId: null,
			max_width: null,
		}
		row_state_n.value = null
	}, 100)
})
</script>

<template>
	<BCard no-body class="mb-1">
		<BCardHeader header-tag="header" class="p-1" role="tab">
			<BButton 
				block 
				v-b-toggle="'row-' + row.max_width"
				variant="secondary"
				size="sm"
			>
				Schéma pre max. šírku: {{ row.max_width }} <strong v-if="changed">- zmenené!</strong>
			</BButton>
		</BCardHeader>
		<BCollapse 
			:id="'row-' + row.max_width" 
			visible accordion="my-accordion"
			role="tabpanel"
		>
			<BCardBody>
				<BCardText class="text-dark">
					Schéma pre max. rozlíšenie [px]: <br />
					<BFormInput v-model="row_str.max_width" 
						size="sm" type="number"
					></BFormInput>
				</BCardText>
				<BCardText class="text-dark">
					Počet fotiek v jednotlivých riadkoch: <br />
					<BFormInput v-model="row_str.schema" 
						size="sm" type="text"
						:state="row_state_n"
					></BFormInput>
				</BCardText>
				<BCardText class="text-dark">
					Výška jednotlivých riadkov v px: <br />
					<BFormInput v-model="row_str.height" 
						size="sm" type="text"
						:state="row_state_n"
					></BFormInput>
				</BCardText>
				<BCardText class="text-dark">
					Veľkosť medzery pod daným riadkom: <br />
					<BFormInput v-model="row_str.padding" 
						size="sm" type="text"
						:state="row_state_n"
					></BFormInput>
				</BCardText>
				<BCardText class="text-dark">
					Poradie fotky v riadku, ktorá má byť širšia ako ostatné v riadku: <br />
					<BFormInput v-model="row_str.widerPhotoId" 
						size="sm" type="text"
						:state="row_state_n"
					></BFormInput>
				</BCardText>
				<BCardText>
					<button 
						type="button"
						class="btn btn-secondary btn-sm mr-1"
						:class="'schema-row-cancel-' + id_part"
						@click="onCancelRow"
						:disabled="!(changed && row_state_n != false)"
					>
						Cancel
					</button>
					<button 
						type="button"
						class="btn btn-success btn-sm"
						:class="'schema-row-save-' + id_part"
						@click="onSaveRow"
						:disabled="!(changed && row_state_n != false)	"
					>
						Ulož
					</button>
				</BCardText>
			</BCardBody>
		</BCollapse>
	</BCard>
</template>