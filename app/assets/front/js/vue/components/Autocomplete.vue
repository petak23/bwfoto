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
    choice: {
      type: String,
      required: true,
    },
    myred: String,
    placeholder: String,
    inputname: String
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
                  this.results = [];
                  response.data.forEach(cl => this.results.push(cl))
                  this.isSearching = false;     
             })
             .catch((error) => {
                console.log(error);
             });
      }
    },
    setLink(id) {
      return this.myred + '/' + id;
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
              :placeholder="placeholder"
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
              Hľadám...
            </span>
            <span v-show="searchquery.length < 3"> &nbsp; </span>
          </li>
          <li class="list-group-item"
              v-for="(result, i) in results"
              :key="result.id"
              :class="{ 'is-active': i === arrowCounter }"
          >
            <a :href="setLink(result.id)" :title="result[choice]" @click="onAClick"> {{ result[choice] }} </a>
          </li>
        </ul>
      </div>
    </form>
  </div>
</template>