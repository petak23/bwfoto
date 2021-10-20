<script>
/** 
 * Component Menu_order
 * Posledná zmena(last change): 14.10.2021
 *
 * @author Ing. Peter VOJTECH ml <petak23@gmail.com>
 * @copyright Copyright (c) 2021 - 2021 Ing. Peter VOJTECH ml.
 * @license
 * @link http://petak23.echo-msz.eu
 * @version 1.0.0
 * @see https://codepen.io/NathanWailes/pen/rNLajYO?editors=1000
 */

export default {
  //vuetify: new Vuetify(),
  data () {
    return {
      headers: [
        {
          text: 'Dessert',
          align: 'start',
          sortable: false,
          value: 'name',
        },
      ],
      desserts: [
        {
          name: 'Frozen Yogurt',
        },
        {
          name: 'Ice cream sandwich',
        },
        {
          name: 'Eclair',
        },
        {
          name: 'Cupcake',
        },
        {
          name: 'Gingerbread',
        },
      ],
    }
  },
  methods: {
    saveOrder (event) {
      const movedItem = this.desserts.splice(event.oldIndex, 1)[0];
      this.desserts.splice(event.newIndex, 0, movedItem);
    }
  },
  directives: {
    sortableDataTable: {
      bind (el, binding, vnode) {
        const options = {
          animation: 150,
          onUpdate: function (event) {
            vnode.child.$emit('sorted', event)
          }
        }
        Sortable.create(el.getElementsByTagName('tbody')[0], options)
      }
    }
  },
}