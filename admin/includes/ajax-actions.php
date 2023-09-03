<?php

require( '../../config.php' );
require( '../../init.php' );

if(isset($_POST['action'])){
	$action = $_POST['action'];

	$super_user = false;
	if( $login_user && USER_ADMIN && !ADMIN_DEMO ){
		$super_user = true;
	}

	if($action == 'save_widgets_position'){
		$data = $_POST['data'];
		if( $super_user ){
			update_option('widgets', json_encode($data));
			echo 'ok';
		}
	} elseif($action == 'update_widget'){
		$data = $_POST['data'];
		if( $super_user ){
			$stored_widgets = json_decode(get_option('widgets'), true);
			
			foreach ($stored_widgets as $key => $item) {
				if($key == $_POST['parent']){
					$stored_widgets[$key][(int)$_POST['index']] = $data;
					break;
				}
			}

			update_option('widgets', json_encode($stored_widgets));
			echo 'ok';
		}
	} elseif($action == 'delete_widget'){
		if( $super_user ){
			$stored_widgets = json_decode(get_option('widgets'), true);
			
			foreach ($stored_widgets as $key => $item) {
				if($key == $_POST['parent']){
					unset($stored_widgets[$key][(int)$_POST['index']]);
					if(count($stored_widgets[$key])){
						$stored_widgets[$key] = array_values($stored_widgets[$key]);
					}
					break;
				}
			}

			update_option('widgets', json_encode($stored_widgets));
			echo 'ok';
		}
	} elseif($action == 'check_theme_updates'){
		if( $super_user ){
			$themes = [];
			$dirs = scan_folder('content/themes/');
			foreach ($dirs as $dir) {
				$json_path = ABSPATH . 'content/themes/' . $dir . '/info.json';
				if(file_exists( $json_path )){
					$theme = json_decode(file_get_contents( $json_path ), true);
					$themes[$dir] = array(
						'name' => $theme['name'],
						'version' => $theme['version']
					);
				}
			}
			$update_availabe = get_option('updates');
			if(is_null($update_availabe)){
				$update_availabe = [];
			} else {
				$update_availabe = json_decode($update_availabe, true);
			}
			$url = 'https://api.cloudarcade.net/themes/fetch.php?action=check&code='. check_purchase_code();
			$url .= '&data='.urlencode(json_encode($themes));
			$url .= '&ref='.DOMAIN.'&v='.VERSION;
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$curl = curl_exec($ch);
			curl_close($ch);
			echo $url;
			if($curl != ''){
				$update_list = json_decode($curl, true);
				if(count($update_list)){
					if(!isset($update_availabe['themes'])){
						$update_availabe['themes'] = [];
					}
					if(json_encode($update_list) != json_encode($update_availabe['themes'])){
						$update_availabe['themes'] = $update_list;
						update_option('updates', json_encode($update_availabe));
					}
				}
				echo 'ok';
			} else {
				if(!is_null($update_availabe) && count($update_availabe)){
					if(isset($update_availabe['themes'])){
						unset($update_availabe['themes']);
						update_option('updates', json_encode($update_availabe));
					}
				}
				echo 'ok';
			}
		}
	} elseif($action == 'update_alert'){
		if( $super_user ){
			$update_availabe = get_option('updates');
			
			if(is_null($update_availabe)){
				$update_availabe = [];
			} else {
				$update_availabe = json_decode($update_availabe, true);
			}
			
			$update_availabe[$_POST['type']] = true;

			update_option('updates', json_encode($update_availabe));
			echo 'ok';
		}
	} elseif($action == 'unset_update_alert'){
		if( $super_user ){
			$update_availabe = get_option('updates');
			
			if(is_null($update_availabe)){
				$update_availabe = [];
			} else {
				$update_availabe = json_decode($update_availabe, true);
			}

			if(isset($update_availabe[$_POST['type']])){
				unset($update_availabe[$_POST['type']]);
				update_option('updates', json_encode($update_availabe));
			}
			echo 'ok';
		}
	} elseif($action == 'get_plugin_list'){
		//Used for plugin updates
		if( $super_user ){
			require_once('../../includes/plugin.php');
			if(count($plugin_list)){
				$list = [];
				foreach($plugin_list as $plugin){
					if($plugin['author'] == 'RedFoc' || $plugin['author'] == 'CloudArcade'){
						array_push($list, array(
							'dir_name' => $plugin['dir_name'],
							'version' => $plugin['version']
						));
					}
				}
				$result = array(
					'plugins' => json_encode($list),
					'code' => check_purchase_code(),
					'version' => VERSION,
					'domain' => DOMAIN
				);
				echo json_encode($result);
			}
		}
	} elseif($action == 'update_plugin'){
		if( $super_user ){
			$target = ABSPATH.'content/plugins/tmp_plugin.zip';
			file_put_contents($target, file_get_contents($_POST['path'].'.zip'));
			if(file_exists($target)){
				$zip = new ZipArchive;
				$res = $zip->open($target);
				if ($res === TRUE) {
					$zip->extractTo(ABSPATH.'content/plugins/');
					$zip->close();
					$status = 'success';
					$info = 'Plugin installed!';
				} else {
				  echo 'doh!';
				}
				unlink($target);
				echo 'ok';
			} else {
				echo 'not found';
			}
		}
	}
}

?>