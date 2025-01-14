<script setup>
import { ref, watch, onMounted } from 'vue'
import MainService from "../../services/MainService.js";

const props = defineProps({
	price: {
		type: Number, 
	},
	properties: {
		type: Object,
	}
})

const form_price = ref(0)
const form_props = ref({}) // Aktuálne vlastnosti
const view_add = ref(false)
const product_props = ref({}) // Všetky dostupné vlastnosti z DB
//--- for add
const sel_category = ref(null)
const form_categories = ref([])			
const sel_value = ref(null)
const form_values = ref([])
const form_plus_perc = ref(null)
const form_plus_sum = ref(null)
const added_props_not_verified = ref(null)

const reset = () => {
	form_price.value = props.price
	form_props.value = props.properties
}
const onSubmit = () => {}
const onReset = () => {
	reset()
}
const delProp = (id) => {
	form_props.value.props = form_props.value.props.filter((item) => {
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
	let final_price = form_price.value
	form_props.value.props.forEach((item) => {
		if (item.price_increase_percentage !== null)
			final_price += form_price.value * item.price_increase_percentage / 100
		if(item.price_increase_price !== null) final_price += item.price_increase_price
	})
	form_props.value.final_price = final_price
}
const addViewProp = () => {
	view_add.value = true
}
const getCategories = () => {
	MainService.getProductPropsCategories()
		.then(response => {
			product_props.value = response.data
			form_categories.value = [{ value: null, text: 'Vyberte kategóriu' }]
			for (const [key, value] of Object.entries(product_props.value)) {
				form_categories.value.push({
					'value': value[0].id_property_categories, 
					'text': 	value[0].category
				})
				//console.log(`${key}: ${value}`);
			}
			form_values.value = []
		})
		.catch((error) => {
			console.error(error)
		})	
}

watch(() => props.price, () => {
	reset()
})
watch(sel_category, (newSel_category) => {
	sel_value.value = null
	form_values.value = [{ value: null, text: 'Vyberte hodnotu' }]
	product_props.value[newSel_category].forEach((item) => {
		form_values.value.push({
			value: item.id,
			text: item.name
		})
	})
})
watch(sel_value, (newSel_value) => {
	if (sel_category.value !== null) {
		product_props.value[sel_category.value].forEach((item) => {
			if (item.id == newSel_value) {
				form_plus_perc.value = item.price_increase_percentage
				form_plus_sum.value = item.price_increase_price
				added_props_not_verified.value = item
			}
		})
	}
})

onMounted(() => {
	reset()
	calculateFinalPrice()
	getCategories()
})
</script>


<template>
	<div>
		<b-form @submit="onSubmit" @reset="onReset">
			<label for="input-price">Základná cena:</label>
			<b-form-input 
				id="input-price"
				v-model="form_price" 
				placeholder="Zadajte cenu"
				type="number"
			></b-form-input>
		</b-form>
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
				<tr v-for="item in form_props.props" :key="item.category">
					<td>{{ item.category }}</td>
					<td>{{ item.name }}</td>
					<td>{{ item.price_increase_percentage == null ? '---' : item.price_increase_percentage }}</td>
					<td>{{ item.price_increase_price == null ? '---' : item.price_increase_price }}</td>
					<td><button class="btn btn-sm btn-outline-danger" @click="delProp(item.id)">x</button></td>
				</tr>
				<tr v-if="view_add">
					<td>
						<b-form-select
							v-model="sel_category"
							:options="form_categories"
							size="sm"
						></b-form-select>
					</td>
					<td>
						<b-form-select
							v-if="sel_category != null"
							v-model="sel_value"
							:options="form_values"
							size="sm"
						></b-form-select>
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
	</div>
</template>



<style lang="scss" scoped>

</style>