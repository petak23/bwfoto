Vue.component('autocomplete', {
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
          arrowCounter: -1,
      }
  },
  template: `
    <div class="autocomplete">
      <form autocomplete="off"><!--required for disable google chrome auto fill-->
        <input  type="text"
                :placeholder="placeholder"
                :name="inputname"
                class="form-control"
                v-model="searchquery"
                @input="autoComplete"
                @keydown.down="onArrowDown"
                @keydown.up="onArrowUp"
                @keydown.enter="onEnter"
        >
        <div class="autocomplete-result" v-show="isOpen">
          <ul class="list-group">
            <li class="list-group-item"
                v-for="(result, i) in results"
                :key="result.id"
                :class="{ 'is-active': i === arrowCounter }"
            >
                <a :href="setLink(result.id)" :title="result[choice]"> {{ result[choice] }} </a>
            </li>
          </ul>
        </div>
      </form>
    </div>
  `,
  methods: {
      autoComplete() {
          this.$emit('autocomplete-start');
          this.results = [];
          if (this.searchquery.length > 2) {
              axios.get(this.source, {params: {[this.inputname]: this.searchquery}})
                   .then(response => {
                        console.log(response.data);
                        response.data.forEach(cl => this.results.push(cl));
                        console.log(this.results);
                        this.isOpen = true;      
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
          if (this.arrowCounter < this.results.length - 1) {
              this.arrowCounter = this.arrowCounter + 1;
          }
      },
      onArrowUp() {
          if (this.arrowCounter > 0) {
              this.arrowCounter = this.arrowCounter - 1;
          }
      },
      onEnter() {
          this.setResult(this.results[this.arrowCounter]);
          this.arrowCounter = -1;
      },
      handleClickOutside(evt) {
          if (!this.$el.contains(evt.target)) {
              this.isOpen = false;
              this.arrowCounter = -1;
          }
      }
  },
  mounted() {
      document.addEventListener('click', this.handleClickOutside);
  },
  destroyed() {
      document.removeEventListener('click', this.handleClickOutside);
  }
});


//for Tracy Debug Bar
//axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

var vm = new Vue({
    el: '#autocomplete',
    data: function () {
        return {
            handle: null,
            param: null
        }
    },
//    components: {
//		//our component
//        autocomplete
//    },
    methods: {
        onSelect(item) {
            //is emitted from child component and call nette handleAdd
            axios.get(this.handle + '&' + this.param + '=' + item.id)
                 .then( (response) => {
                    if (response.data.snippets) {
                        this.updates(response.data.snippets);
                    }
                 })

        },
        updates(snippets) {
            this.$refs.autocomplete.searchquery = '';

            Object.keys(snippets).forEach((id) => {
                const el = document.getElementById(id);
                if (el) {
                    this.updateSnippet(el, snippets[id]);
                }
            });

 			//maybe reinit nette.ajax.js for new ajax call
            //$.nette.load();
        },
        updateSnippet(el, content) {
            el.innerHTML = content;
        }
    },

    mounted: function () {
        // `this` points to the vm instance
        this.handle = this.$el.getAttribute('data-handle-link');
        this.param = this.$el.getAttribute('data-handle-param');
    }
});//.$mount('#autocomplete'); 