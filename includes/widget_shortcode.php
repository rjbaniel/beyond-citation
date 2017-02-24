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
		$database_name = $database_array[0]->post_title;

		?>
		<button id="bc-widget-gen-button" class="bc-widget-gen__button">Embed</button>
		<div id="bc-widget-gen-container" class="bc-widget-gen__container">
			<p class="bc-widget-gen__description">Copy the HTML below and paste it into your website to add a widget with information about <?php echo esc_html( $database_name ) ?>. Click anywhere inside the text area to to select it for copying.</p>
			<pre class="bc-widget-gen__html" id="bc-widget-gen-pre">
&lt;div class="bc-widget" data-database="<?php echo esc_attr( $database ); ?>"&gt;&lt;/div&gt;
&lt;script type="text/javascript" src="<?php echo esc_url( $widget_script_url ); ?>"&gt;&lt;/script&gt;</pre>
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