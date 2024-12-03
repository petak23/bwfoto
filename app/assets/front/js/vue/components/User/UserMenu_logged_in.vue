<script setup>
/**
 * Komponenta pre užívateľkú ponuku - prihlásený užívateľ.
 * Posledna zmena 19.04.2024
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.7
 */
import { useMainStore } from '../../store/main'
const store = useMainStore()
</script>

<template>
	<div class="btn-group user-lang-menu" role="group" aria-label="User-lang-menu">
		<a 
			:href="store.userLogLink" 
			:title="store.user.name" 
			role="button" 
			class="btn btn-outline-info btn-sm" 
		>
			{{ store.user.name }}
		</a>
		<a 
			v-if="store.adminLink != null"
			:href="store.adminLink" 
			:title="store.texts.base_AdminLink_name" 
			role="button" 
			class="btn btn-outline-info btn-sm" 
		>
			{{ store.texts.base_AdminLink_name }}
		</a>
		<a 
			v-if="store.adminerLink != null"
			:href="store.adminerLink" 
			role="button" 
			target="_blank"
			class="btn btn-outline-secondary btn-sm" 
		>
			<i class="fas fa-database"></i>
		</a>
		<a 
			v-if="store.logOutLink != null"
			:href="store.logOutLink" 
			:title="store.texts.log_out" 
			role="button" 
			class="btn btn-outline-warning btn-sm" 
		>
			<i class="fas fa-sign-out-alt pr-1"></i>
			{{ store.texts.log_out }}
		</a>
	</div>
</template>