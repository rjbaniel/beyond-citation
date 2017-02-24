<?php
/*
Plugin Name: Beyond Citation
Description: Plugin for the Beyond Citation project.
Version: 0.1.0
Author: Daniel Jones, Boone Gorges
Text Domain: beyond-citation
Domain Path: /languages
*/

function bc_init() {
	bc_includes();
	bc_register_database_cpt();
}
add_action( 'init', 'bc_init' );

function bc_includes() {
	include_once( plugin_dir_path( __FILE__ ) . '/includes/database_cpt.php' );
	include_once( plugin_dir_path( __FILE__ ) . '/includes/databases_api.php' );
	include_once( plugin_dir_path( __FILE__ ) . '/includes/widget_shortcode.php' );
}