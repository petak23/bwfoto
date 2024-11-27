<script setup>
/** 
 * @lastchange 27.11.2024
 *
 * @editedby Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @version 1.0.3
 */
import { computed } from "vue"

import PhotoGrid from "./PhotoGrid.vue" //v3
import PhotoRow from "./PhotoRow.vue"	//v3
import PhotoMask from "./PhotoMask.vue" //v3
import ViewMore from "./ViewMore.vue" //v3
import NumOfRemaining from "./NumOfRemaining.vue" //v3
import PhotoThumb from './PhotoThumb.vue' //v3

const props = defineProps({
	height: Number,   // Výška riadku v px
	padding: Number,  // Veľkosť padding-bottom
	photos: Array,
	layoutNum: Number,
	remainingNum: Number,
	showNumOfRemainingPhotos: Boolean,
	maxRandomPercWidth: Number, // Percento, o ktoré sa môže meniť naviac šírka fotky
	widerPhotoId: Number //Poradové id fotky, ktorá má byť širšia
})

/* Ak je menej ako 2 fotky alebo poradie širšej fotky je -1 tak vráť -1(všetky fotky budú rovnako široké)
 * inak ak je poradie širšej fotky zadané( > 0) vráť (poradie - 1)
 * inak generuj náhodné id
 * this.widerPhotoId: 
 * poradie fotky v riadku, ktorá má byť širšia 1,2,... 
 * ak je zadané 0 generuje sa náhodne
 * ak je zadané -1 všetky fotky budú rovnaké */
const randId = computed(() => {
	return (props.photos.length < 2 || props.widerPhotoId == -1) ? -1
								: (props.widerPhotoId > 0 ? (props.widerPhotoId - 1)
																				 : Math.floor(Math.random() * props.photos.length)) 
})

// Náhodných maximálne maxRandomPercWidth percent k šírke prvku
const photoStyle = computed(() => {
	return { width: Math.floor(Math.random() * (props.maxRandomPercWidth +1))	}
})
</script>

<template>
	<photo-row :rowHeight="height" :rowPadding="padding">
		<photo-grid 
			v-for="(data, i) in photos"
			@click="$emit('itemClick', data, i)"  
			:key="i"
			:photostyle="i == randId ? photoStyle : {}"
		>
			<template v-if="showNumOfRemainingPhotos && remainingNum > 0 && data.id === (layoutNum - 1)">
				<photo-mask></photo-mask>
				<view-more>
					<num-of-remaining>{{remainingNum}}</num-of-remaining>
				</view-more>
			</template>
			<photo-thumb :thumb="data.source"></photo-thumb>
		</photo-grid>
	</photo-row>
</template>