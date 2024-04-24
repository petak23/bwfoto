<script>
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
//export default {}
</script>

<template>
	<div class="btn-group user-lang-menu" role="group" aria-label="User-lang-menu">
		<a 
			:href="$store.state.userLogLink" 
			:title="$store.state.user.name" 
			role="button" 
			class="btn btn-outline-info btn-sm" 
		>
			{{ $store.state.user.name }}
		</a>
		<a 
			v-if="$store.state.adminLink != null"
			:href="$store.state.adminLink" 
			:title="$store.state.texts.base_AdminLink_name" 
			role="button" 
			class="btn btn-outline-info btn-sm" 
		>
			{{ $store.state.texts.base_AdminLink_name }}
		</a>
		<a 
			v-if="$store.state.adminerLink != null"
			:href="$store.state.adminerLink" 
			role="button" 
			target="_blank"
			class="btn btn-outline-secondary btn-sm" 
		>
			<i class="fas fa-database"></i>
		</a>
		<a 
			v-if="$store.state.logOutLink != null"
			:href="$store.state.logOutLink" 
			:title="$store.state.texts.log_out" 
			role="button" 
			class="btn btn-outline-warning btn-sm" 
		>
			<i class="fas fa-sign-out-alt pr-1"></i>
			{{ $store.state.texts.log_out }}
		</a>
	</div>
</template>