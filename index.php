<?php

session_start();

require( 'config.php' );
require( 'init.php' );
require( 'classes/Collection.php' );
require( 'includes/plugin.php' );

$stored_widgets = json_decode(get_option('widgets'), true);

$url_params = [];

if( PRETTY_URL ){
	$url_params = isset($_GET['viewpage']) ? explode("/", $_GET['viewpage']) : [];
	
	$_GET['viewpage'] = isset( $url_params[0] ) ? $url_params[0] : 'homepage';
	if(isset( $url_params[1] )){
		$_GET['slug'] = $url_params[1];
	}
}

load_language('index');
load_plugins('index');

$page_name = isset( $_GET['viewpage'] ) ? $_GET['viewpage'] : 'homepage';

$custom_path = get_custom_path($page_name);

require_once( TEMPLATE_PATH . '/functions.php' );

if(file_exists( 'includes/page-' . $custom_path . '.php' )){
	require( 'includes/page-' . $custom_path . '.php' );
} else {
	if(file_exists( TEMPLATE_PATH.'/page-' . $page_name . '.php' )){
		require( TEMPLATE_PATH.'/page-' . $page_name . '.php' );
	} else {
		require( 'includes/page-404.php' );
	}
}

?>