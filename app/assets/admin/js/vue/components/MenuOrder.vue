<script>
import vuetify from '@/admin/js/vue/plugins/vuetify'

export default {
  vuetify,
  props: {
    articles: {
      type: String,
      required: true
    },
    basepath: {
      type: String,
      required: true
    }
  },
  data: function () {
    return {
      headers: [
        { text: 'Id',  value: 'id' },
        { text: 'Avatar', value: 'avatar' },
        { text: 'Name', value: 'name' },
      ]
    }
  },
  computed: {
    // Parsovanie JSON-u  na array
    menuitems() {
      return JSON.parse(this.articles)
    },
  }
};
</script>

<template>
  <v-data-table
      :headers="headers"
      :items="menuitems"
      hide-default-header
      hide-default-footer
      class="elevation-1"
    >
    
      <template v-slot:top>
        <div class="text-center p-2">Zoznam podčlánkov danej časti:</div>
      </template>

      <template v-slot:item.avatar="{ item }">
        <a  v-if="item.avatar != null"
            :href="item.link" 
            :title="item.name">
          <img   
                :src="basepath + '/files/menu/' + item.avatar" 
                :title="item.name" 
                style="max-height: 5rem;"
          />
        </a> 
      </template>

      <template v-slot:item.name="{ item }">
        <a :href="item.link" :title="item.name">
          {{ item.name }}
        </a> 
      </template>

    </v-data-table>
</template>