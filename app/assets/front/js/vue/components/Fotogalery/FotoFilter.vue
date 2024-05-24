<script>
	export default {
		props: {
			val: {
				type: Number,
				default: 1
			},
		},
		data() {
			return {
				choice: 1,
				data: [{id: 1, txt: "Všetky"}, {id: 2, txt: "Len na sklade"}],
			}
		},
		methods: {
			change(id) {
				this.choice = id
				this.$emit('filter-change', this.choice)
			}
		},
		mounted () {
			this.choice = this.val
		},
	}
</script>

<template>
	<div class="btn-group">
		<button type="button" class="btn btn-outline-secondary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
			<i class="fa-solid fa-filter mr-2"></i>{{ data[choice - 1].txt }}
		</button>
		<div class="dropdown-menu dropdown-menu-right">
			<button 
				v-for="i in data"
				class="dropdown-item" 
				type="button" 
				@click="change(i.id)"
				:key="i.id"
			>
				<i 
					class="fa-regular"
					:class="choice == i.id ? 'fa-square-check' : 'fa-square'"
				></i> {{ i.txt }}
			</button>
		</div>
	</div>
</template>

<style scoped>
button.disabled {
	color: #888 !important; 
}
</style>