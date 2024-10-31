<?php

class Pay_Add_Post {

	function __construct() {
		add_shortcode( 'pay_addpost', array( $this, 'shortcode' ) );

		add_action( 'appthemes_first_run', array( $this, 'install' ) );
	}
	
	function shortcode( $attributes ) {

		add_action( 'comments_open', '__return_false' );

		extract( shortcode_atts( array('post_type' => 'post'), $attributes ) );

		if( ! is_user_logged_in() ){
			$message = __( 'This page is restricted. Please %s to view this page.', PAY_TD );
			return sprintf( $message, wp_loginout( get_permalink(), false ) );
		}

		$post_type_object = get_post_type_object( $post_type );
		if( ! $post_type_object ){
			$message = __( 'This page has been configured incorrectly. Post type does not exist.', PAY_TD );
			return $message;
		}

		do_action( 'pay_setup_form', $post_type, $post_type_object );

		ob_start();
	
		$this->post_form( $post_type );
	        $content = ob_get_contents();

        	ob_end_clean();

	        return $content;
	}

	function post_form( $post_type ) {

		if ( isset( $_POST['pay_post_new_submit'] ) ) {
			$nonce = $_REQUEST['_wpnonce'];
			if ( !wp_verify_nonce( $nonce, 'pay-add-post' ) ) {
				wp_die( __( 'Cheating?', PAY_TD ) );
			}	
			$this->submit_post( $post_type );
		}

		?>
		<div id="pay-post-area">
			<form id="pay_new_post_form" name="pay_new_post_form" action="" enctype="multipart/form-data" method="POST">
			<?php wp_nonce_field( 'pay-add-post' ) ?>
			<div class="pay-post-form">
	
				<?php do_action( 'pay_display_form', $post_type ); ?>
				<?php do_action( 'appthemes_purchase_fields' ); ?>	
	
				<div class="pay-form-field pay-group">
					<label><?php _e( 'Payment Method', PAY_TD ); ?></label>
					<?php appthemes_list_gateway_dropdown(); ?>
				</div>
				<div class="pay-form-field pay-group">
					<label>&nbsp;</label>
					<input class="pay-submit" type="submit" name="pay_new_post_submit" value="<?php echo esc_attr_e( 'Submit Post', PAY_TD ); ?>">
					<input type="hidden" name="pay_post_type" value="<?php echo $post_type; ?>" />
					<input type="hidden" name="pay_post_new_submit" value="yes" />
				</div>
	
			</div>
			</form>
		</div>
        <?php
    	}

	function submit_post( $post_type ) {
		global $userdata;

		$errors = array();
	
		$errors = apply_filters( 'pay_validate_form', new WP_Error );
		apply_filters( 'appthemes_validate_pruchase_fields', $errors );
		if ( $errors->get_error_codes() ) {
			echo pay_error_msg( $errors->get_error_messages() );
			return;
		}

		$post = array(
			'post_status' => 'draft',
			'post_author' => $userdata->ID,
			'post_type' => $post_type,
		);

		$post = apply_filters( 'pay_process_form', $post );
		$post_id = wp_insert_post( $post );
		do_action( 'pay_process_post', get_post( $post_id ) );

		if ( $post_id ) {

			pay_notify_post_mail( $userdata, $post_id );
			
			$order = appthemes_new_order();

			$item_type = PAY_ITEM_REGULAR;
			$price = APP_Item_Registry::get_meta( $item_type, 'price' );

			$order->add_item( $item_type, apply_filters( 'pay_set_price', $price ), $post_id );
			do_action( 'appthemes_create_order', $order );

			$order->set_gateway( $_POST['payment_gateway'] );
			wp_redirect( $order->get_return_url() );
			exit;
		}
	}

	function clean_tags( $string ){
		$string = preg_replace( '/\s*,\s*/', ',', rtrim( trim( $string ), ' ,' ) );
		return $string;
	}

	function install(){

		$query = array( 's' => '[pay_addpost]' );
		$results = new WP_Query( $query );

		if( $results->post_count > 0 )
			return;

		$post = array(
			'post_title' => __( 'Add Post', PAY_TD ),
			'post_type' => 'page',
			'post_status' => 'publish',
			'post_content' => '[pay_addpost]'
		);
		wp_insert_post( $post );
	}

}

new Pay_Add_Post;
