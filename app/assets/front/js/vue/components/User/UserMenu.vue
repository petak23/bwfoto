<script setup>
/**
 * Komponenta pre užívateľkú ponuku.
 * Posledna zmena 03.12.2024
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.3
 */

import UserMenu_not_logged from "./UserMenu_not_logged"
import UserMenu_logged_in from "./UserMenu_logged_in"

import { useMainStore } from '../../store/main'
const store = useMainStore()

</script>

<template>
	<div>
		<UserMenu_logged_in v-if="store.user != null" />
		<UserMenu_not_logged v-else />
	</div>
</template>