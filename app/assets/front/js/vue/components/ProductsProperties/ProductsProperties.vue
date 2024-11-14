<script setup>
/** 
 * Component ProductsProperties
 * Posledná zmena(last change): 14.11.2024
 *
 * @author Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @copyright Copyright (c) 2021 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.0.1
 * 
 */
import { ref, onMounted, watch } from 'vue'
import { BButton, BModal, BForm, BFormInput, BFormSelect } from 'bootstrap-vue-next';

import MainService from "../../services/MainService.js";

const props = defineProps({
	article: {
		type: Object,
		default: null,
	},
})

const form_price = ref(0)
const form_props = ref({}) // Aktuálne vlastnosti
const view_add = ref(false)
const proProps = ref({}) // Všetky dostupné vlastnosti z DB
//--- for add
const sel_category = ref(null)
const form_categories = ref([])
const sel_value = ref(null)
const form_values = ref([])
const form_plus_perc = ref(null)
const form_plus_sum = ref(null)
const added_props_not_verified = ref(null)
const edit_enabled = ref(false)

const viewModalEditProperties = ref(false)

const emit = defineEmits(['product_update_props'])

const handleOk = () => {
	viewModalEditProperties.value = false
	let to_save = {
		id_products: props.article.id,
		id_products_property: 
			form_props.value.props.filter(item => item.id_property_categories === undefined).map(item => item.id),
		id_new_property: 
			form_props.value.props.filter(item => item.id_property_categories !== undefined).map(item => item.id),
		price: form_price.value,
		final_price: form_props.value.final_price,
	}
	console.log(to_save)
	MainService.postSaveProductProps(to_save)
		.then(response => {
			console.log(response.data);
			emit('product_update_props', [/*{id_product: response.data.id}*/])
		})
}
const reset = () => {
	if (props.article.price !== undefined && edit_enabled.value) {
		form_price.value = props.article.price
		form_props.value = props.article.properties
		calculateFinalPrice()
		getCategories()
	} 
}
const onSubmit = () => {

}
const onReset = () => {
	reset()
}
const delProp = (id) => {
	form_props.value.props = form_props.value.props.filter(function (item) {
		return item.id != id
	})
	calculateFinalPrice()
}
const addFormClear = () => {
	sel_category.value = null
	sel_value.value = null
	form_values.value = []
	form_plus_perc.value = null
	form_plus_sum.value = null
	added_props_not_verified.value = null
}
const addProp = () => {
	view_add.value = false
	if (added_props_not_verified.value != null) {
		form_props.value.props.push(added_props_not_verified.value)
		addFormClear()
		calculateFinalPrice()
	}
}
const calculateFinalPrice = () => {
	let final_price = parseFloat(form_price.value)
	form_props.value.props.forEach((item) => {
		if (item.price_increase_percentage !== null)
			final_price += form_price.value * item.price_increase_percentage / 100
		if (item.price_increase_price !== null) final_price += item.price_increase_price
	})
	form_props.value.final_price = final_price.toFixed(2)
}
const addViewProp = () => {
	view_add.value = true
}
const getCategories = () => {
	MainService.getProductPropsCategories()
		.then(response => {
			proProps.value = response.data
			form_categories.value = [{ value: null, text: 'Vyberte kategóriu' }]
			for (const [key, value] of Object.entries(proProps.value)) {
				form_categories.value.push({
					'value': value[0].id_property_categories,
					'text': value[0].category
				})
				//console.log(`${key}: ${value}`);
			}
			form_values.value = []
		})
		.catch((error) => {
			console.error(error)
		})
}


watch(() => props.article, () => {
	reset()
})

watch(sel_category, (newSel_category) => {
	if (newSel_category != null) {
		sel_value.value = null
		form_values.value = [{ value: null, text: 'Vyberte hodnotu' }]
		proProps.value[newSel_category].forEach((item) => {
			form_values.value.push({
				value: item.id,
				text: item.name
			})
		})
	}
})
watch(sel_value, (newSel_value) => {
	if (sel_category.value !== null) {
		proProps.value[sel_category.value].forEach((item) => {
			if (item.id == newSel_value) {
				form_plus_perc.value = item.price_increase_percentage
				form_plus_sum.value = item.price_increase_price
				added_props_not_verified.value = item
			}
		})
	}
})
watch(form_price, (newForm_price) => {
	if (newForm_price != null && newForm_price > 0) {
		calculateFinalPrice()
	}
})

watch(() => store.user, () => {
	edit_enabled.value = store.checkPermission('Front:Products', 'edit')
})

onMounted(() => {
	reset()
	if (store.user != null) edit_enabled.value = store.checkPermission('Front:Products', 'edit')
})
</script>

<template>
	<div 
		v-if="props.article.properties !== undefined" class="border border-secondary position-relative"
	>
		<BButton 
			v-if="store.user !== null"
			size="sm"
			variant="outline-success"
			class="edit-button"	
			@click="viewModalEditProperties = !viewModalEditProperties"
		>
			<i class="fa-solid fa-pencil"></i>
		</BButton>


		<div>Základná cena: {{ props.article.price }}€</div>
		<div v-for="p in props.article.properties.props">
			{{ p.category }}: {{ p.name }} 
			(
				<span v-if="p.price_increase_percentage !== null"> 
					+{{ p.price_increase_percentage }}% = {{ props.article.price * p.price_increase_percentage / 100 }}€
				</span>
				<span v-else-if="p.price_increase_price !== null"> +{{ p.price_increase_price }}€</span> 
			)
		</div>
		<div class="border-top">Konečná suma: 
			<strong>{{ props.article.properties.final_price }}€</strong>
		</div>

		<BModal 
			v-model="viewModalEditProperties"
			title="Editácia vlastností produktu"
			v-if="store.user !== null"
			centered
			size="lg"
			header-bg-variant="dark"
			header-text-variant="light"
			body-bg-variant="dark"
			body-text-variant="light"
			footer-bg-variant="dark"
			footer-text-variant="light"
			button-size="sm"
			@ok="handleOk"
		>
			<!--pp-main-edit-form
				:price="props.article.price"
				:properties="props.article.properties"
			>
			</!--pp-main-edit-form-->
			<BForm @submit="onSubmit" @reset="onReset">
				<label for="input-price">Základná cena:</label>
				<BFormInput 
					id="input-price"
					v-model="form_price" 
					placeholder="Zadajte cenu"
					type="number"
				></BFormInput>
			</BForm>
			<table class="table table-dark table-striped">
				<thead>
					<tr>
						<th>Kategória</th>
						<th>Hodnota</th>
						<th>Navýšenie ceny o %</th>
						<th>Navýšenie ceny o €</th>
						<th></th>
					</tr>
				</thead>
		  	<tbody>
					<tr v-for="item in form_props.props">
						<td>{{ item.category }}</td>
						<td>{{ item.name }}</td>
						<td>{{ item.price_increase_percentage == null ? '---' : item.price_increase_percentage }}</td>
						<td>{{ item.price_increase_price == null ? '---' : item.price_increase_price }}</td>
						<td><button class="btn btn-sm btn-outline-danger" @click="delProp(item.id)">x</button></td>
					</tr>
					<tr v-if="view_add">
						<td>
							<BFormSelect
								v-model="sel_category"
								:options="form_categories"
								size="sm"
							></BFormSelect>
						</td>
						<td>
							<BFormSelect
								v-if="sel_category != null"
								v-model="sel_value"
								:options="form_values"
								size="sm"
							></BFormSelect>
						</td>
						<td>
							{{ form_plus_perc != null ? form_plus_perc : '---' }}
						</td>
						<td>
							{{ form_plus_sum != null ? form_plus_sum : '---' }}
						</td>
						<td><button class="btn btn-sm btn-success" @click="addProp()">Pridaj</button></td>
					</tr>
					<tr v-else>
						<td colspan="4"></td>
						<td><button class="btn btn-sm btn-outline-success" @click="addViewProp()">+</button></td>
					</tr>
					<tr>
						<td colspan="5">
							Celková cena: {{ form_props.final_price }}€
						</td>
					</tr>
				</tbody>
			</table>
		</BModal>
	</div>
</template>


<style scoped>
.edit-button {
	position: absolute;
	top: 0;
	right: 0;
}
</style>