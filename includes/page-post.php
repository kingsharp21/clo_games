<?php

defined('POST_ACTIVE') or die('Posts plugin not installed.');

require_once( TEMPLATE_PATH . '/functions.php' );

$post = null;

if ( isset($_GET['slug']) ) {
	$_GET['slug'] = htmlspecialchars($_GET['slug']);
	$post = Post::getBySlug( $_GET['slug'] );
}

if($post){
	$page_title = $post->title . ' | '.SITE_TITLE;
	$meta_description = str_replace(array('"', "'"), "", strip_tags($post->content));
	require( TEMPLATE_PATH . '/post.php' );
} else {
	if(file_exists( TEMPLATE_PATH . '/post-list.php' )){
		$page_title = _t('Posts') . ' | '.SITE_TITLE;
		$meta_description = _t('Posts') .' | '.SITE_DESCRIPTION;
		require( TEMPLATE_PATH . '/post-list.php' );
	} else {
		require( ABSPATH . 'includes/page-404.php' );
	}
}

?>