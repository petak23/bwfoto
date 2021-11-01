<script>
import axios from 'axios';

//for Tracy Debug Bar
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

export default {
  props: {
    source: {
      type: String,
      required: true,
    },
    links: {
      type: String,
      required: true,
    },
    //myred: String,
    //placeholder: String,
    inputname: String,
    texts: {
      type: String,
      required: true,
    },
  },
  data: function () {
    return {
      searchquery: '',
      results: [],
      isOpen: false,
      isSearching: true,
      arrowCounter: -1,
    }
  },
  computed: {
    // Parsovanie JSON-u  na array
    mylinks() {
      return JSON.parse(this.links)
    },
    mytexts() {
      return JSON.parse(this.texts)
    }
  },
  methods: {
    autoComplete() {
      this.$emit('autocomplete-start');
      this.results = [];
      if (this.searchquery.length > 0) {
        this.isOpen = true;
        this.isSearching = true;
      }
      if (this.searchquery.length > 2) {
        axios.get(this.source, {params: {[this.inputname]: this.searchquery}})
              .then(response => {
                //console.log(response);
                this.results = [];
                response.data.forEach(cl => this.results.push(cl))
                this.isSearching = false; 
                //console.log(this.results);    
              })
              .catch((error) => {
                console.log(error);
              });
      }
    },
    setLink(result) {
      if (result.type == 1) {
        return this.mylinks[1] + result.id;
      } else if (result.type == 2) {
        return this.mylinks[2] + result.id + '?first_id='+result.id_dokument;
      }
    },
    onArrowDown() {
    //    if (this.arrowCounter < this.results.length - 1) {
    //        this.arrowCounter = this.arrowCounter + 1;
    //    }
    },
    onArrowUp() {
    //   if (this.arrowCounter > 0) {
    //        this.arrowCounter = this.arrowCounter - 1;
    //    }
    },
    onEnter() {
    //    this.setResult(this.results[this.arrowCounter]);
    //    this.arrowCounter = -1;
    },
    onAClick() {
      return true;
    },
    handleClickOutside(evt) {
      if (!this.$el.contains(evt.target)) {
        this.isOpen = false;
        this.arrowCounter = -1;
        this.searchquery = '';
      }
    }
  },
  mounted() {
    document.addEventListener('click', this.handleClickOutside)
  },
  destroyed() {
    document.removeEventListener('click', this.handleClickOutside)
  }
}
</script>

<template>
  <div class="autocomplete">
    <form autocomplete="off" class="my-2 my-lg-0" @submit.prevent><!--required for disable google chrome auto fill-->
      <input  type="search" 
              :placeholder="mytexts.placeholder"
              :name="inputname"
              class="form-control mr-sm-2"
              aria-label="Search"
              v-model="searchquery"
              @input="autoComplete"
              @keydown.down="onArrowDown"
              @keydown.up="onArrowUp"
              @keydown.enter="onEnter"
      >
      <div class="autocomplete-result" v-show="isOpen">
        <ul class="list-group">
          <li class="list-group-item text-secondary" v-show="isSearching">
            <span v-show="searchquery.length > 2">
              <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
              {{ mytexts.searching }}
            </span>
            <span v-show="searchquery.length < 3">{{ mytexts.min_char }}</span>
          </li>
          <li class="list-group-item"
              v-for="(result, i) in results"
              :key="i"
              :class="{ 'is-active': i === arrowCounter }"
          >
            <a :href="setLink(result)" :title="result.name" @click="onAClick"> 
              {{ result.name }} 
              <div class="small" v-if="result.description != ''"><span v-html="result.description"></span></div>
            </a>
          </li>
          <li class="list-group-item text-warning" v-show="!isSearching && searchquery.length > 2 && results.length == 0">
            <span>{{ mytexts.not_found }}</span>
          </li>
        </ul>
      </div>
    </form>
  </div>
</template>