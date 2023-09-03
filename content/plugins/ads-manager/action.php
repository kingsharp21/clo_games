<?php

session_start();

require_once( '../../../config.php' );
require_once( '../../../init.php' );

if(isset($_POST['action'])){
	if($_POST['action'] == 'report'){
		$reports = get_option('game-reports');
		if(!$reports){
			$reports = [];
		} else {
			$reports = json_decode($reports, true);
		}
		$arr = array(
			'type' => $_POST['type'],
			'comment' => substr(strip_tags($_POST['comment']), 0, 200),
			'date' => date('Y-m-d'),
			'game_id' => (int)$_POST['id'],
			'id' => time()
		);
		array_unshift($reports, $arr);
		update_option('game-reports', json_encode($reports));
	} elseif($_POST['action'] == 'delete'){
		if(USER_ADMIN){
			$reports = get_option('game-reports');
			$reports = json_decode($reports, true);
			$index = 0;
			$pref = count($reports);
			foreach ($reports as $item) {
				if($item['id'] == (int)$_POST['id']){
					unset($reports[$index]);
					echo 'deleted';
					break;
				}
				$index++;
			}
			$reports = array_values($reports);
			update_option('game-reports', json_encode($reports));
		}
	}
}

?>