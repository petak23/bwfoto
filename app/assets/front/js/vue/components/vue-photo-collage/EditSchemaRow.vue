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
				Schéma pre max. šírku: {{ row.max_width }}
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
					{{ row.schema.toString() }}
				</b-card-text>
				<b-card-text class="text-dark">
					Výška jednotlivých riadkov v px: <br />
					{{ row.height.toString() }}
				</b-card-text>
				<b-card-text class="text-dark">
					Veľkosť medzery pod daným riadkom: <br />
					{{ row.padding.toString() }}
				</b-card-text>
				<b-card-text class="text-dark">
					Poradie fotky v riadku, ktorá má byť širšia ako ostatné v riadku: <br />
					{{ row.widerPhotoId.toString() }}
				</b-card-text>
			</b-card-body>
		</b-collapse>
	</b-card>
</template>


<style lang="scss" scoped>

</style>