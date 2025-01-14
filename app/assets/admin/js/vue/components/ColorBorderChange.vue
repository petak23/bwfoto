<script setup>
/**
 * Komponenta pre zmenu okrajového rámčeka príloh.
 * Posledna zmena 14.01.2025
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2025 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.1
 */
import { ref, computed, onMounted } from 'vue'
import MainService from '../services/MainService'
import { BButton, BModal } from 'bootstrap-vue-next'

const props = defineProps({
	basePath: {
		type: String,
		required: true
	},
	editButtonView: {
		type: String,
		default: '0'
	},
	idHlavneMenu: {
		type: String,
		required: true
	}
})

const border_a_c = ref('#ff0000')
const border_a_w = ref('1')
const border_b_c = ref('#00ff00')
const border_b_w = ref('1')
const border_c_c = ref('#0000ff')
const border_c_w = ref('1')
const data_origin = ref({})

const viewModal = ref(false)

const border_a_l = computed(() => {
	return { border: border_a_w.value+"px solid "+border_a_c.value }
})

const border_b_l = computed(() => {
	return { border: border_b_w.value+"px solid "+border_b_c.value }
})

const border_c_l = computed(() => {
	return { border: border_c_w.value+"px solid "+border_c_c.value }
})

const save = async () => {
	viewModal.value = false
	MainService.postSaveBorder(props.idHlavneMenu, {
			borders: {
				border_a: border_a_c.value + "|" + border_a_w.value,
				border_b: border_b_c.value + "|" + border_b_w.value,
				border_c: border_c_c.value + "|" + border_c_w.value,
			}
		})
		.then(response => {
			data_origin.value = response.data.result
			dataSet(data_origin.value)
		})
		.catch(function (error) {
			console.error(error)
		});      
}

const cancel = () => {
	dataSet(data_origin.value)
	viewModal.value = false
}

const dataSet = (data) => {
	border_a_c.value = data.border_a != null ? data.border_a.split('|')[0] : '#cc0000'
	border_a_w.value = data.border_a != null ? data.border_a.split('|')[1] : '1'
	border_b_c.value = data.border_a != null ? data.border_b.split('|')[0] : '#00cc00'
	border_b_w.value = data.border_a != null ? data.border_b.split('|')[1] : '1'
	border_c_c.value = data.border_a != null ? data.border_c.split('|')[0] : '#0000cc'
	border_c_w.value = data.border_a != null ? data.border_c.split('|')[1] : '1'
}


onMounted(() => {
	// TODO Načítanie údajov priamo z DB alebo zo storu ?!?
	MainService.getOneMainMenuArticle(props.idHlavneMenu)
		.then(response => {
			data_origin.value = response.data
			dataSet(data_origin.value)
		})
		.catch((error) => {
			console.error(error);
		});
})
</script>

<template>
	<div  class="btn-group btn-group-sm"
				role="group" aria-label="okraje-link" 
				n:if="$clanok->hlavne_menu->id_hlavne_menu_template == 2">
		<BButton 
			v-if="props.editButtonView == '1'"
			variant="outline-info"
			@click="viewModal = !viewModal"
		>
			<i class="fas fa-pencil-alt"></i> Nastav okrajový rámček
		</BButton>
	
		<BModal 
			v-model="viewModal"
			title="Zmena okrajového rámčeka"
			ok-title="Ulož"
			centered
			@ok="save"
			@cancel="cancel"
		>
			<div class="row">
				<div class="col-6">    
					<label n:name=border_a_width>Okraj A:</label>&nbsp;
					<input  type="number" 
									id="border_a_width" 
									name="border_a_width" 
									size=2 min=0 max=99 
									class="input_number"
									v-model="border_a_w"> px
					<input  type="color" 
									id="favcolor" 
									name="favcolor" 
									v-model="border_a_c">
					<br />
					<label n:name=border_a_width>Okraj B:</label>&nbsp;
					<input  type="number" 
									id="border_b_width" 
									name="border_b_width" 
									size=2 min=0 max=99 
									class="input_number"
									v-model="border_b_w"> px
					<input  type="color" 
									id="favcolor" 
									name="favcolor" 
									v-model="border_b_c">
					<br />
					<label n:name=border_a_width>Okraj C:</label>&nbsp;
					<input  type="number" 
									id="border_c_width" 
									name="border_c_width" 
									size=2 min=0 max=99 
									class="input_number"
									v-model="border_c_w"> px
					<input  type="color" 
									id="favcolor" 
									name="favcolor" 
									v-model="border_c_c">
				</div>    
				

				<div class="col-6 pv-okraj-nahlad">
					<div class="okraj-nahlad-tmavy">
						<div class="border_x2 okraj-c" v-bind:style="border_c_l">
							<div class="border_x2 okraj-b" v-bind:style="border_b_l">
								<img :src="props.basePath + '/images/okraj_temp.png'" alt="okraj" class="border_x2 okraj-a" v-bind:style="border_a_l">
							</div>
						</div>
					</div>
					<div class="okraj-nahlad-svetly">
						<div class="border_x2 okraj-c" v-bind:style="border_c_l">
							<div class="border_x2 okraj-b" v-bind:style="border_b_l">
								<img :src="props.basePath + '/images/okraj_temp.png'" alt="okraj" class="border_x2 okraj-a" v-bind:style="border_a_l">
							</div>
						</div>
					</div>
				</div>

			</div> 
		</BModal>
	</div>

</template>

<style lang="scss" scoped>
.border_x2 {
	display: inline-block;
}
.okraj-nahlad-tmavy {
	background-color: black;
	padding: 1rem;
	border: 1px solid #999;
}
.okraj-nahlad-svetly {
	background-color: white;
	padding: 1rem;
	border: 1px solid #999;
}
.input_number {
	width: 5rem;
}
</style>