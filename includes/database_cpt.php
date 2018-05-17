<?php

function bc_register_database_cpt() {
	$args = array(
		'label'				=> __( 'Databases', 'beyond-citation'),
		'description'		=> 'An academic database',
		'public'			=> true,
		'capability_type'	=> 'post',
		'rewrite'			=> array(
			'slug' => __( 'database', 'beyond-citation' ),
		),
		'supports'			=> array( 'title', 'editor', 'thumbnail', 'excerpt' ),
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

	wp_enqueue_style( 'beyond-citation-cpt', BEYONDCITATION_PLUGIN_URL . 'assets/css/cpt.css' );
}

function bc_display_database_metabox( $post ) {
	$fields = bc_get_database_fields();
	?>
		<em>* means the field is required.</em>
	<?php
	foreach( $fields as $field_name => $field ) : ?>
		<div class="bc-database-field" style="margin-bottom: .5rem;">
			<label for="<?php echo $field_name; ?>">
				<?php
					if ( $field['required'] )
						$required = 'required';

					echo esc_html( $field['title'] );
					if ( $required )
						echo '*';
				?>
			</label>
			<br>
			<?php
			$type = $field['type'];
			$class_name_id_string = 'class="bc-database-input" name="' . esc_attr( $field_name ) . '" id="' . esc_attr( $field_name ) . '"';
			$required = '';

			$field_value = bc_get_database_field_value( $field_name, $post->ID );
			$disabled = ! $field['editable'] ? 'disabled="disabled"' : '';

			switch ( $type ) :
				case 'textarea':
					?>
					<textarea <?php echo $class_name_id_string; ?> <?php echo $required; ?> <?php echo $disabled; ?> cols="50" rows="5"><?php
						echo esc_textarea( $field_value ); ?></textarea>
					<?php
					continue;
				case 'text':
					?>
					<input type="text" size="75" value="<?php echo esc_attr( $field_value ); ?>" <?php echo $class_name_id_string; ?> <?php echo $required; ?> <?php echo $disabled; ?>>
					<?php
					continue;
				case 'checkbox':
					$checked = '';
					if ( $field_value )
						$checked = 'checked';
					?>
					<input type="checkbox" <?php echo $checked; ?> <?php echo $class_name_id_string; ?> <?php echo $disabled; ?>>
					<?php
					continue;
				case 'select':
					$options = $field['options'];
					?>
						<select <?php echo $class_name_id_string; ?> <?php echo $disabled; ?>>
							<?php
							foreach( $options as $option ) :
								$selected = '';
								if ( $option['value'] == $field_value )
									$selected = 'selected';
							?>
								<option value="<?php echo esc_attr( $option['value'] ) ?>" <?php echo $selected; ?>>
									<?php echo esc_html( $option['display'] ); ?>
								</option>
							<?php
							endforeach
							?>
						</select>
					<?php
					continue;
				case 'checkboxes':
					$options = $field['options'];
					$selected_options = $field_value;
					?>
					<em>Select all that apply</em><br>
					<?php
					foreach ( $options as $option ) :
						$checked = '';
						if ( is_array( $selected_options ) ) {
							if ( in_array( $option['value'], $selected_options) )
								$checked = 'checked';
						} else {
							if ( $option['value'] == $selected_options )
								$checked = 'checked';
						}
						?>
						<input
							type="checkbox"
							name="<?php echo esc_attr( $field_name ) . '[]'; ?>"
							value="<?php echo esc_attr( $option['value'] ); ?>"
							class="bc-database-input"
							id="<?php echo $option['value']; ?>"
							<?php echo $checked; ?>
						>
							<label for="<?php echo $option['value']; ?>">
								<?php echo esc_html( $option['display'] ); ?>
							</label>
						</input><br>
						<?php
					endforeach;
					continue;
			endswitch;
			?>
		</div>
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
		} elseif ( $field['type'] == 'checkbox' || $field['type'] == 'checkboxes' ) {
			update_post_meta( $post_id, $field_name, '' );
		}
	}
}
add_action( 'save_post', 'bc_save_database_fields' );

function bc_get_database_fields() {
	$fields = array(
		'bc_overview' => array(
			'title' => 'BC Overview',
			'type' => 'textarea',
			'required' => true,
			'editable' => true,
		),
		'bc_profile_link' => array(
			'title' => 'Link to full BC content for database',
			'type' => 'text',
			'required' => true,
			'editable' => false,
		),
		'date_range' => array(
			'title' => 'Date range',
			'type' => 'text',
			'required' => false,
			'editable' => true,
		),
		'publisher_name' => array(
			'title' => 'Publisher',
			'type' => 'text',
			'required' => true,
			'editable' => true,
		),
		'link_publisher_about_page' => array(
			'title' => 'Publisher About page',
			'type' => 'text',
			'required' => true,
			'editable' => true,
		),
		'type_object' => array(
			'title' => 'Object type',
			'type' => 'checkboxes',
			'options' => array(
				array(
					'value' => 'journals',
					'display' => 'Journals',
				),
				array(
					'value' => 'newspapers',
					'display' => 'Newspapers',
				),
				array(
					'value' => 'images',
					'display' => 'Images',
				),
				array(
					'value' => 'ephemera',
					'display' => 'Ephemera',
				),
				array(
					'value' => 'indexes',
					'display' => 'Indexes',
				),
				array(
					'value' => 'artifiacts',
					'display' => 'Artifacts',
				),
				array(
					'value' => 'articles',
					'display' => 'Articles',
				),
				array(
					'value' => 'maps',
					'display' => 'Maps',
				),
				array(
					'value' => 'books',
					'display' => 'Books',
				),
				array(
					'value' => 'time-based-media',
					'display' => 'Time-based media',
				),
			),
			'required' => true,
			'editable' => true,
		),
		'geographic_location_original_materials' => array(
			'title' => 'Location of original materials',
			'type' => 'text',
			'required' => false,
			'editable' => true,
		),
		'geographic_location_subject' => array(
			'title' => 'Location of subject matter',
			'type' => 'text',
			'required' => false,
			'editable' => true,
		),
		'image_exportable' => array(
			'title' => 'Exportable image',
			'type' => 'checkbox',
			'required' => false,
			'editable' => true,
		),
		'facsimile_image' => array(
			'title' => 'Facsimile image',
			'type' => 'checkbox',
			'required' => false,
			'editable' => true,
		),
		'full_text_searchable' => array(
			'title' => 'Full text searchable',
			'type' => 'text',
			'required' => false,
			'editable' => true,
		),
		'link_titles_list' => array(
			'title' => 'Titles list link',
			'type' => 'text',
			'required' => false,
			'editable' => true,
		),
		'original_catalog' => array(
			'title' => 'Original catalogue',
			'type' => 'textarea',
			'required' => false,
			'editable' => true,
		),
		'original_microfilm' => array(
			'title' => 'Original microfilm',
			'type' => 'select',
			'options' => array(
				array(
					'value' => 'yes',
					'display' => 'Yes',
				),
				array(
					'value' => 'no',
					'display' => 'No',
				),
				array(
					'value' => 'some',
					'display' => 'Some',
				),
			),
			'required' => false,
			'editable' => true,
		),
		'original_sources' => array(
			'title' => 'Original sources',
			'type' => 'textarea',
			'required' => false,
			'editable' => true,
		),
		'history' => array(
			'title' => 'History/Provenance',
			'type' => 'textarea',
			'required' => true,
			'editable' => true,
		),
		'third_party_reviews' => array(
			'title' => 'Reviews',
			'type' => 'textarea',
			'required' => false,
			'editable' => true,
		),
		'link_worldcat' => array(
			'title' => 'WorldCat link',
			'User description' => 'To see the library closest to you that has access: ',
			'type' => 'text',
			'required' => false,
			'editable' => true,
		),
		'access' => array(
			'title' => 'Access',
			'type' => 'textarea',
			'required' => true,
			'editable' => true,
		),
		'ill_conditions' => array(
			'title' => 'InterLibrary Loan Conditions',
			'type' => 'textarea',
			'required' => false,
			'editable' => true,
		),
		'info_from_publisher' => array(
			'title' => 'Info from Publisher',
			'type' => 'textarea',
			'required' => false,
			'editable' => true,
		),
		'conversations' => array(
			'title' => 'Conversations',
			'type' => 'textarea',
			'required' => true,
			'editable' => true,
		),
		'citing' => array(
			'title' => 'Citing',
			'type' => 'textarea',
			'required' => true,
			'editable' => true,
		),
		'bc_editor_entry' => array(
			'title' => 'BC Editor Entry',
			'type' => 'textarea',
			'required' => true,
			'editable' => true,
		),
	);
	return $fields;
}

/**
 * Get value for a database field.
 *
 * @param string $field_name
 */
function bc_get_database_field_value( $field_name, $post_id = null ) {
	if ( null === $post_id && is_singular( 'bc_database' ) ) {
		$post_id = get_queried_object_id();
	}

	$value = '';

	switch ( $field_name ) {
		case 'bc_profile_link' :
			$value = get_permalink( $post_id );

		default :
			$value = get_post_meta( $post_id, $field_name, true );
		break;
	}

	return $value;
}
