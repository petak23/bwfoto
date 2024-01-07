<script>
import PpMainEditForm from './PpMainEditForm'

export default {
	components: {
		PpMainEditForm,
	},
	props: {
		article: {
			type: Object,
		},
	},

}
</script>

<template>
	<div 
		v-if="typeof article.properties !== 'undefined' && article.properties.props.length"
		class="border border-secondary position-relative"
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
		>
			<pp-main-edit-form
				:price="article.price"
				:properties="article.properties"
			>
			</pp-main-edit-form>
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