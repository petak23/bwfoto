<script>
import axios from 'axios'

export default {
	data() {
		return {
			langs: null,
		}
	},
	methods: {
		getLangs() {
			let odkaz = this.$store.state.apiPath + 'lang/getakclangs'
			axios.get(odkaz)
				.then(response => {
					//console.log(response.data)
					this.langs = response.data
				})
				.catch((error) => {
					console.log(odkaz);
					console.log(error);
				});
		},
	},
	mounted () {
		this.getLangs();
	},
}
</script>

<template>
	<div  
		class="btn-group user-lang-menu" role="group" aria-label="Lang-menu" 
		v-if="langs !== null && langs.count" 
	>
		<a
			v-for="lang in langs.langs" :key="lang.id" 
			:href="lang.link" 
			:title="lang.title" role="button" 
			:aria-label="lang.name"
			class="btn"
			:class="lang.class.length ? lang.class : ''"
		>
			<img :src="lang.image.src" :alt="lang.image.alt" />
		</a>
	</div>
</template>


<style lang="sass" scoped>

</style>