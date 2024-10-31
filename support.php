<?php

add_action( 'pay_setup_form', 'pay_setup_title', 10, 2 );
add_action( 'pay_setup_form', 'pay_setup_editor', 10, 2 );
add_action( 'pay_setup_form', 'pay_setup_category', 10, 2 );
add_action( 'pay_setup_form', 'pay_setup_post_tag', 10, 2 );

function pay_setup_title( $post_type, $object ){
	if( post_type_supports( $post_type, 'title' ) ){
		add_action( 'pay_display_form', 'pay_display_title' );
		add_filter( 'pay_validate_form', 'pay_validate_title' );
		add_filter( 'pay_process_form', 'pay_process_title' );
	}
}

function pay_display_title( $post_type ){

	$title = isset( $_POST['pay_post_title'] ) ? esc_attr( $_POST['pay_post_title'] ) : '';
	?>
	<div class="pay-form-field pay-group">
		<label for="new-post-title">
			<?php _e( 'Post Title', PAY_TD ); ?> <span class="required">*</span>
		</label>
		<input class="" type="text" value="<?php echo $title; ?>" name="pay_post_title" id="new-post-title" minlength="2">
	</div>
		<?php
}

function pay_validate_title( $errors ){

	$title = trim( strip_tags( $_POST['pay_post_title'] ) );
	if ( empty( $title ) ) {
		$errors->add('bad-title', __( 'Empty post title', PAY_TD ) );
	}

	return $errors;
}

function pay_process_title( $post ){

	$post['post_title'] = trim( strip_tags( $_POST['pay_post_title'] ) );
	return $post;

}

function pay_setup_editor( $post_type, $object ){
	if( post_type_supports( $post_type, 'editor' ) ){
		add_action( 'pay_display_form', 'pay_display_editor' );
		add_filter( 'pay_validate_form', 'pay_validate_editor' );
		add_filter( 'pay_process_form', 'pay_process_editor' );
	}
}

function pay_display_editor( $post_type ){

       	$description = isset( $_POST['pay_post_content'] ) ? $_POST['pay_post_content'] : '';
	?>
	<div class="pay-form-field pay-group">
		<label for="new-post-desc">
			<?php _e( 'Description', PAY_TD ); ?>
			<span class="required">*</span>
		</label>
		<div style="float:left" >
			<?php wp_editor( $description, 'new-post-desc', array(
				'textarea_name' => 'pay_post_content', 
				'editor_class' => 'requiredField', 
				'teeny' => true, 
				'textarea_rows' => 8
				) ); 
			?>
		</div>
	</div>
	<?php
}

function pay_validate_editor( $errors ){

	$content = trim( $_POST['pay_post_content'] );
	if ( empty( $content ) ) {
		$errors->add( 'bad-content', __( 'Empty post content', PAY_TD ) );
	}

	return $errors;
}

function pay_process_editor( $post ){

	$post['pay_post_content'] = trim( $_POST['pay_post_content'] );
	return $post;

}

function pay_setup_category( $post_type, $object ){
	if( in_array( 'category', get_object_taxonomies( $post_type ) ) ){
		add_action( 'pay_display_form', 'pay_display_category' );
		add_filter( 'pay_validate_form', 'pay_validate_category' );
		add_filter( 'pay_process_form', 'pay_process_category' );
	}
}

function pay_display_category( $post_type ){

	$selected_category = false;
	if( !empty( $_POST['pay_post_category'] ) ){
		$selected_category = $_POST['pay_post_category'];
	}
	?>
	<div class="pay-form-field pay-group">
		<label for="new-post-cat">
			<?php _e( 'Category', PAY_TD ); ?> 
			<span class="required">*</span>
		</label>
		<div class="category-wrap" style="float:left;">
			<div id="lvl0">
			<?php
				wp_dropdown_categories( array(
					'show_option_none' =>  __( '-- Select --', PAY_TD ),
					'selected' => $selected_category,
				        'hierarchical' => '1',
					'hide_empty' => 0,
					'orderby' => 'name',
					'name' => 'pay_post_category',
					'id' => 'cat',
					'show_count' => '0',
					'title_li' => '',
					'use_desc_for_title' => '1',
					'class' => 'cat requiredField',
				) );
			?>
			</div>
		</div>
		<div class="loading"></div>
	</div>
	<?php
}

function pay_validate_category( $errors ){

	$category = $_POST['pay_post_category'];
	if( empty( $category ) ){
		$errors->add( 'bad-category', __( 'Please choose a category', PAY_TD ) );
		return $errors;
	}
	
	return $errors;
}

function pay_process_category( $post ){

	$post['pay_post_category'] = $_POST['pay_post_category'];
	return $post;
}

function pay_setup_post_tag( $post_type, $object ){
	if( in_array( 'post_tag', get_object_taxonomies( $post_type ) ) ) {
		add_action( 'pay_display_form', 'pay_display_post_tags' );
		add_filter( 'pay_process_form', 'pay_process_post_tags' );
	}
}

function pay_display_post_tags( $post_type ){
	?>    
	<div class="pay-form-field pay-group">
		<label for="new-post-tags">
			<?php _e( 'Tags', PAY_TD ); ?>
		</label>
		<input type="text" name="pay_post_tags" id="new-post-tags" class="new-post-tags">
	</div>
	<?php
}

function pay_process_post_tags( $post ){

	$tags = '';
	$tags = preg_replace( '/\s*,\s*/', ',', rtrim( trim( $_POST['pay_post_tags'] ) ) );
	$tags = explode( ',', $tags );
	$post['tags_input'] = $tags;
	return $post;

}
