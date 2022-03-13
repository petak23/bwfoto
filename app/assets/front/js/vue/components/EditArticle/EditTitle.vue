<script>
export default {
  props: {
    articlename: String,
    title: String,
  },
  data() {
    return {
      show: true
    }
  },
  methods: {
    /*onSubmit(event) {
      event.preventDefault()
      alert(JSON.stringify(this.form))
    },
    onReset(event) {
      event.preventDefault()
      // Reset our form values
      this.form.email = ''
      this.form.name = ''
      this.form.food = null
      this.form.checked = []
      // Trick to reset/clear native browser form validation state
      this.show = false
      this.$nextTick(() => {
        this.show = true
      })
    }*/
  }
}
</script>

<template>
  <span>
    <b-button 
      v-b-modal.modal-1
      class="btn btn-sm btn-primary"
      :title="title"
    >
      <i class="fas fa-pen"></i>
    </b-button>

    <b-modal id="modal-1" centered 
      :title="title + ': ' + articlename" 
      header-bg-variant="dark"
      header-text-variant="light"
      body-bg-variant="dark"
      body-text-variant="light"
      footer-bg-variant="dark"
      footer-text-variant="light"  
    >
      <!--b-form @submit="onSubmit" @reset="onReset" v-if="show">
        <b-form-group
          id="input-group-1"
          label="Názov zobrazený v nadpise pre jazyk Slovenčina:"
          label-for="sk_view_name"
          description="We'll never share your email with anyone else."
        >
          <b-form-input
            id="sk_view_name"
            v-model="form.email"
            type="email"
            placeholder="Enter email"
            required
          ></b-form-input>
        </b-form-group>
      </b-form -->
    </b-modal>
  </span>
</template>

<style>

</style>