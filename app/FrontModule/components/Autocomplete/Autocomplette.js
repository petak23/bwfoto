import Vue from 'vue';
import axios from 'axios';
import autocomplete from 'autocomplete';

//for Tracy Debug Bar
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

var vm = new Vue({
    data: function () {
        return {
            handle: null,
            param: null,
        }
    },
    components: {
		//our component
        autocomplete
    },
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
}).$mount('#autocomplete');