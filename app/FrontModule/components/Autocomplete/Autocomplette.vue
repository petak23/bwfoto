<template>
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
              @click="setResult(result)"
              :class="{ 'is-active': i === arrowCounter }"
          >
            {{ result[choice] }}
          </li>
        </ul>
      </div>
    </form>
  </div>
</template>

<style scoped>
  .autocomplete-result .is-active,
  .autocomplete-result li:hover {
    background-color: #379d2f;
    color: white;
  }
</style>

<script>
    import axios from 'axios';

    export default {
        name: "autocomplete",
        props: {
            source: {
                type: String,
                required: true,
            },
            choice: {
                type: String,
                required: true,
            },

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
        methods: {
            autoComplete() {
                this.$emit('autocomplete-start');
                this.results = [];
                if (this.searchquery.length > 2) {
                    axios.get(this.source, {params: {[this.inputname]: this.searchquery}})
                        .then(response => {
                            this.results = response.data.data;
                            this.isOpen = true;
                        })
                        .catch((error) => {
                            // console.log(error);
                        });
                }
            },
            setResult(item) {
                this.isOpen = false;
                this.searchquery = item[this.choice];
                this.product_id = item.id;
                this.$emit('selected', item);
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
            document.addEventListener('click', this.handleClickOutside)
        },
        destroyed() {
            document.removeEventListener('click', this.handleClickOutside)
        }
    }
</script>