<script>
/** 
 * Component EditMenu
 * Posledná zmena(last change): 21.02.2023
 *
 * @author Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @copyright Copyright (c) 2021 - 2023 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.0.0
 * 
 */
export default {
	props: {
		edit_enabled: {
			type: Number,
			default: 0,
		},
		link_to_admin: {
			type: String,
			default: "",
		}
	},
}
</script>

<template>
	<div v-if="edit_enabled == 1"
			class="btn-group btn-group-sm editable" 
			role="group"
	>
		<b-button
			variant="outline-warning"
			size="sm"
			v-b-modal.modal-edit-title-form
			:title="$store.state.texts.base_edit_title"
		>
			<i class="fas fa-pen"></i>
		</b-button>
		<b-button 
			variant="outline-warning"
			size="sm"
			:title="$store.state.texts.base_edit_texts"
			v-b-modal.editArticleTextsModal
		>
			<i class="fa-solid fa-file-lines"></i>
		</b-button>
		<a 
			class="btn btn-sm btn-outline-warning"
			v-if="link_to_admin.length > 0"
			:href="link_to_admin"
			:title="$store.state.texts.base_to_admin"
		>
			<i class="fa-solid fa-person-walking-dashed-line-arrow-right"></i>
		</a>
	</div>
</template>


<style scoped>

</style>