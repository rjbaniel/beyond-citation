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
		'supports'			=> array( 'title', 'editor', 'thumbnail' ),
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
			switch ( $type ) :
				case 'textarea':
					?>
					<textarea <?php echo $class_name_id_string; ?> <?php echo $required; ?> cols="50" rows="5"><?php
						echo esc_textarea( get_post_meta( $post->ID, $field_name, true ) ); ?></textarea>
					<?php
					continue;
				case 'text':
					?>
					<input type="text" size="75" value="<?php echo esc_attr( get_post_meta( $post->ID, $field_name, true ) ); ?>" <?php echo $class_name_id_string; ?> <?php echo $required; ?>>
					<?php
					continue;
				case 'checkbox':
					$checked = '';
					if ( get_post_meta( $post->ID, $field_name, true ) )
						$checked = 'checked';
					?>
					<input type="checkbox" <?php echo $checked; ?> <?php echo $class_name_id_string; ?>>
					<?php
					continue;
				case 'select':
					$options = $field['options'];
					?>
						<select <?php echo $class_name_id_string; ?>>
							<?php
							foreach( $options as $option ) :
								$selected = '';
								if ( $option['value'] == get_post_meta( $post->ID, $field_name, true ) )
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
					$selected_options = get_post_meta( $post->ID, $field_name, true );
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
		),
		'bc_profile_link' => array(
			'title' => 'Link to full BC content for database',
			'type' => 'text',
			'required' => true,
		),
		'date_range' => array(
			'title' => 'Date range',
			'type' => 'text',
			'required' => false,
		),
		'publisher_name' => array(
			'title' => 'Publisher',
			'type' => 'text',
			'required' => true,
		),
		'link_publisher_about_page' => array(
			'title' => 'Publisher About page',
			'type' => 'text',
			'required' => true,
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
					'value' => 'media',
					'display' => 'Media',
				),
			),
			'required' => true,
		),
		'geographic_location_original_materials' => array(
			'title' => 'Location of original materials',
			'type' => 'text',
			'required' => false,
		),
		'geographic_location_subject' => array(
			'title' => 'Location of subject matter',
			'type' => 'text',
			'required' => false,
		),
		'image_exportable' => array(
			'title' => 'Exportable image',
			'type' => 'checkbox',
			'required' => false,
		),
		'facsimile_image' => array(
			'title' => 'Facsimile image',
			'type' => 'checkbox',
			'required' => false,
		),
		'full_text_searchable' => array(
			'title' => 'Full text searchable',
			'type' => 'checkbox',
			'required' => true,
		),
		'link_titles_list' => array(
			'title' => 'Titles list link',
			'type' => 'text',
			'required' => false,
		),
		'original_catalog' => array(
			'title' => 'Original catalogue',
			'type' => 'textarea',
			'required' => false,
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
		),
		'original_sources' => array(
			'title' => 'Original sources',
			'type' => 'textarea',
			'required' => false,
		),
		'history' => array(
			'title' => 'History/Provenance',
			'type' => 'textarea',
			'required' => true,
		),
		'third_party_reviews' => array(
			'title' => 'Reviews',
			'type' => 'textarea',
			'required' => false,
		),
		'link_wordlcat' => array(
			'title' => 'Worldcat link',
			'User description' => 'To see the library closest to you that has access: ',
			'type' => 'text',
			'required' => false,
		),
		'access' => array(
			'title' => 'Access',
			'type' => 'textarea',
			'required' => true,
		),
		'ill_conditions' => array(
			'title' => 'InterLibrary Loan Conditions',
			'type' => 'textarea',
			'required' => false,
		),
		'info_from_publisher' => array(
			'title' => 'Info from Publisher',
			'type' => 'textarea',
			'required' => false,
		),
		'conversations' => array(
			'title' => 'Conversations',
			'type' => 'textarea',
			'required' => true,
		),
		'citing' => array(
			'title' => 'Citing',
			'type' => 'textarea',
			'required' => true,
		),
		'bc_editor_entry' => array(
			'title' => 'BC Editor Entry',
			'type' => 'textarea',
			'required' => true,
		),
	);
	return $fields;
}
