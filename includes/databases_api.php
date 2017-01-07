<?php

/*
	Two routes with one endpoint each:
		-One to get a list of all database ids
		-The other to get info about a database by id
*/
function bc_register_routes() {
	$namespace = 'beyond_citation/v';
	$version = '1';

	register_rest_route( $namespace . $version, '/databases', array(
		'methods' => WP_REST_Server::READABLE,
		'callback' => 'bc_get_databases',
	) );

	register_rest_route( $namespace . $version, '/databases/(?P<id>[\w\d-]+)', array(
		'methods' => WP_REST_Server::READABLE,
		'callback' => 'bc_get_database_info'
	) );
}
add_action( 'rest_api_init', 'bc_register_routes' );

function bc_get_databases( $request ) {
	$posts = get_posts( array( 'post_type' => 'bc_database' ) );
	if ( empty( $posts ) )
		return null;
	$databases = wp_list_pluck( $posts, 'post_name' );
	return $databases;
}

function bc_get_database_info( $request ) {
	$params = $request->get_params();
	$db_id = $params['id'];
	
	$posts = get_posts( array(
			'post_type' => 'bc_database',
			'name' => $db_id,
		)
	);
	if ( empty( $posts ) )
		return 'No databases found.';

	$database = $posts[0];
	$return_array = array(
		'name' => $db_id,
		'title' => get_the_title( $database->ID ),
		'access' => get_post_meta( $database->ID, '_bc_access', true ),
	);

	return $return_array;
}