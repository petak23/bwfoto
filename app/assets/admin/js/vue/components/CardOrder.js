//import draggable from "@/vuedraggable";

/**
 * Komponenta pre zmenu poradia.
 * Posledna zmena 25.10.2021
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2021 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.0
 * @link https://sortablejs.github.io/Vue.Draggable/#/transition-example-2
 */

Vue.component('cardorder', {
  components: {
    vuedraggable
  },
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
  data() {
    return {
      drag: false,
      items: [],
      nextchange: false // Aby sa pri prvom behu nespustilo ukladanie.
    };
  },
  computed: {
    dragOptions() {
      return {
        animation: 200,
        group: "description",
        disabled: false,
        ghostClass: "ghost"
      };
    },

  },
  watch: {
    items: function (newItem) { // Kedykolvek sa item zmení tak sa spustí
      if (this.nextchange) {
        //console.log(newItem)
        // TODO - ukladanie do DB
      } else {
        this.nextchange = true
      }
    }
  },
  mounted() {
    // TODO - Priame načítanie z DB cez api
    this.items = JSON.parse(this.articles)
  },
  template: `
    <div class="row">
      <div class="col-6">
        <h3 class="p-2">Zoznam podčlánkov danej časti:</h3>
        <draggable
          class="list-group"
          tag="ul"
          v-model="items"
          v-bind="dragOptions"
          @start="drag = true"
          @end="drag = false"
        >
          <transition-group type="transition" :name="!drag ? 'flip-list' : null">
            <li
              class="list-group-item"
              v-for="item in items"
              :key="item.order"
            >
              <a  :href="item.link" 
                  :title="item.name">
                {{ item.name }}
                <img  v-if="item.avatar != null"
                      :src="basepath + '/files/menu/' + item.avatar" 
                      :title="item.name" 
                      style="max-height: 5rem;"
                />
              </a> 
            </li>
          </transition-group>
        </draggable>
      </div>
    </div>
  `
});

/*<style>
.button {
  margin-top: 35px;
}

.flip-list-move {
  transition: transform 0.5s;
}

.no-move {
  transition: transform 0s;
}

.ghost {
  opacity: 0.5;
  background: #c8ebfb;
}

.list-group {
  min-height: 20px;
}

.list-group-item {
  cursor: move;
}

.list-group-item i {
  cursor: pointer;
}
</style>*/