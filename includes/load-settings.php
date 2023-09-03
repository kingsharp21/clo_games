<?php

define( "ADMIN_DEMO", false );

require( 'sub-folder.php' );

$options = load_site_settings();

if(ADMIN_DEMO){
	$theme = 'default';
	if(isset($_GET['theme'])){
		if($_GET['theme'] == 'dark-grid' || $_GET['theme'] == 'default'){
			$theme = $_GET['theme'];
			$_SESSION['theme'] = $_GET['theme'];
		}
	} elseif(isset($_SESSION['theme'])){
		$theme = $_SESSION['theme'];
	}
	$options['theme_name'] = $theme;
}

define( "PRETTY_URL", filter_var($options['pretty_url'], FILTER_VALIDATE_BOOLEAN) );
define( "URL_PROTOCOL", $options['url_protocol'] );
define( "DOMAIN", URL_PROTOCOL . $_SERVER['SERVER_NAME'] . get_domain_port() . '/' . SUB_FOLDER );
define( "SITE_DOMAIN", $_SERVER['SERVER_NAME'] );

if($options['custom_path']){
	$options['custom_path'] = json_decode($options['custom_path'], true);
}

function get_domain_port(){
	//Used for localhost with port
	$port = $_SERVER['SERVER_PORT'];
	if($port && $port === '8080'){
		return ':'.$port;
	} else {
		return '';
	}
}

function load_site_settings(){
	$conn = open_connection();
	$sql = "SELECT * FROM options";
	$st = $conn->prepare($sql);
	$st->execute();
	$row = $st->fetchAll();
	$opt = array();
	foreach ($row as $item) {
		$opt[$item['name']] = $item['value'];
	}
	return $opt;
}

?>