<script>

/** 
 * Component UserChange
 * Posledná zmena(last change): 24.04.2024
 *
 * @author Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @copyright Copyright (c) 2021 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.0.4
 * 
 */

import MainService from '../../front/js/vue/services/MainService'

export default {
	data() {
		return {
			users: null,
			id_user_main: 0, // id aktuálneho vlastníka článku
		}
	},
	methods: {
		onSubmit(event) {
			event.preventDefault()
			if (event.submitter.classList.contains("user-change-submit")) {
				this.$bvModal.hide("editUserChangeModal")
				this.$store.dispatch('changeUserMainId', this.id_user_main)
			}
		},
		onReset(event) {
			event.preventDefault()
			if (event.explicitOriginalTarget.classList.contains("user-change-reset")) {
				this.$bvModal.hide("editUserChangeModal")
				this.id_user_main = this.$store.state.article.id_user_main
			}
		}
	},
	watch: {
		'$store.state.article.id_user_main': function () {
			this.id_user_main = this.$store.state.article.id_user_main
		}
	},
	mounted () {
		MainService.getUserChangeFormUsers(4)
			.then(response => {
				this.users = response.data
			})
			.catch((error) => {
				console.log(error);
			});
	},
}
</script>

<template>
	<span>
		<b-button 
			variant="link"
			size="sm"
			title="Zmeň vlastníka položky"
			v-b-modal.editUserChangeModal
		>
			<i class="fas fa-pencil-alt"></i>
		</b-button>
		<b-modal id="editUserChangeModal" centered
			title="Zmeň vlastníka položky"
			header-bg-variant="dark"
			header-text-variant="light"
			body-bg-variant="dark"
			body-text-variant="light"
			footer-bg-variant="dark"
			footer-text-variant="light" 
			:hide-footer="true" 
		>
			<div class="alert alert-info form-info-box">
				<div class="row">
					<div class="col-2 text-center ">
						<i class="fas fa-question-circle fa-2x align-middle"></i>
					</div>
					<div class="col-10">
						<p>Tu môžete zmeniť vlastníctvo tohoto článku na iného užívateľa. Po zmene už ale nebudete mať práva vlastníka článku 
								(napr. editácia článku, nastavení, ...)! </p>
						<p><strong>&nbsp;&nbsp;Preto si tento krok dobre rozmyslite!!!</strong></p>
					</div>
				</div>
			</div>
			<b-form @submit="onSubmit" @reset="onReset">
				<b-form-group
					id="input-group-1"
					label="Nový vlastník:"
					label-for="view_name"
				>
					<b-form-radio-group
						v-model="id_user_main"
						:options="users"
						stacked
						class="mb-3"
					></b-form-radio-group>
				</b-form-group>
				<b-button type="submit" variant="success"  class="user-change-submit mr-2">Ulož</b-button>
				<b-button type="reset" variant="secondary" class="user-change-reset">Cancel</b-button>
			</b-form>
		</b-modal>
	</span>
</template>

<style lang="scss" scoped>

</style>