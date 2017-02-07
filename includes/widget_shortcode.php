<?php
function bc_display_widget_generator( $atts ) {
	wp_enqueue_script( 'bc-widget-gen', plugins_url( 'beyond-citation/widget-gen.js' ), array(), 0.1, true );
	wp_enqueue_style( 'bc-widget-gen', plugins_url( 'beyond-citation/widget-gen.css' ), array(), 0.1, 'screen' );
	ob_start();

	$atts = shortcode_atts( array(
		'database' => '',
	), $atts );
	$database = $atts['database'];
	?>
	<button id="bc-widget-gen-button" class="bc-widget-gen__button">Embed</button>
	<div id="bc-widget-gen-container" class="bc-widget-gen__container">
		<p class="bc-widget-gen__description">Copy the HTML below and paste it into your website to add a widget with information about this database.</p>
		<pre class="bc-widget-gen__html">
&lt;div class="bc-widget" data-database="<?php echo esc_attr( $database ); ?>"&gt;&lt;/div&gt;
&lt;script type="text/javascript" src="/wp-content/plugins/beyond-citation/widget.js"&gt;&lt;/script&gt;</pre>
	</div>
	<?php
	return ob_get_clean();
}

add_shortcode( 'bc_widget_generator', 'bc_display_widget_generator' );
?>