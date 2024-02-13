<script>
import MainService from "../../services/MainService.js";

export default {
	props: {
		price: {
			type: Number, 
		},
		properties: {
			type: Object,
		}
	},
	data() {
		return {
			form_price: 0,
			form_props: {}, // Aktuálne vlastnosti
			view_add: false,
			props:{}, // Všetky dostupné vlastnosti z DB
			//--- for add
			sel_category: null,
			form_categories: [],			
			sel_value: null,
			form_values: [],
			form_plus_perc: null,
			form_plus_sum: null,
			added_props_not_verified: null,
		}
	},
	watch: {
		price: function (newPrice) {
			this.reset()
		},
		sel_category: function (newSel_category) {
			this.sel_value = null
			this.form_values = [{ value: null, text: 'Vyberte hodnotu' }]
			this.props[newSel_category].forEach((item) => {
				this.form_values.push({
					value: item.id,
					text: item.name
				})
			})
		},
		sel_value: function (newSel_value) {
			if (this.sel_category !== null) {
				this.props[this.sel_category].forEach((item) => {
					if (item.id == newSel_value) {
						this.form_plus_perc = item.price_increase_percentage
						this.form_plus_sum = item.price_increase_price
						this.added_props_not_verified = item
					}
				})
			}
		}
	},
	methods: {
		reset() {
			this.form_price = this.price
			this.form_props = this.properties
		},
		onSubmit() {
			
		},
		onReset() {
			this.reset()
		},
		delProp(id) {
			this.form_props.props = this.form_props.props.filter(function(item) {
				return item.id != id
			})
			this.calculateFinalPrice()
		},
		addFormClear() {
			this.sel_category = null
			this.sel_value = null
			this.form_values = []
			this.form_plus_perc = null
			this.form_plus_sum = null
			this.added_props_not_verified = null
		},
		addProp() {
			this.view_add = false
			if (this.added_props_not_verified != null) {
				this.form_props.props.push(this.added_props_not_verified)
				this.addFormClear()
				this.calculateFinalPrice()
			}
		},
		calculateFinalPrice() {
			let final_price = this.form_price
			this.form_props.props.forEach((item) => {
				if (item.price_increase_percentage !== null)
					final_price += this.form_price * item.price_increase_percentage / 100
				if(item.price_increase_price !== null) final_price += item.price_increase_price
			})
			this.form_props.final_price = final_price
		},
		addViewProp() {
			this.view_add = true
		},
		getCategories() {
			MainService.getProductPropsCategories()
				.then(response => {
					this.props = response.data
					this.form_categories = [{ value: null, text: 'Vyberte kategóriu' }]
					for (const [key, value] of Object.entries(this.props)) {
						this.form_categories.push({
							'value': value[0].id_property_categories, 
							'text': 	value[0].category
						})
						//console.log(`${key}: ${value}`);
					}
					this.form_values = []
				})
				.catch((error) => {
					console.error(error)
				})	
		},
	},
	mounted () {
		this.reset()
		this.calculateFinalPrice()
		this.getCategories()
	},
}
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
				<tr v-for="item in form_props.props">
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