<script>
	export default {
		props: {
			nakup: {
				type: String,
				default: '{}'
			},
			basePath: {
				type: String,
				required: true,
			}
		},
		data() {
			return {
				my_nakup: [],
				actual: null,
			}
		},
		methods: {
			viewDetails(id) {
				this.my_nakup.forEach((item) => {
					if (item.id == id) this.actual = item
				})
				this.$bvModal.show("modal-nakup")
			},
			getMyNakup() {
				if (this.nakup.length) this.my_nakup = JSON.parse(this.nakup)
				//console.log(this.my_nakup)
			}
		},
		/*computed: {
			address2() {
				return this.actual != null ? JSON.parse(this.actual.user_profile.address2) : null 
			}
		},*/
		watch: {
			nakup(newValue, oldValue) {
				this.getMyNakup()
			}
		},
		mounted () {
			this.getMyNakup()
		},
	}
</script>

<template>
	<div class="col-12">
		<table class="table table-sm table-stripped" v-if="my_nakup.length">
			<tr>
				<th>Kupujúci</th>
				<th>Dátum</th>
				<th>Cena</th>
				<th>Kód</th>
				<th>Stav</th>
				<th></th>
			</tr>
			<tr v-for="n in my_nakup">
				<td>{{ n.user_name }}</td>
				<td>{{ n.created }}</td>
				<td>{{ n.price }}</td>
				<td>{{ n.code }}</td>
				<td>{{ n.status }}</td>
				<td>
					<button class="btn btn-sm btn-outline-success" :id="'nakup'+n.id" title="Zobraz detail nákupu"
						@click.prevent="viewDetails(n.id)">
						<i class="fa-solid fa-eye"></i>
					</button>
				</td>
			</tr>
		</table>
		<b-modal v-if="actual != null" id="modal-nakup" centered size="xl" :title="'Objednávka č.: ' + actual.code" ok-only
			modal-class="lightbox-img" ref="modal1fo">
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
				<div class="col-6">{{ actual.status }}</div>

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

				<div class="col-12" v-for="p in actual.products">
					<div class="card mb-3">
						<div class="row no-gutters">
							<div class="col-md-4">
								<img :src="basePath + '/' + p.product.thumb_file" :alt="p.product.name">
							</div>
							<div class="col-md-8">
								<div class="card-body">
									<h5 class="card-title">
										{{ p.product.name }}
										<small class="text-muted">Cena: {{ p.product.properties.final_price }} €</small>
									</h5>
									<ul class="list-group list-group-flush">
										<li class="list-group-item" v-for="r in p.product.properties.props">
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