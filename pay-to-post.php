<?php

/*
Plugin Name: AppThemes Pay2Post
Plugin URI: http://appthemes.com
Description: Allows for users to post via payment through the frontend
Author: AppThemes
Version: 0.5
Author URI: http://appthemes.com
*/

define( 'PAY_TD', 'app-pay' );

require dirname( __FILE__ ) . '/errors.php';
$current_theme = wp_get_theme();
if ( get_template_directory() !== get_stylesheet_directory() )
	$current_theme = wp_get_theme( $current_theme->Template );
$theme_names = array( 'ClassiPress', 'Clipper', 'JobRoller', 'Vantage' );
if( in_array( $current_theme->Name, $theme_names ) ){
	add_action( 'admin_notices', 'pay_display_theme_warning' );
	return;
}

define( 'APP_FRAMEWORK_URI', plugins_url( '/lib/framework',  __FILE__ ) );
define( 'PAY_ITEM_REGULAR', 'regular' );
if( !defined( 'APP_TD' ) )
	define( 'APP_TD', 'app-pay' );

require dirname( __FILE__ ) . '/lib/framework/load.php';
require dirname( __FILE__ ) . '/lib/payments/load.php';

require_once 'functions.php';

if ( is_admin() ) {
    require_once 'admin/settings.php';
}

require_once 'add-post.php';
require_once 'support.php';
require_once 'order-summary.php';

register_activation_hook( __FILE__, 'pay_install' );
register_deactivation_hook( __FILE__, 'pay_uninstall' );

add_action( 'init', 'pay_setup' );
add_action( 'init', 'pay_load_textdomain' );
add_action( 'init', 'pay_add_payment_post_types' );
add_action( 'plugins_loaded', 'pay_options_setup' );
add_action( 'wp_enqueue_scripts', 'pay_enqueue_scripts' );

function pay_setup() {


}

function pay_options_setup() {
	global $pay_options;
	require dirname( __FILE__ ) . '/admin/options.php';

	add_theme_support( 'app-payments', array(
		'items' => array(
			array(
				'type' => PAY_ITEM_REGULAR,
				'title' => __( 'Regular Post', PAY_TD ),
				'meta' => array(
					'price' => $pay_options->price_per_post,
				)
			),
		),
		'items_post_types' => array( 'post' ),
		'images_url' => plugins_url( 'lib/payments/images/', __FILE__ ),
		'options' => $pay_options,
	) );

}

function pay_install() {
	global $wpdb;

	require_once dirname( __FILE__ ) . '/lib/framework/load-p2p.php';
	P2P_Storage::install();

	flush_rewrite_rules( false );
	do_action('appthemes_first_run');
}

function pay_uninstall() {

}

function pay_enqueue_scripts() {
	$path = plugins_url( '', __FILE__ );
	wp_enqueue_style( 'pay-to-post', $path . '/css/style.css' );
}

function pay_load_textdomain() {
	$locale = apply_filters( 'pay_locale', get_locale() );
	$mofile = dirname( __FILE__ ) . "/languages/app-pay-$locale.mo";

	if ( file_exists( $mofile ) ) {
		load_textdomain( 'app-pay', $mofile );
	}
}

function pay_add_payment_post_types(){
	APP_Item_Registry::register( 'new_item', 'Regular Post' );
}
