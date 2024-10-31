<?php

add_action( 'admin_init', 'pay_settings_setup' );
function pay_settings_setup(){
	global $admin_page_hooks;
	add_action( 'tabs_' . $admin_page_hooks['app-payments'] . '_page_app-payments-settings', 
		array( 'Pay_Settings_Tab', 'init' ) );
}

class Pay_Settings_Tab{

	private static $page;
	static function init( $page ){
		self::$page = $page;

		$page->tabs->add_after( 'general', 'pay2post', __( 'Pay2Post', PAY_TD ) );

		$fields = array(
			array(
				'title' => __( 'Price Per Post', PAY_TD ),
				'name' => 'price_per_post',
				'type' => 'text',
				'extra' => array(
					'style' => 'width: 50px',
				)
			),
		);

		$page->tab_sections['pay2post']['general'] = array(
			'title' => __( 'General', PAY_TD ),
			'fields' => $fields 
		);
	}

}
