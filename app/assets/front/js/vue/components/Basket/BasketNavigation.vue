<script>
/**
 * Komponenta pre vypísanie navigácie nákupu.
 * Posledna zmena 05.03.2024
 *
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.0
 */
export default {
	props: {
		view_part: {
			type: Number,
			default: 1
		},
	},
	data() {
		return {
			items: [
				{ id: 1, key: "Obsah košíka", enabled: true },
				{ id: 2, key: "Adresa", enabled: false },
				{ id: 3, key: "Doprava a platba", enabled: false },
				{ id: 4, key: "Sumár", enabled: false },
			],
		}
	},
	methods: {
		getToPage(id) {
			this.$root.$emit('basket-view-part', [{ view_part: id }])
		}
	},
	created () {
		this.$root.$on("basket-nav-update", data => {  /* formát prichádzajúcich dát: { id: x, enabled: true|false } */
			if (parseInt(data.id) > 0 && parseInt(data.id) <= this.items.length) { // ošetrenie hraníc
				this.items[data.id - 1].enabled = data.enabled == true // ošetrenie, že to bude bool
			}
			if (this.$session.has('basket-nav')) this.$session.remove('basket-nav')
			this.$session.set('basket-nav', JSON.stringify(this.items))
			if (data.view_part != undefined) this.$root.$emit('basket-view-part', [{view_part: data.view_part}])
		})

		if (this.$session.has('basket-nav')) this.items = JSON.parse(this.$session.get('basket-nav'))

	},
}
</script>

<template>
	<div class="row mb-2">
		<div 
			class="col-3"
			v-for="i in items"
			:key="i.id"
		>
			<button 
				@click="getToPage(i.id)"	
				class="w-100 btn btn-sm nav-button"
				:class="[
					i.id == view_part ? 'btn-success disabled' : (i.enabled ? 'btn-secondary' : 'btn-outline-secondary disabled'),
				]"
				:disabled="i.enabled ? false : true"
			>
				{{ i.key }}
			</button>
		</div>
	</div>
</template>

<style scoped>
.nav-button:disabled {
	cursor: not-allowed;
	opacity: .5;
}
</style>