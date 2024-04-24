<script>
import MainService from "../../services/MainService.js";

export default {
	props: {
		article: {
			type: Object,
			default: null,
		},
	},
	data() {
		return {
			form_price: 0,
			form_props: {}, // Aktuálne vlastnosti
			view_add: false,
			props: {}, // Všetky dostupné vlastnosti z DB
			//--- for add
			sel_category: null,
			form_categories: [],
			sel_value: null,
			form_values: [],
			form_plus_perc: null,
			form_plus_sum: null,
			added_props_not_verified: null,
			edit_enabled: false,
			to_check_permission: {
				resource: 'Front:Products',
				action: 'edit',
			}
		}
	},
	watch: {
		article: function (newArticle) {
			this.reset()
		},
		sel_category: function (newSel_category) {
			if (newSel_category != null) {
				this.sel_value = null
				this.form_values = [{ value: null, text: 'Vyberte hodnotu' }]
				this.props[newSel_category].forEach((item) => {
					this.form_values.push({
						value: item.id,
						text: item.name
					})
				})
			}
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
		},
		form_price: function (newForm_price) {
			if (newForm_price != null && newForm_price > 0) {
				this.calculateFinalPrice()
			}
		},
		'$store.state.user': function () {
			this.checkPermission(this.to_check_permission)
		}
	},
	methods: {
		handleOk() {
			let to_save = {
				id_products: this.article.id,
				id_products_property: 
					this.form_props.props.filter(item => item.id_property_categories === undefined).map(item => item.id),
				id_new_property: 
					this.form_props.props.filter(item => item.id_property_categories !== undefined).map(item => item.id),
				price: this.form_price,
				final_price: this.form_props.final_price,
			}
			console.log(to_save)
			MainService.postSaveProductProps(to_save)
				.then(response => {
					console.log(response.data);
					this.$root.$emit('product_update_props', [/*{id_product: response.data.id}*/])
				})
		},
		reset() {
			if (this.article.price !== undefined && this.edit_enabled) {
				this.form_price = this.article.price
				this.form_props = this.article.properties
				this.calculateFinalPrice()
				this.getCategories()
			} 
		},
		onSubmit() {

		},
		onReset() {
			this.reset()
		},
		delProp(id) {
			this.form_props.props = this.form_props.props.filter(function (item) {
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
			let final_price = parseFloat(this.form_price)
			this.form_props.props.forEach((item) => {
				if (item.price_increase_percentage !== null)
					final_price += this.form_price * item.price_increase_percentage / 100
				if (item.price_increase_price !== null) final_price += item.price_increase_price
			})
			this.form_props.final_price = final_price.toFixed(2)
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
							'text': value[0].category
						})
						//console.log(`${key}: ${value}`);
					}
					this.form_values = []
				})
				.catch((error) => {
					console.error(error)
				})
		},
		checkPermission(check_perm) {
			this.edit_enabled = false
			if (this.$store.state.user != null && typeof (this.$store.state.user.id) != 'undefined') {
				this.$store.state.user.permission.forEach(function check(item) {
					if (item.resource == check_perm.resource) {
						let p = false
						if (item.action == null) {
							p = true
						} else if (Array.isArray(item.action) && item.action.includes(check_perm.action)) {
							p = true
						}
						this.edit_enabled = p
					}
				}, this)
			}
		}
	},
	mounted() {
		this.reset()
		if (this.$store.state.user != null) this.checkPermission(this.to_check_permission)
	},
}
</script>

<template>
	<div 
		v-if="typeof article.properties !== 'undefined'" class="border border-secondary position-relative"
	>
		<b-button v-b-modal.modal-edit-properties
			v-if="$store.state.user !== null"
			size="sm"
			variant="outline-success"
			class="edit-button"	
		>

			<i class="fa-solid fa-pencil"></i>
		</b-button>


		<div>Základná cena: {{ article.price }}€</div>
		<div v-for="p in article.properties.props">
			{{ p.category }}: {{ p.name }} 
			(
				<span v-if="p.price_increase_percentage !== null"> 
					+{{ p.price_increase_percentage }}% = {{ article.price * p.price_increase_percentage / 100 }}€
				</span>
				<span v-else-if="p.price_increase_price !== null"> +{{ p.price_increase_price }}€</span> 
			)
		</div>
		<div class="border-top">Konečná suma: 
			<strong>{{ article.properties.final_price }}€</strong>
		</div>

		<b-modal id="modal-edit-properties" 
			title="Editácia vlastností produktu"
			v-if="$store.state.user !== null"
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
				:price="article.price"
				:properties="article.properties"
			>
			</!--pp-main-edit-form-->
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
		</b-modal>
	</div>
</template>


<style scoped>
.edit-button {
	position: absolute;
	top: 0;
	right: 0;
}
</style>