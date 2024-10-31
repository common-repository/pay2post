<?php

class Pay_Order_Summary extends APP_View{

	function __construct(){
		parent::__construct( __( 'Order Checkout', PAY_TD ) );
		add_filter( 'appthemes_disable_order_summary_template', '__return_true' );
	}

	function condition(){
		return is_singular( APPTHEMES_ORDER_PTYPE );
	}

	function template_include( $templates ){

		add_filter( 'the_content', array( $this, 'add_content' ) );
		add_filter( 'comments_open', '__return_false' );
		
		$template = locate_template( array( 'order.php' ) );
		if( $template ){
			return $template;
		}
		return get_page_template();
	}

	function add_content( $content ){

		$order = get_order();

		if( ! in_array( $order->get_status(), array( APPTHEMES_ORDER_COMPLETED, APPTHEMES_ORDER_ACTIVATED ) ) ){
			echo '<div class="pay-gateway">';
			process_the_order();
			echo '</div>';
		}

		$order = get_order();

		if( in_array( $order->get_status(), array( APPTHEMES_ORDER_COMPLETED, APPTHEMES_ORDER_ACTIVATED ) ) ){
			
			echo '<div class="pay-order-summary">';

			$table = new APP_Order_Summary_Table( $order );
			$table->show();

			$first_item = $order->get_item();
			$url = get_permalink( $first_item['post']->ID );
			
			$ptype_obj = get_post_type_object( $first_item['post']->post_type );
			$message = sprintf( __( 'Continue to %s', PAY_TD ), $ptype_obj->labels->singular_name );
			printf( '<input type="submit" value="%s" onClick="location.href=\'%s\'; return false;">', $message, $url );

			echo '</div>';

		}

		return '';

	}
}
new Pay_Order_Summary;
