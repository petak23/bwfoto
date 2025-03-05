import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import VerzieView from '../views/VerzieView.vue'
import SliderView from '../views/Sliderview.vue'
import { useMainStore } from '../store/main.js'
import { useFlashStore } from '../../../../components/FlashMessages/store/flash'

const routes = [
	{
		path: '/administration/',
		name: 'Domovská stránka',
		component: HomeView,
	},
	{
		path: '/administration/verzie',
		name: 'Výpis verzií',
		component: VerzieView,
	},
	{
		path: '/administration/slider',
		name: 'Výpis slider-a',
		component: SliderView,
	},
	/*{ 
		path: '/clanky/:id/:first_id?',
		component: MainArticleView 
	},
	{
		path: '/units',
		name: 'Jednotky',
		component: UnitsView
		// route level code-splitting
		// this generates a separate chunk (About.[hash].js) for this route
		// which is lazy-loaded when the route is visited.
		//component: () => import('../views/UnitsView.vue')
	}*/
]

const basePath = document.getElementById('app').dataset.basePath

const router = createRouter({
	history: createWebHistory(basePath.substring(1)),
	routes
})

router.beforeEach((to) => {
	const store = useMainStore()
	const storeF = useFlashStore()
})

export default router