<?php

function bc_register_database_cpt() {
	$args = array(
		'label'				=> __( 'Databases', 'beyond-citation'),		
		'description'		=> 'An academic database',
		'public'			=> true,
		'capability_type'	=> 'post',
		'reweirte'			=> array(
			'slug' => __( 'database', 'beyond-citation' ),
		),
		'supports'			=> array( 'title', 'custom-fields' ),
		'register_meta_box_cb' => 'bc_register_database_metabox'
	);
	
	register_post_type( 'bc_database', $args );	
}

function bc_register_database_metabox() {
	add_meta_box( 
		'bc_database_info',
		__( 'Database Info', 'beyond-citation' ),
		'bc_display_database_metabox',
		'bc_database',
		'normal',
		'high'
	);
}

function bc_display_database_metabox( $post ) {
	$fields = bc_get_database_fields();

	// Display a <textarea> with a <label> for each postmeta field
	foreach( $fields as $field_name => $field ) : ?>
		<label for="<?php echo $field_name; ?>">
			<?php echo esc_html( $field['title'] ); ?>
		</label>
		<br>
		<textarea class="bc-database-field" name="<?php echo esc_attr( $field_name ); ?>" id="<?php echo esc_attr( $field_name ); ?>">
<?php echo esc_textarea( get_post_meta( $post->ID, $field_name, true ) ); ?></textarea>
	<?php endforeach;
	
	wp_nonce_field( 'bc_update_database_meta', 'bc_database_meta_nonce' );
}

function bc_save_database_fields() {
	// Only continue if the saved post is a bc_database
	if ( ! isset( $_POST['post_type'] ) || $_POST['post_type'] !== 'bc_database' )
		return;

	// nonce check
	if ( ! isset( $_POST['bc_database_meta_nonce'] ) ||
		! wp_verify_nonce( $_POST['bc_database_meta_nonce'], 'bc_update_database_meta' ) )
		return;

	$post_id = $_POST['post_ID'];
	$fields = bc_get_database_fields();
	// Update the bc_database postmeta fields
	foreach( $fields as $field_name => $field ) {
		if ( isset( $_POST[$field_name] ) ) {
			update_post_meta( $post_id, $field_name, $_POST[$field_name] );
		}
	}
}
add_action( 'save_post', 'bc_save_database_fields' );