<script>
/**
 * Komponenta pre vypísanie obľúbených produktov.
 * Posledna zmena 12.09.2024
 *
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.2
 * 
 * @description https://www.npmjs.com/package/vue-session
 */

export default {
	data() {
		return {
			/* Štruktúra položiek poľa liked:
				{
					id_product: 0,
					id_article: 0,
					source: "",
					name: "",
				}*/
			liked: [], 
		}
	},
	methods: {
		getFromSession() {
			let spom = this.$session.getAll()
			this.liked = []
			for (const [key, value] of Object.entries(spom)) {
				if (key.startsWith("like")) {
					this.liked.push(JSON.parse(value))
				}
			}
			this.$store.commit("UPDATE_PRODUCTS_LIKE_ITEMS", this.liked)
		},
		delAll() { // Vymazanie všetkých obľúbených položiek
			let vm = this
			this.liked.forEach(function(i) {
				vm.$session.remove('like-' + i.id_product)	
			})
			this.getFromSession()
		},
		delOne(id) { // Vymazanie jednej obľúbenej položky
			this.$session.remove('like-' + id)
			this.getFromSession()
		}
	},
	watch: {
		'$store.state.productsLikeItem': function () {
			this.liked = this.$store.state.productsLikeItem
		}
	},
	mounted () {
		this.$session.start()

		this.getFromSession()
	}
}
</script>

<template>
	<div class="liked" v-if="liked.length > 0">
		<div class="btn-group dropup">
			<button 
				type="button" 
				class="btn btn-lg btn-warning dropdown-toggle rounded-pill" 
				data-toggle="dropdown" aria-expanded="false"
			>
				<i class="fa-regular fa-heart my-heart"></i>
				<span class="badge badge-pill badge-warning">
					{{ liked.length }}
				</span>
			</button>
			<ul class="dropdown-menu" v-if="$store.state.app_settings != null">
				<li
					v-for="i in liked"
					:key="i.id_product" 
					class="dropdown-item-text d-flex justify-content-between mx-2"
				>
					<a :href="$store.state.app_settings.basePath + '/clanky/' + i.id_article + '/?first_id=' + i.id_product">
						
						<img :src="$store.state.app_settings.basePath + '/' + i.source" class="rounded float-start pe-1" :alt="i.name" />
						{{ i.name }}
					</a>
					<b-button variant="light" @click.prevent="delOne(i.id_product)">
						<i class="fa-regular fa-trash-can text-danger"></i>
					</b-button>
				</li>
				<li><hr class="dropdown-divider"></li>
				<li><a class="dropdown-item px-2 del-item" href="#" @click.prevent="delAll">Vymaž všetky obľúbené foto</a></li>
				<li><a class="dropdown-item px-2 mt-2 all-item" :href="$store.state.app_settings.basePath + '/homepage/productlike'">Zobraz obľúbené foto</a></li>
			</ul>
		</div>
	</div>
</template>

<style scoped>
.liked {
  font-size: 0.9rem;
	position: fixed;
	left: 4rem;
	bottom: 1rem;
  max-width: 50vw;
  z-index: 2000;
	border-width: .25rem;
}

/*.my-heart {
	margin-left: 1ex;
	margin-bottom: 1ex;
	margin-top: 1ex;
}*/
.badge {
	position: absolute !important;
	top: -10px !important;
	right: -10px;
}
.dropdown-menu {
	min-width: 22rem;
}
.dropdown-item-text img {
	max-width: 4rem;
}
.del-item {
	background-color: rgba(233, 116, 116, 0.41);
}
.all-item {
	background-color: rgba(120, 233, 116, 0.41);
}
</style>