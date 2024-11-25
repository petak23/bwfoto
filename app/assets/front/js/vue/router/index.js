import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import LogInView from '../views/LogInView.vue'
import MainArticleView from '../views/MainArticleView.vue'
import ProductsLikeView from '../views/ProductsLikeView.vue'
import UserView from '../views/UserView.vue'
import { useMainStore } from '../store/main'
import { useFlashStore } from '../store/flash'

const routes = [
	{
		path: '/',
		//redirect: '/clanky/home'
		name: 'Domovská stránka',
		component: HomeView,
	},
	{
		path: '/login',
		name: 'Prihlásenie',
		component: LogInView
	},
	{
		path: '/registration',
		name: 'Registrácia',
		component: LogInView,
		props: { viewRegistrationForm: 'registration' }
	},
	{
		path: '/resetpassword/:id/:new_password_key',
		name: 'Reset hesla',
		component: LogInView,
		props: { viewRegistrationForm: 'resetPassword' }
	},
	{ 
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