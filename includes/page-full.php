<?php

//New window page for gameplay

require_once( TEMPLATE_PATH . '/functions.php' );

if ( !isset($_GET['slug']) || !$_GET['slug'] ) {
	require( ABSPATH . 'includes/page-404.php' );
	return;
}

$_GET['slug'] = htmlspecialchars($_GET['slug']);

$game = Game::getBySlug( $_GET['slug'] );
if($game){
	$page_title = $game->title;
	$meta_description = str_replace(array('"', "'"), "", strip_tags($game->description));

	?>

	<!DOCTYPE html>
	<html>
	<head>
		<meta charset="utf-8">
		<title><?php echo $page_title ?></title>
		<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no">
		<meta name="description" content="<?php echo $meta_description ?>">
		<style type="text/css">
			body {
			    color: #eee;
			    line-height: 1.43;
			    position: inherit;
			    margin: 0;
			    padding: 0;
			    background-color: #000;
			    overflow: hidden;
			    height: 100%;
			}
			#game-content {
			    position: absolute;
			    top: 0;
			    left: 0;
			    width: 0;
			    height: 0;
			    overflow: hidden;
			    max-width: 100%;
			    max-height: 100%;
			    min-width: 100%;
			    min-height: 100%;
			    box-sizing: border-box;
			}
		</style>
	</head>
	<body>
		<iframe id="game-content" frameborder="0" allow="autoplay" allowfullscreen="" seamless="" scrolling="no" src="<?php echo $game->url ?>"></iframe>
	</body>
	</html>

	<?php
} else {
	require( ABSPATH . 'includes/page-404.php' );
}

?>