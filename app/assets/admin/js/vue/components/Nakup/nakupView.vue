<script>
import nakupList from './nakupList.vue';
import MainService from '../../services/MainService.js'

export default {
	components: {
		nakupList,
	},
	props: {
		nakup: {
			type: String,
			default: '{}'
		},
		basePath: {
			type: String,
			required: true,
		},
		spravca: {
			type: String,
			required: true,
		}
	},
	data() {
		return {
			users: null,
			a_spravca: 0,
			a_spravca_form: 0,
		}
	},
	watch: {
		spravca(newValue, oldValue) {
			this.a_spravca = this.a_spravca_form = parseInt(this.spravca)
		}
	},
	computed: {
		text_spravca() {
			let s = ""
			if (this.users != null) {
				this.users.forEach(i => {
					if (i.value == this.a_spravca) s = i.text
				});
			}
			return s 
		}
	},
	methods: {
		viewSpravcaForm() {
			
		},
		getUsersForSpravca() {
			MainService.getUsersForSpravca()
				.then(response => {
					this.users = response.data
				})
				.catch((error) => {
					console.error(error)
				})
		},
		handleOk() {
			let vm = this
			MainService.postSaveUdaj('nakup_spravca', this.a_spravca_form)
				.then(response => {
					if (response.data.result == '200') vm.a_spravca = vm.a_spravca_form
				})
				.catch((error) => {
					console.error(error)
				})
		},
		handleCancel() {
			this.a_spravca_form = this.a_spravca
		}
	},
	mounted () {
		this.getUsersForSpravca();
		this.a_spravca = this.a_spravca_form = parseInt(this.spravca)
	},
}
</script>

<template>
	<span>
		<div class="col-12 row">
			<div class="col-12 col-md-6">
				<h2>Zoznam objednávok:</h2>
			</div>
			<div class="col-12 col-md-6">
				Aktuálny správca nákupov: {{ text_spravca }}
				<button class="btn btn-sm btn-link"
					@click="viewSpravcaForm()" 
					v-b-modal.modal-spravca
				>
					<i class="fa-solid fa-pencil"></i>
				</button>
				<b-modal 
					id="modal-spravca" centered size="xl" 
					title="Zmena oprávnenného správcu nákupov:" 
					no-close-on-backdrop
					no-close-on-esc
					hide-header-close
					modal-class="lightbox-img" ref="modal1fo"
					@ok="handleOk"
					@cancel="handleCancel"
				>
				<div class="form-check" 
					v-for="u in users" :key="u.value">
					<input 
						v-model="a_spravca_form"
						class="form-check-input" type="radio" name="usersRadios" 
						:id="'usersRadios' + u.value" 
						:value="u.value" 
						:checked="a_spravca == u.value">
					<label class="form-check-label" :for="'usersRadios' + u.value">
						{{ u.text }}
					</label>
				</div>
				</b-modal>
			</div>
		</div>

		<nakup-list 
			:nakup="nakup" 
			:base-path="basePath" 
		/>
	</span>
</template>
