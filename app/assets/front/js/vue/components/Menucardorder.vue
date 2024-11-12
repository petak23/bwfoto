<script setup>
/** 
 * Component Menucardorder
 * Posledná zmena(last change): 09.08.2024
 *
 * @author Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @copyright Copyright (c) 2021 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.0.7
 */

import { /*ref, */watch, onMounted } from 'vue'
import MainService from '../services/MainService'
import { BImg } from 'bootstrap-vue-next'
import { RouterLink } from 'vue-router'
import { Sortable } from "sortablejs-vue3"

import { useMainStore } from '../store/main'
const store = useMainStore()


const props = defineProps({
	/*filesPath: { // Adresár k súborom
		type: String,
		required: true
	}, */
	id_hlavne_menu: { 
		type: Number,
		required: true,
	},
	edit_enabled: {
		type: Boolean,
		default: false
	}
})

//const items = ref([])
/*const mainProps = ref({  //  Momentálne navyužité bolo v <b-img-lazy />
	center: true,
	fluidGrow: true,
	blank: true,
	blankColor: '#bbb',
})*/

const moveArticle = (ai) => {
	let from = ai.from.index
	let to = ai.to.index
	let out = []
	for (let i = 0; i < this.items.length; i++) {
		out.push(this.items[i].id)
	}
	// https://www.codegrepper.com/code-examples/javascript/change+index+order+in+array+javascript
	let element = out[from];
	out.splice(from, 1);
	out.splice(to, 0, element);
	MainService.postSaveOrderSubmenu(props.id_hlavne_menu, { items: out })
		.then((response) => {
			if (response.data.result == 'OK') {
				console.log("OK")
			}
		})
		.catch((error) => {
			console.error(error);
		})
}

/*const getSubmenu = (id_submenu) => {
	items.value = []
	MainService.getSubmenuFront(id_submenu)
		.then(response => {
			items.value = Object.values(response.data)
		})
		.catch((error) => {
			console.error(error);
		})
}*/

watch(() => store.main_menu_active, (newMainMenuActive) => {
	console.log("MCO - watch", newMainMenuActive)
	store.getSubmenu(newMainMenuActive)
})

onMounted(() => {
	console.log("MCO - mounted", props.id_hlavne_menu);
	store.getSubmenu(props.id_hlavne_menu)
})
</script>

<template>
	<section id="webAlbums" class="row" v-if="store.sub_menu != null">
		<div class="col-12 menu-section">
			<Sortable
				:list="store.sub_menu"
				item-key="id"
				tag="div"
				class="row"
				:options="{ handle: '.handle' }"
				@end="(event) => console.log(event)"
			>
				<template #item="{ element, index }">
					<div class="col-12 col-sm-6 col-md-4 col-xxl-3 position-relative draggable card album" :key="element.id">
							<i 
								v-if="props.edit_enabled"
								class="fas fa-grip-vertical handle position-absolute text-white"
								style="top: 0; left: 0"
							></i>
							<RouterLink 
								:to="element.vue_link"
								:title="element.name"
								:replace="true"
							>
								<BImg 
									v-if="element.avatar != null" 
									:src="store.baseUrl + '/' + store.udaje_webu.config.dir_to_menu + element.avatar" 
									class="img-responsive img-square"
									:alt="element.name" 
									:lazy="true" />
								<i v-if="element.node_class != null" :class="element.node_class"> </i>
								<h3>{{ element.name }}</h3>
							</RouterLink>
							<div class="caption">
								<p v-if="element.anotacia" class="popis">
									{{ element.anotacia }} 
									<a :href="element.link" title="more">»»»</a>
								</p>
							</div>
						</div>
				</template>
			</Sortable>
			
			<!--dnd-zone
				:transition-duration="0.3"
				handle-class="handle"
				v-on:move="moveArticle"
			>
				<dnd-container
					:dnd-model="items"
					dnd-id="grid-example"
					class="row"
					dense
				>
					<dnd-item
						v-for="image in items"
						:key="image.id"
						:dnd-id="image.id"
						:dnd-model="image"
					>
						<div class="col-12 col-sm-6 col-md-4 col-xxl-3 album position-relative">
							<i 
								v-if="props.edit_enabled == '1'"
								class="fas fa-grip-vertical handle position-absolute"
								style="top: 0; left: 0"
							></i>
							<a :href="image.link" :title="image.name">
								<BImg 
									v-if="image.avatar != null" 
									:src="store.baseUrl + '/' + store.udaje_webu.config.dir_to_menu + image.avatar" 
									class="img-responsive img-square"
									:alt="image.name" 
									:lazy="true" />
								<i v-if="image.node_class != null" :class="image.node_class"> </i>
								<h3>{{ image.name }}</h3>
							</a>
							<div class="caption">
								<p v-if="image.anotacia" class="popis">
									{{ image.anotacia }} 
									<a :href="image.link" title="more">»»»</a>
								</p>
							</div>
						</div>
					</dnd-item>
				</dnd-container>
			</dnd-zone-->
		</div>
	</section>
</template>

<style lang="scss" scoped>
#webAlbums
{
	display: block;

	.album {
		text-align: center;
		margin: 1rem 0 0 0;
		border: none;
		background-color: transparent;
	}
	h3 {
		margin: .5em 0 0 0;
	}
	/*h3:hover {
		color: #b80;
	}*/
	img {
		outline: .4rem solid rgba(225, 225, 225, 0.7);
		outline-offset: -.4rem;
	}
	img:hover {
		outline: .7rem solid rgba(225, 225, 225, 1);
		outline-offset: -.7rem;
	}
}	// END -- #webAlbums
</style>