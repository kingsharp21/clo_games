<?php

if(!defined('CRON')){
	die('p');
}

$data = get_option('cron-job');

define("LIMIT", 3);
$game_count = 0;

if(!is_null($data)){
	$data = json_decode($data, true);
	if(isset($data['auto-post'])){
		$task_date = $data['auto-post']['date'];
		$cur_date = date("Y-m-d H:i:s");
		if($cur_date >= $task_date){
			$datetime1 = date_create($cur_date);
			$datetime2 = date_create($task_date);
			$interval = date_diff($datetime1, $datetime2);
			$diff = $interval->format('%d');

			if($diff < 4){
				$new_task_date = date('Y-m-d H:i:s', strtotime('+8 hours', strtotime(date('Y-m-d H:i:s'))));
				$data['auto-post']['date'] = $new_task_date;
				update_option('cron-job', json_encode($data));
				auto_add_games($data);
			} else { //More than 4 days inactive
				echo 'remove';
				unset($data['auto-post']);
				update_option('cron-job', json_encode($data));
			}
		} else {
			if(!defined('CRON')){
				echo 'on the way';
			}
		}
	} else {
		//Inactive
	}
}

function auto_add_games($data){
	if(!ADMIN_DEMO){
		$data['auto-post']['last-status'] = 'null';
		$url = 'https://api.cloudarcade.net/fetch-auto.php?action=fetch&code='. check_purchase_code();
		$url .= '&data='.json_encode($data['auto-post']['list']);
		$url .= '&ref='.DOMAIN.'&v='.VERSION;
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$curl = curl_exec($ch);
		curl_close($ch);
		$game_data = json_decode($curl, true);
		foreach ($game_data as $a => $b) {
			foreach ($b as $item) {
				$item['tags'] = '';
				x_add_game($item);
			}
		}
	}
}

function x_add_game($p){
	global $game_count;
	if($game_count >= LIMIT){
		return;
	}
	$ref = '';
	if(isset($p['ref'])) $ref = $p['ref'];
	$p['description'] = html_purify($p['description']);
	$p['instructions'] = html_purify($p['instructions']);
	$redirect = 0;
	if(isset($p['redirect'])){
		$redirect = $p['redirect'];
	}
	if(isset($p['slug'])){
		$slug = esc_slug($p['slug']);
	} else {
		$slug = esc_slug(strtolower(str_replace(' ', '-', basename($p["title"]))));
	}
	$p['slug'] = $slug;
	$game = new Game;
	$check=$game->getBySlug($slug);
	if(is_null($check)){
		$game_count++;
		if($ref != 'upload'){
			if(IMPORT_THUMB){
				import_thumb($p['thumb_2'], $slug);
				$name = basename($p['thumb_2']);
				$p['thumb_2'] = '/thumbs/'.$slug.'-'.$name;
				//
				import_thumb($p['thumb_1'], $slug);
				$name = basename($p['thumb_1']);
				$p['thumb_1'] = '/thumbs/'.$slug.'-'.$name;
				if( SMALL_THUMB ){
					$output = pathinfo($p['thumb_2']);
					$p['thumb_small'] = '/thumbs/'.$slug.'-'.$output['filename'].'_small.'.$output['extension'];
					imgResize(substr($p['thumb_2'], 1), 160, 160, $slug);
				}
			}
		}
		$game->storeFormValues( $p );
		$game->insert();
		$status='added';
		//
		$cats = commas_to_array($p['category']);
		if(is_array($cats)){ //Add new category if not exist
			$length = count($cats);
			for($i = 0; $i < $length; $i++){
				$p['name'] = $cats[$i];
				$category = new Category;
				$exist = $category->isCategoryExist($p['name']);
				if($exist){
				  //
				} else {
					unset($p['slug']);
					$p['description'] = '';
					$category->storeFormValues( $p );
					$category->insert();
				}
				$category->addToCategory($game->id, $category->id);
			}
		}
	}
	else{
		$status='exist';
	}
}

function import_thumb($url, $game_slug){
	if($url) {
		if (!file_exists('thumbs')) {
			mkdir('thumbs', 0777, true);
		}
		$name = basename($url);
		$new = 'thumbs/'.$game_slug.'-'.$name;
		compressImage($url, $new , COMPRESSION_LEVEL);
	}
}
function compressImage($source, $destination, $quality) {
	$info = getimagesize($source);
	if ($info['mime'] == 'image/jpeg') 
	$image = imagecreatefromjpeg($source);
	elseif ($info['mime'] == 'image/gif') 
	$image = imagecreatefromgif($source);
	elseif ($info['mime'] == 'image/png') 
	$image = imagecreatefrompng($source);

	if ($info['mime'] == 'image/png'){
		imageAlphaBlending($image, true);
		imageSaveAlpha($image, true);
		imagepng($image, $destination, 9);
	} else {
		imagejpeg($image, $destination, $quality);
	}
}

?>