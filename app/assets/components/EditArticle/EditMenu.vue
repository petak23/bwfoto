<script setup>
/** 
 * Component EditMenu
 * Posledná zmena(last change): 18.02.2025
 *
 * @author Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @copyright Copyright (c) 2021 - 2025 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.0.8
 * 
 */
import { onMounted, ref, watch, computed } from 'vue'
import { BModal, BDropdown, BDropdownItem, BDropdownDivider, BButton, BButtonGroup} from 'bootstrap-vue-next'
import { useMainStore } from '../../front/js/vue/store/main'
const store = useMainStore()
import MainService from '../../front/js/vue/services/MainService'
import EditTexts from "./EditTexts.vue"		

const menuDialogView = ref(false)
const editArticleTextsDialogView = ref(false)

const props = defineProps({
	color_type: { // Typ farebnej schémy tlačidiel 
		type: String,
		default: "primary"
	},
	article: {
		type: Object,
		required: true
	},
})

const templates = ref(null)

const	art_title = ref({
	menu_name: '',
	h1part2: '',
	view_name: '',
	template: 0
})

const getArtTitle = () => {
	if(props.article != null) {
		art_title.value.menu_name = props.article.menu_name
		art_title.value.h1part2 = props.article.h1part2
		art_title.value.view_name = props.article.view_name
		art_title.value.template = props.article.template
	}
}

const emit = defineEmits(['reloadArticle'])

const onSubmitTitle = (event) =>{
	event.preventDefault()
	// Aby sa formulár odoslal, len ak je stačené tlačítko s class="main-submit"
	if (event.submitter.classList.contains("main-submit")) {
		emit('reloadArticle', {
					menu_name: art_title.value.menu_name,
					h1part2: art_title.value.h1part2,
					view_name: art_title.value.view_name,
					template: art_title.value.template,
				})
	}
	menuDialogView.value = false
}

const onResetTitle = (event) => {
	event.preventDefault()
	if (event.explicitOriginalTarget.classList.contains("main-reset")) {
		getArtTitle()
		menuDialogView.value = false	
	}
}

const reloadArticleText = (data) => {
	editArticleTextsDialogView.value = false
	emit('reloadArticle', data)
}

watch(() => props.article, () => {
	getArtTitle()
})

const buttons_class = computed(() => {
	return "outline-" + props.color_type
})

onMounted(() => {
	getArtTitle()
	MainService.getForFormTemplate()
		.then(response => {
			templates.value = response.data
		})
		.catch((error) => {
			console.error(error);
		});
})

</script>

<template>
	<BButtonGroup size="sm">
		<BButton 
			:variant="buttons_class"
			@click.prevent="menuDialogView = true"
			:title="store.texts.base_edit_title"
		>
			<i class="fas fa-pen"></i>
		</BButton>
		<BButton 
			:variant="buttons_class"
			:title="store.texts.base_edit_texts"
			@click.prevent="editArticleTextsDialogView = true"
		>
			<i class="fa-solid fa-file-lines"></i>
		</BButton>
		<BButton
			:variant="buttons_class"
			v-if="store.udaje_webu.adminLink != null && store.udaje_webu.adminLink.length > 0"
			:href="store.udaje_webu.adminLink"
			:title="store.texts.base_to_admin"
		>
			<i class="fa-solid fa-person-walking-dashed-line-arrow-right"></i>
		</BButton>
		<BDropdown right :variant="buttons_class" toggle-class="p-1 rounded-start-0" no-caret>
			<template #button-content>
				&nbsp;<i class="fa-solid fa-ellipsis text-warning"></i>&nbsp;
			</template>
			<BDropdownItem disabled>Pridaj podčlánok</BDropdownItem>
			<BDropdownItem disabled>Pridaj podmenu</BDropdownItem>
			<BDropdownDivider />
			<BDropdownItem disabled variant="outline-danger">Vymaž</BDropdownItem>
		</BDropdown>
	</BButtonGroup>
	
	<BModal 
		v-model="menuDialogView" 
		centered 
		:title="store.texts.base_edit_title" 
		hide-footer
		body-bg-variant="dark"
		header-bg-variant="dark"
		hide-header-close
	>
		<form @submit="onSubmitTitle" @reset="onResetTitle">
			<div id="input-group-1" role="group" class="form-group">
				<label for="view_name" class="d-block">Názov zobrazený v nadpise:</label>
				<div>
					<input 
						id="view_name" type="text" 
						required="required" aria-required="true" class="form-control"
						v-model="art_title.view_name"
					/>
				</div>
			</div>

			<div id="input-group-2" role="group" class="form-group">
				<label for="menu_name" class="d-block">Názov zobrazený v menu:</label>
				<div>
					<input 
						id="menu_name" type="text" 
						class="form-control"
						v-model="art_title.menu_name"
					/>
					<small class="form-text text-muted">
						Ak necháte pole prázdne použije sa rovnaký ako pre nadpis.
					</small>
				</div>
			</div>

			<div id="input-group-3" role="group" class="form-group">
				<label for="h1part2" class="d-block">Podnadpis:</label>
				<div>
					<input 
						id="h1part2" type="text" 
						class="form-control"
						v-model="art_title.h1part2"
					/>
				</div>
			</div>
			
			<div id="input-group-4" role="group" class="form-group mb-2">
				<label for="h1part2" class="d-block">Použitá šablóna:</label>
				<select v-model="art_title.template" id="template-change">
					<option v-for="te in templates" :value="te.value" :key="te.value">{{te.text}}</option>
				</select>
			</div>
			<button class="btn btn-success main-submit me-2" type="submit">Ulož</button>
			<button class="btn btn-secondary main-reset" type="reset" >Cancel</button>
		</form>
	</BModal>
	<edit-texts
		:editArticleTextsDialogView="editArticleTextsDialogView"
		:article="props.article"
		@reloadArticle="reloadArticleText"
	/>
</template>