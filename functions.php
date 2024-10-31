<?php
add_action( 'init', 'pay_buffer_start' );
add_action( 'appthemes_transaction_completed', 'pay_handle_completed_order' );
add_action( 'appthemes_transaction_activated', 'pay_handle_activated_order' );
add_filter( 'pay_can_post', 'pay_can_post' );

if ( !stristr( get_option( 'permalink_structure' ), '%postname%' ) ) {
    add_action( 'admin_notices', 'pay_permalink_nag', 3 );
}

function pay_auth_redirect_login() {
    $user = wp_get_current_user();

    if ( $user->ID == 0 ) {
        nocache_headers();
        wp_redirect( get_option( 'siteurl' ) . '/wp-login.php?redirect_to=' . urlencode( $_SERVER['REQUEST_URI'] ) );
        exit();
    }
}

function pay_error_msg( $error_msg ) {
    $msg_string = '';
    foreach ($error_msg as $value) {
        if ( !empty( $value ) ) {
            $msg_string = $msg_string . '<div class="error">' . $msg_string = $value . '</div>';
        }
    }
    return $msg_string;
}

function pay_notify_post_mail( $user, $post_id ) {
    $blogname = get_bloginfo( 'name' );
    $to = get_bloginfo( 'admin_email' );
    $permalink = get_permalink( $post_id );

    $headers = sprintf( __( "From: %s <%s>\r\n", PAY_TD ), $blogname, $to );
    $subject = sprintf( __( '[%s] New Post Submission', PAY_TD ), $blogname );

    $msg = sprintf( __( 'A new post has been submitted on %s', PAY_TD ), $blogname ) . "\r\n\r\n";
    $msg .= sprintf( __( 'Author : %s', PAY_TD ), $user->display_name ) . "\r\n";
    $msg .= sprintf( __( 'Author Email : %s', PAY_TD ), $user->user_email ) . "\r\n";
    $msg .= sprintf( __( 'Title : %s', PAY_TD ), get_the_title( $post_id ) ) . "\r\n";
    $msg .= sprintf( __( 'Permalink : %s', PAY_TD ), $permalink ) . "\r\n";
    $msg .= sprintf( __( 'Edit Link : %s', PAY_TD ), admin_url( 'post.php?action=edit&post=' . $post_id ) ) . "\r\n";

    wp_mail( $to, $subject, $msg, $headers );
}

function pay_buffer_start() {
	ob_start();
}

function pay_permalink_nag() {

    if ( current_user_can( 'manage_options' ) )
    	    $msg = sprintf( __( 'You need to set your <a href="%1$s">permalink custom structure</a> to at least contain <b>/&#37;postname&#37;/</b> before Pay2Post will work properly.', PAY_TD ), 'options-permalink.php' );

    echo "<div class='error fade'><p>$msg</p></div>";
}


function pay_can_post( $perm ) {
    $user = wp_get_current_user();

    if ( ! is_user_logged_in() ) {
            return 'no';
    }

    return $perm;
}

function pay_handle_activated_order( $order ){

	foreach( $order->get_items( PAY_ITEM_REGULAR ) as $item ){
		wp_publish_post( $item['post']->ID );
	}

}

function pay_handle_completed_order( $order ) {
	$order->activate();
}
