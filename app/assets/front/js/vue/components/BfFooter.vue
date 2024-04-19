<script>
import BwfotoTreeMain from './Menu/BWfoto_Tree_Main'
import UserMenu from './User/UserMenu'

export default {
	components: {
		BwfotoTreeMain,
		UserMenu
	},
	props: {
		dirToImages: {
			type: String,
			required: true
		},
		copy: {
			type: String,
			default: "",
		},
		lastUpdate: {
			type: String,
			default: "",
		},
	},
	data() {
		return {
			base_img: null,
		}
	},
	watch: {
		'$store.state.basePath': function () {
			this.base_img = this.$store.state.basePath + '/' + this.dirToImages
		}
	},
}
</script>

<template>
	<footer class="py-3">
		<!-- --- Mapa stranky --- --> 
		<nav class="w-100">
			<bwfoto-tree-main 
				part="-1" 
				ul-class="nav text-center justify-content-center py-3"
			/>
		</nav>
		<!-- --- Info o stranke --- -->
		<div class="row justify-content-center" id="footerContent">
			<div id="logoBWfoto" class="px-3 my-3 col-md">
				<img 
					v-if="base_img != null"
					:src="base_img + 'logo_bw-w.png'"
					title="Logo BWfoto" alt="Logo Bwfoto" class="img-responsive">
			</div>
			<div id="contact" class="px-3 my-3 col-md">
				<h4>Ateliér Zámečník</h4>
				<ul class="list-group">
					<li class="list-unstyled"><span class="fa fa-home"></span> Spišské Bystré</li>
					<li class="list-unstyled"><span class="fa fa-envelope"></span> bwfoto@bwfoto.sk</li>
				</ul>
			</div>
		</div>
		<div class="pv-footer info-layer my-3">
			<ul class="nav justify-content-center">
				<li class="p-2" v-if="copy.length > 0">{{ copy }}</li>
				<li class="p-2">
					<a href="https://nette.org/cs/" class="logo-nette" title="nette powered">
						<img v-if="base_img != null" :src="base_img + 'nette-powered1.gif'" alt="nette powered"	/>
					</a>
					&nbsp;
					<a href="https://vuejs.org/" class="logo-nette" title="Vue js" target="_blank">
						<img v-if="base_img != null" :src="base_img + 'logo_vue.png'" alt="vue powered" class="vue-logo" />
					</a>
				</li>
				<li class="p-2">{{ lastUpdate }}</li>
				<li class="p-2">created by <a href="http://petak23.echo-msz.eu/" title="petak23.echo-msz.eu" target="_blank">petak23</a></li>
			</ul>
		</div> 
		<div class="my-3 text-center">
			<user-menu />
		</div>
	</footer>
</template>