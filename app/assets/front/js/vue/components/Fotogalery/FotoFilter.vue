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
				choice: 0,
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
			<i class="fa-solid fa-filter mr-2"></i>Filter
		</button>
		<div class="dropdown-menu dropdown-menu-right">
			<button class="dropdown-item" type="button" @click="change(1)">
				<i 
					class="fa-regular"
					:class="choice == 1 ? 'fa-square-check' : 'fa-square'"
				></i> Všetky
			</button>
			<button class="dropdown-item disabled" disabled type="button" @click="change(2)">
				<i 
					class="fa-regular"
					:class="choice == 2 ? 'fa-square-check' : 'fa-square'"
				></i> Len na sklade
			</button>
		</div>
	</div>
</template>

<style scoped>
button.disabled {
	color: #888 !important; 
}
</style>