<script setup>
/** 
 * @lastchange 27.11.2024
 *
 * @editedby Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @version 1.0.2
 */
import PhotoCollage from "./PhotoCollage.vue" //v3
import { ref, computed, watch, onMounted } from "vue"

function createPhotoIds(photos) {
	return photos.map((data, i) => {
		return { ...data, id: i };
	});
}

function createLayoutPhotoMaps(layout, photos) {
	const newPhotos = createPhotoIds(photos);
	const newMaps = {};
	layout.reduce((accumulator, currentValue, currentIndex) => {
		newMaps[currentIndex] = newPhotos.slice(
			accumulator,
			accumulator + currentValue
		);
		return accumulator + currentValue;
	}, 0);

	return newMaps;
}

const props = defineProps({
	disabled: {
		type: Boolean,
		default: false,
	},
	width: {
		type: String,
		default: "800px",
	},
	height: {
		type: Array,
	},
	padding: {
		type: Array,
	},
	layout: {
		type: Array,
		default: [],
	},
	photos: {
		type: Array,
		default: [],
	},
	showNumOfRemainingPhotos: {
		type: Boolean,
		default: false,
	},
	maxRandomPercWidth: {
		type: Number,
		default: 20
	},
	widerPhotoId: {
		type: Array,
		default: [],
	},
	gapSize: String,
	borderRadius: String,
})

//const layout = ref(props.layout)

const internalHeight = ref([])
const internalPadding = ref([])
const allowRender = ref(false)
const layoutPhotoMaps = ref({})
const viewerIsOpen = ref(false)
const currentImage = ref(0)

watch(layoutPhotoMaps, () => {
	allowRender.value = !Object.keys(layoutPhotoMaps.value).length
})

watch(() => props.gapSize, () => {
	setGapSize()
}, { deep: true })

watch(() => props.borderRadius, () => {
	setBorderRadius()
}, { deep: true })

watch(() => props.layout, () => {
	reconfigurate()
}, { deep: true })

watch(() => props.photos, () => {
	reconfigurate()
}, { deep: true })

watch(() => props.height, () => {
	internalHeight.layout = props.height
	let r = props.layout.length - internalHeight.value.length // Počet riadkov, pre ktoré nemám výšku
	if (r > 0) {
		for (let i = 0; i < r; i++) { // Doplnenie výšok riadkov ak chýbajú
			internalHeight.value.push(props.height[i % props.height.length])
		}
	}
}, { deep: true })

watch(() => props.padding, () => {
	internalPadding.value = props.padding
	let r = props.layout.length - internalPadding.value.length // Počet riadkov, pre ktoré nemám padding
	if (r > 0) {
		for (let i = 0; i < r; i++) { // Doplnenie výšok riadkov ak chýbajú
			internalPadding.value.push(props.padding[i % props.padding.length])
		}
	}
}, { deep: true })

const reconfigurate = () => {
	layoutPhotoMaps.value = createLayoutPhotoMaps(props.layout, props.photos);
	setGapSize()
	setBorderRadius()
}
const setGapSize = () => {
	if (document) {
		document.documentElement.style.setProperty(
			"--vue-photo-grid-gap",
			props.gapSize
		)
	}
}
const setBorderRadius = () => {
	if (document) {
		document.documentElement.style.setProperty(
			"--vue-photo-grid-radius",
			props.borderRadius
		);
	}
}

const layoutNum	= computed(() => {
	return props.layout.reduce((accumulator, currentValue) => accumulator + currentValue, 0)
})

const remainingNum = computed(() => {
	return props.photos.length - layoutNum
})

onMounted(() => {
	reconfigurate()
})
</script>

<template>
	<div class="vue-photo-collage-wrapper">
		<photo-collage
			:disabled="disabled"
			:width="width"
			:height="internalHeight"
			:padding="internalPadding"
			:layout="layout"
			:layoutPhotoMaps="layoutPhotoMaps"
			:layoutNum="layoutNum"
			:remainingNum="remainingNum"
			:showNumOfRemainingPhotos="showNumOfRemainingPhotos"
			:maxRandomPercWidth="maxRandomPercWidth"
			:widerPhotoId="widerPhotoId"
			@itemClick="(data, i) => $emit('itemClick', data, i)"
		>
		</photo-collage>
	</div>
</template>