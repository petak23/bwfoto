<script setup>
import { ref, watch, onMounted } from 'vue'
import MainService from "../../services/MainService.js"

import { useFlashStore } from '../../../../../components/FlashMessages/store/flash'
const storeF = useFlashStore()

const props = defineProps({
	nakup: {
		type: String,
		default: '{}'
	},
	basePath: {
		type: String,
		required: true,
	}
})

const my_nakup = ref([])
const actual_id = ref(0)	// id aktualneho nakupu v my_nakup
const actual = ref(null)
const actual_status_old = ref(0)
const new_status = ref(0)
const nakup_status = ref([])

const viewDetails = (id) => {
	let ii = 0
	my_nakup.value.forEach((item) => {
		if (item.id == id) {
			actual.value = item
			actual_id.value = ii
			actual_status_old.value = actual.value.status
		}
		ii++
	})
}

const getMyNakup = () => {
	if (props.nakup.length) my_nakup.value = JSON.parse(props.nakup)
}

const getAllNakupStatus = () => {
	MainService.getAllNakupStatus()
		.then(response => {
			nakup_status.value = response.data
		})
		.catch((error) => {
			console.error(error)
		})
}

const changeNakupStatus = (event) => { // https://masteringjs.io/tutorials/vue/select-onchange
	//console.log(event.target.value);
	new_status.value = parseInt(event.target.value)
	//console.log(new_status.value);
}

const reset = () => {
	actual.value = null,
	actual_status_old.value = 0
	new_status.value = 0
}

const handleOk = async () => {
	if (new_status.value > 0 && actual.value.id_nakup_status != new_status.value) {
		await MainService.changeNakupStatus(actual.value.id, new_status.value)
			.then(response => {
				//console.log(response.data)
				my_nakup.value[actual_id.value].id_nakup_status = response.data.new_status
				my_nakup.value[actual_id.value].status = nakup_status.value[response.data.new_status]
				reset()
				let message = 'Zmena objednávky č. ' + my_nakup.value[actual_id.value].code + ' na: "' 
				message += my_nakup.value[actual_id.value].status 
				message += '" bola vykonaná. ' + response.data.message 
				storeF.showMessage(message, response.data.status == 200 ? 'success' : 'danger', 'Zmena stavu objednávky', response.data.status == 200 ? 20000 : 60*60*100)
			})
			.catch((error) => {
				console.error(error)
			})
	} else {
		console.log("Nič nerob...");
	}
}

watch(() => props.nakup, (newValue, oldValue) => {
	getMyNakup()
})

onMounted(() => {
	getMyNakup()
	getAllNakupStatus()
})
</script>

<template>
	<div class="col-12">
		<table class="table table-sm table-stripped" v-if="my_nakup.length">
			<tr>
				<th>Kupujúci</th>
				<th>Dátum vytvorenia</th>
				<th>Cena</th>
				<th>Kód</th>
				<th>Stav</th>
				<th></th>
			</tr>
			<tr v-for="n in my_nakup" :key="n.id">
				<td>{{ n.user_name }}</td>
				<td>{{ n.created }}</td>
				<td>{{ n.price }}</td>
				<td>{{ n.code }}</td>
				<td :class="n.id_nakup_status == Object.keys(nakup_status).length ? 'text-muted' : ''">
					{{ n.status }}
				</td>
				<td>
					<button class="btn btn-sm btn-outline-success" :id="'nakup'+n.id" title="Zobraz detail nákupu"
						@click="viewDetails(n.id)" v-b-modal.modal-nakup>
						<i class="fa-solid fa-eye"></i>
					</button>
				</td>
			</tr>
		</table>
		<b-modal 
			v-if="actual != null" 
			id="modal-nakup" centered size="xl" 
			:title="'Objednávka č.: ' + actual.code" 
			ok-only
			no-close-on-backdrop
			no-close-on-esc
			hide-header-close
			modal-class="lightbox-img" ref="modal1fo"
			@ok="handleOk"
		>
			<div class="row">
				<div class="col-6">Kupujúci:</div>
				<div class="col-6">{{ actual.user_name }} </div>

				<div class="col-6">Telefón:</div>
				<div class="col-6">{{ actual.user_profile.phone }} </div>
				<div class="col-6">E-mail:</div>
				<div class="col-6">{{ actual.email }} </div>

				<div class="col-6">Objednávka vytvorená:</div>
				<div class="col-6">{{ actual.created }}</div>

				<div class="col-6">Celková cena:</div>
				<div class="col-6">{{ actual.price }} €</div>

				<div class="col-6">Status:</div>
				<div class="col-6 d-flex justify-content-between">
					<span v-if="actual.id_nakup_status == Object.keys(nakup_status).length" class="text-muted">
						{{ actual.status }}
					</span>

					<select v-else name="nakup_actual_status" @change="changeNakupStatus($event)">
						<option v-for="(item, index) in nakup_status" :key="index" :value="index" :selected="index == actual.id_nakup_status"
							:disabled="index < actual.id_nakup_status"
						>
							{{ item }}
						</option>
					</select>
				</div>

				<div class="col-12">
					<hr />
				</div>

				<div class="col-6">Platba:</div>
				<div class="col-6">{{ actual.shipping.payment.name }}</div>

				<div class="col-6">Doprava:</div>
				<div class="col-6">{{ actual.shipping.shipping.name }}</div>

				<div class="col-6">Poznámka:</div>
				<div class="col-6" :class="actual.shipping.notice == null ? 'text-muted' : ''">
					{{ actual.shipping.notice == null ? 'Bez poznámky.' : actual.shipping.notice }}
				</div>

				<div class="col-12">
					<hr />
				</div>

				<div class="col-12 col-md-6">
					<span class="text-muted">Adresa:</span><br />
					{{ actual.user_profile.street }}<br />
					{{ actual.user_profile.psc }} {{ actual.user_profile.town }}<br />
					{{ actual.user_profile.country }}
				</div>

				<div class="col-12 col-md-6" v-if="actual.user_profile.adress2 != null">
					<span class="text-muted">Dodocia adresa:</span><br />
					{{ actual.user_profile.adress2.street }}<br />
					{{ actual.user_profile.adress2.psc }} {{ actual.user_profile.adress2.town }}<br />
					{{ actual.user_profile.adress2.country }}
				</div>

				<div class="col-12">
					<hr />
				</div>

				<div class="col-12" v-if="actual.user_profile.firm != null">
					<span class="text-muted">Ǔdaje o firme:</span><br />
					Názov: {{ actual.user_profile.firm.name }}<br />
					IČO: {{ actual.user_profile.firm.ico }}<br />
					DIČ: {{ actual.user_profile.firm.dic }}<br />
					IČ DPH: {{ actual.user_profile.firm.icdph }}<br />
					{{ actual.user_profile.firm.street }}<br />
					{{ actual.user_profile.firm.psc }} {{ actual.user_profile.firm.town }}<br />
					{{ actual.user_profile.firm.country }}
				</div>

				<div class="col-12" v-if="actual.user_profile.firm != null">
					<hr />
				</div>

				<div class="col-12">
					<h5>Produkty:</h5>
				</div>

				<div class="col-12" v-for="p in actual.products" :key="p.id">
					<div class="card mb-3">
						<div class="row no-gutters">
							<div class="col-md-4">
								<img :src="props.basePath + '/' + p.product.thumb_file" :alt="p.product.name">
							</div>
							<div class="col-md-8">
								<div class="card-body">
									<h5 class="card-title">
										{{ p.product.name }}
										<small class="text-muted">Cena: {{ p.product.properties.final_price }} €</small>
									</h5>
									<ul class="list-group list-group-flush">
										<li class="list-group-item" v-for="r in p.product.properties.props" :key="r.name">
											{{ r.category }}: <b>{{ r.name }}</b>
										</li>
									</ul>
									<p class="card-text">
										<small class="text-muted">
											{{ p.product.description }}
										</small>
									</p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</b-modal>
	</div>
</template>

<style scoped>

</style>