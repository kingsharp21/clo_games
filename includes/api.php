<?php

if (session_status() == PHP_SESSION_NONE) {
	session_start();
}

require( '../config.php' );
require( '../init.php' );

if(isset($_POST['action'])){
	$score = null;
	if($_POST['action'] === 'submit'){
		if($login_user){ //Only logged in user
			$user_id = $login_user->id;
			if(isset($_POST['value']) && isset($_POST['ref'])){
				$score = $_POST['value'];
				$score = base64_decode($score);
				$score = $score*1.33;
				if (strpos($score, '.')) { 
				    //invalid
				} else {
					$game = Game::getBySlug($_POST['ref']);
					if($game){
						$game_id = $game->id;
						$conn = open_connection();
						$sql = 'SELECT score FROM scores WHERE user_id = :user_id AND game_id = :game_id LIMIT 1';
						$st = $conn->prepare($sql);
						$st->bindValue(":game_id", $game_id, PDO::PARAM_INT);
						$st->bindValue(":user_id", $user_id, PDO::PARAM_INT);
						$st->execute();
						$row = $st->fetch();
						if($row){ //Update existing data
							if($row['score'] < $score){
								$sql = 'UPDATE scores SET score = :score WHERE user_id AND game_id = :game_id = :user_id LIMIT 1';
								$st = $conn->prepare($sql);
								$st->bindValue(":game_id", $game_id, PDO::PARAM_INT);
								$st->bindValue(":user_id", $user_id, PDO::PARAM_INT);
								$st->bindValue(":score", $score, PDO::PARAM_INT);
								$st->execute();
							}
						} else {
							$sql = 'INSERT INTO scores (game_id, user_id, score) VALUES ( :game_id, :user_id, :score)';
							$st = $conn->prepare($sql);
							$st->bindValue(":game_id", $game_id, PDO::PARAM_INT);
							$st->bindValue(":user_id", $user_id, PDO::PARAM_INT);
							$st->bindValue(":score", $score, PDO::PARAM_INT);
							$st->execute();
						}
						//
						$login_user->xp += 10;
						$login_user->update_xp();
						//
						echo 'ok';
					}
				}
			} else {
				die('x');
			}
		}	
	} elseif ($_POST['action'] === 'get_current_user'){
		if($login_user){
			$user = array();
			$user['username'] = $login_user->username;
			$user['id'] = $login_user->id;
			$user['gender'] = $login_user->gender;
			$user['join_date'] = $login_user->join_date;
			$user['birth_date'] = $login_user->birth_date;
			echo json_encode($user);
		}
	} elseif ($_POST['action'] === 'get_scoreboard'){
		if(isset($_POST['conf'])){
			$config = json_decode($_POST['conf'], true);
			$type = $config['type'];
			$amount = 10;
			if(isset($config['amount'])){
				$amount = $config['amount'];
			}
			$sql = null;
			$game = Game::getBySlug($_POST['ref']);
			if(!$game){
				die();
			}
			$game_id = $game->id;
			if($type === 'top-all'){
				$sql = "SELECT * FROM scores ORDER by score DESC, created_date ASC LIMIT ".$amount;
			} elseif($type === 'top-all-day'){
				$sql = "SELECT * FROM scores WHERE created_date > DATE_SUB(NOW(), INTERVAL 1 DAY) ORDER by score DESC LIMIT ".$amount;
			} elseif($type === 'top-all-week'){
				$sql = "SELECT * FROM scores WHERE created_date > DATE_SUB(NOW(), INTERVAL 1 WEEK) ORDER by score DESC LIMIT ".$amount;
			} elseif($type === 'top-all-month'){
				$sql = "SELECT * FROM scores WHERE created_date > DATE_SUB(NOW(), INTERVAL 1 MONTH) ORDER by score DESC LIMIT ".$amount;
			} elseif($type === 'top'){
				$sql = "SELECT * FROM scores WHERE game_id = ".$game_id." ORDER by score DESC LIMIT ".$amount;
			} elseif($type === 'top-day'){
				$sql = "SELECT * FROM scores WHERE created_date > DATE_SUB(NOW(), INTERVAL 1 DAY) AND game_id = ".$game_id." ORDER by score DESC LIMIT ".$amount;
			} elseif($type === 'top-week'){
				$sql = "SELECT * FROM scores WHERE created_date > DATE_SUB(NOW(), INTERVAL 1 WEEK) AND game_id = ".$game_id." ORDER by score DESC LIMIT ".$amount;
			} elseif($type === 'top-month'){
				$sql = "SELECT * FROM scores WHERE created_date > DATE_SUB(NOW(), INTERVAL 1 MONTH) AND game_id = ".$game_id." ORDER by score DESC LIMIT ".$amount;
			}
			if($sql){
				$conn = open_connection();
				$st = $conn->prepare($sql);
				$st->execute();
				//
				$row = $st->fetchAll(PDO::FETCH_ASSOC);
				$list = [];
				foreach($row as $item){
					$item['game_title'] = Game::getById($item['game_id'])->title;
					$item['username'] = User::getById($item['user_id'])->username;
					array_push($list, $item);
				}
				echo json_encode($list);
			}	
		}
	} elseif ($_POST['action'] === 'load_ad'){
		if(isset($_POST['value'])){
			$tags = get_option('ads-manager');
			if($tags){
				$tags = json_decode($tags, true);
				$selected = null;
				foreach ($tags as $tag => $item) {
					if(strtolower($_POST['value']) == strtolower($tag)){
						$selected = $item;
						$selected['type'] = strtolower($tag);
						break;
					}
				}
				if(!$selected){
					foreach ($tags as $tag => $item) {
						if($item['default']){
							$selected = $item;
							$selected['type'] = strtolower($tag);
							break;
						}
					}
				}
				if($selected['type'] == 'banner'){
					$selected['delay'] = 5;
				}
				echo json_encode($selected);
			} else {
				echo '{"error": "Ads Manager plugin not installed."}';
			}
		}
	}
}

?>