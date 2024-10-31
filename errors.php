<?php

function pay_display_theme_warning() {

	$message = __( 'AppThemes Pay2Post can not run with this theme.', PAY_TD );
	echo '<div class="error fade"><p>' . $message . '</p></div>';
	deactivate_plugins( plugin_basename( dirname( __FILE__ ) . '/pay-to-post.php' ) );

}
