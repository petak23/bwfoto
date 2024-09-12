<script>
/**
 * Komponenta pre vypísanie nákupného košíka v hlavnej ponuke.
 * Posledna zmena 04.03.2024
 *
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.1
 */
export default {
	data() {
		return {
			items: []
		}
	},
	methods: {
		getFromSession() {
			let spom = this.$session.getAll()
			this.items = []
			for (const [key, value] of Object.entries(spom)) {
				if (key.startsWith("basket-item")) {
					this.items.push(JSON.parse(value))
				}
			}
			if (this.items.length == 0) {
				this.$session.remove('basket-adress')
				this.$session.remove('basket-shipping')
				this.$session.remove('basket-nav')	
			}
		},
		/*delAll(e) {
			this.$session.clear()
			this.basket = []
			this.$root.$emit("basket-update", [])
		},*/
		delOne(id) {
			this.$session.remove('basket-item-' + id)
			this.$root.$emit("basket-update", [])
			this.getFromSession()
		}
	},
	mounted() {
		this.$session.start()

		this.getFromSession()

		this.$root.$on("basket-insert", item => {
			this.$session.set('basket-item-' + item[0].id_product, JSON.stringify(item[0]))
			this.$root.$emit("basket-update", [])
		});
		this.$root.$on("basket-update", this.getFromSession);
	}
}
</script>

<template>
	<div class="dropdown">
	  <button 
			class="btn btn-success dropdown-toggle"
			type="button"
			data-toggle="dropdown"
			aria-expanded="false"
			:disabled="items.length == 0"
		>
	    <i class="fa-solid fa-basket-shopping"></i>
			<span 
				class="mx-2 badge badge-pill badge-warning"
				v-if="items.length > 0">
				{{ items.length }}
			</span> 
	  </button>
	  <div class="dropdown-menu dropdown-menu-right" v-if="items.length > 0">
	    <div
				v-for="i in items"
				:key="i.id_product" 
				class="d-flex justify-content-between border-bottom"
			>
				<div class="dropdown-item d-flex justify-content-between">
					<a 
						:href="$store.state.basePath + '/clanky/' + i.id_article + '/?first_id=' + i.id_product"
						
					>
						<img :src="$store.state.basePath + '/' + i.product.main_file" class="rounded float-start pe-1" :alt="i.product.name" />
					</a>
					<a 
						:href="$store.state.basePath + '/clanky/' + i.id_article + '/?first_id=' + i.id_product"
						
					>
						{{ i.product.name }}
						<br />{{ i.product.properties.final_price }} €
					</a>
					<button 
						v-if="$store.state.basket.view_part == 1"
						class="btn btn-light btn-sm" 
						@click.prevent="delOne(i.id_product)"
					>
						<i class="fa-regular fa-trash-can text-danger"></i>
					</button>
				</div>
			</div>
			<a class="dropdown-item show-basket mt-2" :href="$store.state.basePath + '/homepage/basket'">
				Zobraz košík
			</a>
	  </div>
	</div>
</template>



<style scoped>
	.dropdown button {
		padding: .4em;
	}
	.dropdown-menu {
		min-width: 22rem;
	}
	.dropdown-menu a {
		font-size: 80%;
	}
	.dropdown-menu img {
		max-width: 4rem;
	}
	.show-basket {
		background-color: rgba(120, 233, 116, 0.41);
	}
</style>