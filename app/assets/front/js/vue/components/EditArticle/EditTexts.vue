<script>
/** 
 * Component Fotocollage
 * Posledná zmena(last change): 30.03.2022
 *
 * @author Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @copyright Copyright (c) 2021 - 2022 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.0.2
 * 
 */
import axios from 'axios'
import _ from 'lodash'

//for Tracy Debug Bar
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

export default {
  props: {
    id_hlavne_menu: String,
    basepath: String,
    title: String,
    edit_enabled: String,
    link: String,
  },
  data() {
    return {
      textin: '',
      preview: '',
      show: true
    }
  },
  methods: {
    onSubmit(event) {
      event.preventDefault()
      let odkaz = this.basepath + 'api/menu/texylasave/' + this.id_hlavne_menu
      let vm = this
      axios.post(odkaz, {
          texy: this.textin,
        })
        .then(function (response) {
          //vm.preview = response.data
          //console.log(response.data)
          // https://stackoverflow.com/questions/35664550/vue-js-redirection-to-another-page
          window.location.href = vm.link;
        })
        .catch(function (error) {
          console.log(odkaz)
          console.log(error)
        });      
    },
    onCancel(event) {
      event.preventDefault()
      window.location.href = this.link;
    },
    insertB() {
      this.insertSomething("**","**")
    },
    insertI() {
      this.insertSomething("//","//")
    },
    insertH(id=3) {
      let h = {
        3: "***********",
        4: "===========",
        5: "-----------"
      }
      id = (id > 5 || id < 3) ? 3 : id
      this.insertSomething("", "\n" + h[id] + "\n")
    },
    insertSomething(valueBefore, valueAfter = null) {
        let textArea = document.getElementsByName('texyla')[0];
        let startPos = textArea.selectionStart,
            // get cursor's position:
            endPos = textArea.selectionEnd,
            cursorPos = startPos, 
            tmpStr = textArea.value;
        if (valueBefore === null) {
            return;
        }

        // insert:
        let tst = tmpStr.substring(0, startPos) + valueBefore
        if (valueAfter !== null) {
          if (startPos < endPos) {
            tst += tmpStr.substring(startPos, endPos)
          }
          tst += valueAfter
        }
        tst += tmpStr.substring(endPos, tmpStr.length)
        this.textin = tst

        // move cursor:
        setTimeout(() => {
            cursorPos += valueBefore.length;
            textArea.selectionStart = textArea.selectionEnd = cursorPos;
        }, 10);
    },
    getPreview: function () {
      let odkaz = this.basepath + 'api/menu/texylapreview'
      //this.preview = "Vytváram náhľad..."
      let vm = this
      axios.post(odkaz, {
          texy: this.textin,
        })
        .then(function (response) {
          vm.preview = response.data
          //console.log(response.data)
        })
        .catch(function (error) {
          console.log(odkaz)
          console.log(error)
        });
    }
  },
  watch: {
    textin: function (newTextin, oldTextin) {
      this.debouncedGetPreview()
    },
  },
  created: function () {
    let odkaz = this.basepath + 'api/menu/getonemenuarticle/' + this.id_hlavne_menu
    let tm = this
    axios.get(odkaz)
      .then(response => {
        tm.textin = response.data.text
      })
      .catch((error) => {
        console.log(odkaz);
        console.log(error);
      });
    // https://v2.vuejs.org/v2/guide/computed.html?redirect=true#Watchers
    this.debouncedGetPreview = _.debounce(this.getPreview, 1000)
  },
}
</script>

<template>
  <div>
    <b-form @submit="onSubmit" @reset="onCancel" v-if="show">
      <!-- b-form-group id="input-group-2" label="Text:" label-for="text" -->
        <b-button-toolbar key-nav aria-label="Application toolbar">
          <b-button-group size="sm" class="mx-1">
            <b-button variant="outline-info" @click="insertB" title="Bold">
              <i class="fa-solid fa-bold"></i>
            </b-button>
            <b-button variant="outline-info" @click="insertI" title="Italic">
              <i class="fa-solid fa-italic"></i>
            </b-button>
          </b-button-group>
          <b-button-group size="sm" class="mx-1">
            <b-button variant="outline-info" @click="insertH(3)" title="Nadpis H3">
              <i class="fa-solid fa-h"></i>3
            </b-button>
            <b-button variant="outline-info" @click="insertH(4)" title="Nadpis H4">
              <i class="fa-solid fa-h"></i>4
            </b-button>
            <b-button variant="outline-info" @click="insertH(5)" title="Nadpis H5">
              <i class="fa-solid fa-h"></i>5
            </b-button>
          </b-button-group>
          <b-button-group size="sm" class="mx-1">
            <b-button variant="outline-info">
              <i class="fa-solid fa-ruler-horizontal"></i>
            </b-button>
            <b-button variant="outline-info">
              <i class="fa-solid fa-list-ul"></i>
            </b-button>
            <b-button variant="outline-info">
              <i class="fa-solid fa-image"></i>
            </b-button>
          </b-button-group>
        </b-button-toolbar>
      
        <b-form-textarea
          id="text"
          v-model="textin" debounce="500"
          placeholder="Zadajte text..."
          rows="6"
          max-rows="6"
          description="Pre formátovanie využite syntax texy."
          name="texyla"
        ></b-form-textarea>
      <!-- /b-form-group -->

      <b-button type="submit" variant="primary">Ulož</b-button>
      <b-button type="reset" variant="secondary">Cancel</b-button>
    </b-form>
    <div class="mt-2 text-white">Náhľad<small>(aktualizuje sa cca. 1x za sekundu)</small>:</div>
    <b-card class="text-dark text-left" v-html="preview"></b-card>
  </div>
</template>

<style>

</style>