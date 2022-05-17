<template>
  <transition name="fade" v-if="visible">
    <div class="alert alert-dismissible fade show" 
					v-for="(m, index) in mess" :key="index"	
					:class="'alert-'+m.type"
					>


			{{ m.message }}

      <button type="button" class="close" @click="hide">
        <span>&times;</span>
      </button>
    </div>
  </transition>
</template>

<script lang="js">
export default {
  props: [
    'text',
    'flashMessages'
  ],

	data() {
		return {
			visible: false,
			message: '',
      mess: [],
		}
	},

	created() {
		if (this.text) {
			this.message = this.text
			this.show()
		}
    if (this.flashMessages.length > 3) {
      this.mess = JSON.parse(this.flashMessages)
      this.show()
    }

		this.$root.$on('flash', message => {
			this.message = message
			this.show()
		})
	},

	methods: {
		show() {
			this.visible = true
			setTimeout(() => this.hide(), 5000)
		},
		hide() {
			this.visible = false
		}
	}
}
</script>

<style lang="scss" scoped>
.alert {
  font-size: 0.9rem;
	position: fixed;
	right: 2em;
	top: 2em;
  max-width: 50vw;
  z-index: 2000;
}

.fade-enter-active,
.fade-leave-active {
	transition: opacity 0.5s;
}
.fade-enter,
.fade-leave-to {
	opacity: 0;
}
</style>