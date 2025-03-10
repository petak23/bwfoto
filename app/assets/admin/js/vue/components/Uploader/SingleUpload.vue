<script setup>
import { ref } from 'vue'
import { Axios } from 'axios'
import { BProgress, BImg, BFormRadioGroup  } from 'bootstrap-vue-next'

const props = defineProps({
	apiUrl: {
		type: String,
		required: true,
	},
	basePath: {
		type: String,
		required: true,
	},
	id: {
		type: String,
		required: true,
	},
	id_hlavne_menu: {
		type: String,
		required: true,
	},
	backLink: String,
})

const uploadValue = ref(0)
const max = ref(100)
const file = ref(null)
const isUploading = ref(false)
const uploadedImage = ref(null)
const selected = ref('1')
const options =[
	{ text: 'Iné', value: '1' },
	{ text: 'Obrázok', value: '2' },
	{ text: 'Video', value: '3'},
	{ text: 'Audio', value: '4' }
]

const imageInserted = () => {
	// TODO $refs
	file.value = this.$refs.imageUploader.files[0];
	uploadImageMethod();
	// TODO $refs
	$refs.imageUploader.value = null;
}

const uploadImageMethod = async () => {
	let formData = new FormData();
	formData.append("priloha", file.value);
	formData.append("type", selected.value);
	//for(var pair of formData.entries()) {
	//  console.log(pair[0], pair[1]);
	//}
	isUploading.value = true;
	let odkaz = props.basePath + "/" + props.apiUrl + "/" + props.id_hlavne_menu
	//let _this = this
	await Axios.post(odkaz, formData, {
		headers: {
			"Content-Type": "multipart/form-data",
		},
		onUploadProgress: ({ total, loaded }) => {
			uploadValue.value = (loaded / total).toFixed(2) * 100;
		},
	})
	.then(response => {
		//console.log(response.data.data)
		uploadedImage.value = response.data.data.main_file
		file.value = null
		isUploading.value = false
	})
}

	/*mounted () {
		const url = "" //window.location.origin;
		API.defaults.baseURL = url + this.path
	},*/
</script>

<template>
	<div class="single-upload">
		<BFormRadioGroup 
			label="Typ prílohy:"
			:aria-describedby="ariaDescribedby"
			button-variant="outline-primary"
			name="file-type"
			buttons
			v-model="selected"
      :options="options"
		/>
		<div class="d-flex justify-content-around">
			<div class="upload-container d-flex justify-content-center align-items-center">
				<input
					:id="props.id"
					type="file"
					ref="imageUploader"
					class="input-uploader"
					@change="imageInserted"
				/>
				<div class="uploader-icon"><i class="fa-solid fa-upload fa-2x"></i></div>
			</div>
			<div class="flex-grow-1 d-flex flex-column px-3 justify-content-around">
				<BProgress v-if="isUploading" :value="uploadValue" :max="max" show-progress animated />
				<BImg
					v-if="uploadedImage"
					class="mt-1"
					height="80px"
					width="80px"
					:src="props.basePath + uploadedImage"
					thumbnail
					fluid
					alt="Uploaded image"
				/>
			</div>
		</div>
	</div>
</template>


<style lang="scss" scoped>
.single-upload {
	.upload-container {
		height: 100px !important;
		width: 100px !important;
		border: 2px dashed #ccc;
		border-radius: 5px !important;
	}
	.upload-container:hover {
			background-color: rgb(175, 245, 169) !important;
	}

	.input-uploader {
		opacity: 0;
		position: absolute;
		width: 100px !important;
		height: 100px !important;
	}

	.uploader-icon {
		width: 35px !important;
		height: 35px !important;
		color: #28a745;
	}
}
</style>