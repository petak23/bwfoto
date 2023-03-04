<script>
/** 
 * Component EditSchemaRow
 * Posledná zmena(last change): 04.03.2023
 *
 * @author Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @copyright Copyright (c) 2021 - 2023 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.0.0
 */

export default {
	props: {
		row: {
			type: Object,
			default: {},
		},
		id_part: {
			type: Number,
			default: 0,
			required: true,
		}
	},
	data() {
		return {
			row_str: {
				schema: null,
				height: null,
				padding: null,
				widerPhotoId: null,
			},
			row_len: {
				schema: 0,
				height: 0,
				padding: 0,
				widerPhotoId: 0,
			},
			row_state: {
				schema: null,
				height: null,
				padding: null,
				widerPhotoId: null,
			},
			row_len_mid: 0, // Hodnota, ktorú by mali mať všetky časti row_len
			showSave: false,
			changed: false,
		}
	},
	methods: {
		setRow_str() {
			this.row_str = {
				schema: this.row.schema.toString(),
				height: this.row.height.toString(),
				padding: this.row.padding.toString(),
				widerPhotoId: this.row.widerPhotoId.toString(),
			}
			this.row_len = {
				schema: this.row_str.schema.split(",").length - 1,
				height: this.row_str.height.split(",").length - 1,
				padding: this.row_str.padding.split(",").length - 1,
				widerPhotoId: this.row_str.widerPhotoId.split(",").length - 1,
			}
			this.changed = false
		},
		onSaveRow($event) {
			$event.preventDefault()
			if ($event.submitter.classList.contains('schema-row-save-' + this.id_part)) {

			}
		},
		onCancelRow($event) {
			$event.preventDefault()

		},
		row_items_count() {
			this.row_len_mid = (this.row_len.schema + this.row_len.height + this.row_len.padding + this.row_len.widerPhotoId) / 4
		}
	},
	watch: {
		row(newValue, oldValue) {
			this.setRow_str()
			this.row_items_count();
		},
		'row_str.schema': function () {
			this.changed = true
			this.row_len.schema = this.row_str.schema.split(",").length - 1
			this.row_state.schema = this.row_len.schema == this.row_len_mid 
		},
		'row_str.height': function () {
			if (this.row_str.height !== null) this.changed = true
			this.row_len.height = this.row_str.height.split(",").length - 1
		},
		'row_str.padding': function () {
			if (this.row_str.padding !== null) this.changed = true
			this.row_len.padding = this.row_str.padding.split(",").length - 1
		},
		'row_str.widerPhotoId': function () {
			if (this.row_str.widerPhotoId !== null) this.changed = true
			this.row_len.widerPhotoId = this.row_str.widerPhotoId.split(",").length - 1
		}
	},
	mounted () {
		this.setRow_str()
		this.row_items_count();
		setTimeout(() => {
			this.changed = false
			this.row_state = {
				schema: null,
				height: null,
				padding: null,
				widerPhotoId: null,
			}
		}, 100);
	},
}
</script>


<template>
	<b-card no-body class="mb-1">
		<b-card-header header-tag="header" class="p-1" role="tab">
			<b-button 
				block 
				v-b-toggle="'row-' + row.max_width"
				variant="secondary"
				size="sm"
			>
				Schéma pre max. šírku: {{ row.max_width }} <strong v-if="changed">- zmenené!</strong>
			</b-button>
		</b-card-header>
		<b-collapse 
			:id="'row-' + row.max_width" 
			visible accordion="my-accordion"
			role="tabpanel"
		>
			<b-card-body>
				<b-card-text class="text-dark">
					Počet fotiek v jednotlivých riadkoch: <br />
					<b-form-input v-model="row_str.schema" 
						size="sm" type="text"
						:state="row_state.schema"
					></b-form-input>
				</b-card-text>
				<b-card-text class="text-dark">
					Výška jednotlivých riadkov v px: <br />
					<b-form-input v-model="row_str.height" size="sm" type="text"></b-form-input>
				</b-card-text>
				<b-card-text class="text-dark">
					Veľkosť medzery pod daným riadkom: <br />
					<b-form-input v-model="row_str.padding" size="sm" type="text"></b-form-input>
				</b-card-text>
				<b-card-text class="text-dark">
					Poradie fotky v riadku, ktorá má byť širšia ako ostatné v riadku: <br />
					<b-form-input v-model="row_str.widerPhotoId" size="sm" type="text"></b-form-input>
				</b-card-text>
				<b-card-text>
					<b-button
						type="reset" 
						variant="secondary"
						size="sm"
						class="mr-1"
						:class="'schema-row-cancel-'+id_part"
						@click="onCancelRow()"
						:disabled="!changed"
					>
						Cancel
					</b-button>
	  			<b-button
						variant="success"
						size="sm"
						:class="'schema-row-save-' + id_part"
						@click="onSaveRow()"
						:disabled="!changed"
					>
						Ulož
					</b-button>
					<b-button disabled 
						variant="outline-success" size="sm" 
						class="ml-3 disabled"
						:class="{ 'd-none': !showSave }"
					>
						Uložené
					</b-button>
				</b-card-text>
			</b-card-body>
		</b-collapse>
	</b-card>
</template>


<style lang="scss" scoped>

</style>