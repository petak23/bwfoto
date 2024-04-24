<script>
/**
 * Komponenta pre užívateľkú ponuku.
 * Posledna zmena 19.04.2024
 * 
 * @author     Ing. Peter VOJTECH ml. <petak23@gmail.com>
 * @copyright  Copyright (c) 2012 - 2024 Ing. Peter VOJTECH ml.
 * @license
 * @link       http://petak23.echo-msz.eu
 * @version    1.0.2
 */

import UserMenu_not_logged from "./UserMenu_not_logged";
import UserMenu_logged_in from "./UserMenu_logged_in";

export default {
	components: {
		UserMenu_logged_in,
		UserMenu_not_logged,
	}
}
</script>

<template>
	<div>
		<user-menu_logged_in v-if="$store.state.user != null" />
		<user-menu_not_logged v-else />
	</div>
</template>