<script>
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
			form_props: {}
		}
	},
	watch: {
		price: function (newPrice) {
			this.reset()
		},
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
		}
	},
	mounted () {
		this.reset()
	},
}
</script>


<template>
	<b-form @submit="onSubmit" @reset="onReset">
		<label for="input-price">Základná cena:</label>
		<b-form-input 
			id="input-price"
			v-model="form_price" 
			placeholder="Zadajte cenu"
			type="number"
		></b-form-input>
		<b-form-group v-for="item in form_props.props">
			<label for="input-price">
				{{ item.category }}: {{ item.name }} [
				{{ item.price_increase_percentage == null ? '---' : item.price_increase_percentage }}, 
				{{ item.price_increase_price == null ? '---' : item.price_increase_price }}]
			</label>
		</b-form-group>
	</b-form>
</template>



<style lang="scss" scoped>

</style>