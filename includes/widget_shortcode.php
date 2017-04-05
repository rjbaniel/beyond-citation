<?php
function bc_display_widget_generator( $atts ) {
	wp_enqueue_script( 'bc-widget-gen', plugins_url( 'beyond-citation/widget-gen.js' ), array(), 0.1, true );
	wp_enqueue_style( 'bc-widget-gen', plugins_url( 'beyond-citation/widget-gen.css' ), array(), 0.1, 'screen' );
	$widget_script_url = plugins_url( 'beyond-citation' ) . '/widget.js';
	ob_start();

	$atts = shortcode_atts( array(
		'database' => '',
	), $atts );
	$database = $atts['database'];
	$args = array(
		'name' => $database,
		'post_type' => 'bc_database',
		'post_status' => 'publish',
		'posts_per_page' => 1,
	);

	$database_array = get_posts( $args );
	if ( $database_array ) :
		$database_object = $database_array[0];
		$database_id = $database_object->ID;
		$database_name = $database_object->post_title;

		?>
		<button id="bc-widget-gen-button" class="bc-widget-gen__button">Embed</button>
		<div id="bc-widget-gen-container" class="bc-widget-gen__container">
			<div class="bc-widget-gen__embed-tabs">
				<a class="embed-tab embed-tab--selected" data-section="embedJS">
					Dynamic
				</a>
				<a class="embed-tab" data-section="embedHTML">
					HTML-only
				</a>
			</div>

			<div id="embedJS" class="bc-widget-gen__embed-section bc-widget-gen__embed-section--js bc_visible">
				<p class="bc-widget-gen__description">Copy the HTML below and paste it into your website to add a widget with information about <?php echo esc_html( $database_name ) ?>. Click anywhere inside the text area to to select it for copying.</p>
				<pre class="bc-widget-gen__code">
&lt;div class="bc-widget" data-database="<?php echo esc_attr( $database ); ?>"&gt;&lt;/div&gt;
&lt;script type="text/javascript" src="<?php echo esc_url( $widget_script_url ); ?>"&gt;&lt;/script&gt;</pre>
			</div>
			<div id="embedHTML" class="bc-widget-gen__embed-section bc-widget-gen__embed-section--html">
				<p class="bc-widget-gen__description">Use this information if you are not able to load external Javascript files on your website. Please note that this code will <em>not</em> be
					automatically updated when we make updates here at Beyond Citation. You will need to re-generate the embed code and replace the older version.</p>
				<pre class="bc-widget-gen__code"><?php
					$date = get_post_meta( $database_id, 'date_range', true );
					$publisher = get_post_meta( $database_id, 'publisher_name', true );
					$overview = get_post_meta( $database_id, 'bc_overview', true );
					$profile_link = esc_url( get_post_meta( $database_id, 'bc_profile_link', true ) );
					?>&lt;h3&gt;<?php echo esc_html( $database_name ); ?>&lt;/h3&gt;
&lt;p&gt;&lt;strong&gt;Date Range: &lt;/strong&gt;<?php echo esc_html( $date ); ?>&lt;br&gt;
&lt;strong&gt;Publisher: &lt;/strong&gt;<?php echo esc_html( $publisher ); ?>&lt;/p&gt;
&lt;p&gt;<?php echo esc_html( $overview ); ?>&lt;/p&gt;
&lt;a href="<?php echo esc_url( $profile_link ); ?>"&gt;See more about <?php echo esc_html( $database_name ); ?>&lt;/a&gt;</pre>
			</div>
		</div>
		<?php
	else :
	?>
		<p>Sorry, we weren't able to generate an embed code for this database ID.</p>
	<?php	
	endif;
	return ob_get_clean();
}

add_shortcode( 'bc_widget_generator', 'bc_display_widget_generator' );
?>