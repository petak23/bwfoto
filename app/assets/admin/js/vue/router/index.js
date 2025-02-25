import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import VerzieView from '../views/VerzieView.vue'
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
	/*{ 
		path: '/clanky/:id/:first_id?',
		component: MainArticleView 
	},
	{ 
		path: '/productlike',
		component: ProductsLikeView
	},
	{
		path: '/clanky/home',
		redirect: '/'
	},
	{
		path: '/user',
		name: 'Užívateľ',
		component: UserView
	},
	{
		path: '/basket',
		name: 'Košík',
		component: BasketView
	},*/
	/*
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