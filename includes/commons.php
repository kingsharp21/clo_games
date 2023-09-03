<?php

defined('ABSPATH') or die('abcd commons');

function get_all_categories(){
	$data = Category::getList();
	return $data['results'];
}
function get_user($username){
	$conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql = 'SELECT * FROM users WHERE username = :username';
	$st = $conn->prepare( $sql );
	$st->bindValue( ":username", $username, PDO::PARAM_STR );
	$st->execute();
	$row = $st->fetch();
	$conn = null;
	if ( $row ) return $row;
	return false;
}
function is_login(){
	if(isset( $_SESSION['username'] )){
		return true;
	} else {
		return false;
	}
}
function show_logout(){
	if(is_login()){
		echo '<a href="'.DOMAIN.'admin.php?action=logout"> Log out </a>';
	}
}
function get_permalink($type, $slug = 0, $arrs = []){
	$type = convert_to_custom_path($type);
	$params = '';
	if(count($arrs)){
		foreach ($arrs as $key => $value) {
			if( PRETTY_URL ){
				$params .= '/'.$value;
			} else {
				$params .= '&'.$key.'='.$value;
			}
		}
		if($slug == ''){
			$params = substr($params, 1);
		}
	}
	if($type == 'game'){
		if( PRETTY_URL ){
			return DOMAIN . 'game/' . $slug;
		} else {
			return DOMAIN . 'index.php?viewpage=game&slug=' . $slug . $params;
		}
	} else if($type == 'archive'){
		if( PRETTY_URL ){
			return DOMAIN . 'archive/' . $slug;
		} else {
			return DOMAIN . 'index.php?viewpage=archive&slug=' . $slug . $params;
		}
	} else if($type == 'search'){
		if( PRETTY_URL ){
			return DOMAIN . 'search/' . $slug;
		} else {
			return DOMAIN . 'index.php?viewpage=search&key=' . $slug . $params;
		}
	} else if($type == 'category'){
		if( PRETTY_URL ){
			return DOMAIN . 'category/' . strtolower($slug) . $params;
		} else {
			return DOMAIN . 'index.php?viewpage=category&slug=' . strtolower($slug) . $params;
		}
	} else if($type == 'page'){
		if( PRETTY_URL ){
			return DOMAIN . 'page/' . $slug . $params;
		} else {
			return DOMAIN . 'index.php?viewpage=page&slug=' . $slug . $params;
		}
	} else {
		if( PRETTY_URL ){
			if(!$slug){
				$slug = '';
			}
			return DOMAIN . $type .'/' . $slug . $params;
		} else {
			if(!$slug){
				$slug = '';
			} else {
				$slug = '&slug='.$slug;
			}
			return DOMAIN . 'index.php?viewpage=' . $type . $slug . $params;
		}
	}
}
function get_small_thumb($game){
	$thumb = (isset($game->thumb_small) && $game->thumb_small != '' ? esc_url($game->thumb_small) : esc_url($game->thumb_2));
	if(substr($thumb, 0, 1) == '/'){
		$thumb = DOMAIN . substr($thumb, 1);
	}
	return $thumb;
}
function get_game_url($game){
	$url = esc_url($game->url);
	if(substr($url, 0, 7) == '/games/'){
		$url = DOMAIN . substr($url, 1);
	}
	return $url;
}
function commas_to_array($str){
	return preg_split("/\,/", $str);
}
function html_purify($html_content){
	require_once ABSPATH.'vendor/HTMLPurifier/HTMLPurifier.auto.php';
	$config = HTMLPurifier_Config::createDefault();
	$purifier = new HTMLPurifier($config);
	$clean_html = $purifier->purify($html_content);
	return $clean_html;
}
function esc_string($str){
	return filter_var($str, FILTER_SANITIZE_STRING);
}
function esc_int($int){
	return filter_var($int, FILTER_SANITIZE_NUMBER_INT);
}
function esc_url($str){
	return filter_var($str, FILTER_SANITIZE_URL);
}
function esc_slug($str){
	if(UNICODE_SLUG){
		return esc_unicode_slug($str);
	} else {
		$str = str_replace(' ', '-', $str);
		return strtolower(preg_replace('~[^A-Za-z0-9-_]~','', $str));
	}
}
function esc_unicode_slug($str){
	$str = str_replace(' ', '-', $str);
	$patterns = array('/\//','/\\\/','/:/','/=/','/&/','/\'/','/"/');
	return preg_replace($patterns, '', $str);
}
function imgResize($path, $rs_width=160, $rs_height=160, $slug = '') {
	$x = getimagesize($path);
	$width  = $x['0'];
	$height = $x['1'];
	switch ($x['mime']) {
	  case "image/gif":
		 $img = imagecreatefromgif($path);
		 break;
	  case "image/jpg":
	  case "image/jpeg":
		 $img = imagecreatefromjpeg($path);
		 break;
	  case "image/png":
		 $img = imagecreatefrompng($path);
		 break;
	}
	$img_base = imagecreatetruecolor($rs_width, $rs_height);
	if($x['mime'] == "image/png"){
		imageAlphaBlending($img_base, false);
		imageSaveAlpha($img_base, true);
	}
	imagecopyresampled($img_base, $img, 0, 0, 0, 0, $rs_width, $rs_height, $width, $height);
	$path_info = pathinfo($path);
	$output = $path_info['dirname'].'/'.$slug.'-'.$path_info['filename'].'_small.'.$path_info['extension'];
	switch ($path_info['extension']) {
	  case "gif":
		 imagegif($img_base, $output);  
		 break;
	case "jpg":
	case "jpeg":
		 imagejpeg($img_base, $output);
		 break;
	  case "png":
		 imagepng($img_base, $output, 9);
		 break;
	}
}
function check_purchase_code(){
	$conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql = 'SELECT * FROM options WHERE name = "purchase_code"';
	$st = $conn->prepare( $sql );
	$st->execute();
	$row = $st->fetch();
	$conn = null;
	if($row){
		if($row['value'] != ''){
			return $row['value'];
		} else {
			return false;
		}
	} else {
		return false;
	}
}
function get_admin_warning(){
	$results = [];
	if(!check_purchase_code() && !ADMIN_DEMO){
		array_push($results, 'Please provide your <b>Item Purchase code</b>. You can submit or update your Purchase code on site settings.');
	}
	if(URL_PROTOCOL == 'http://'){
		if(is_https()){
			array_push($results, 'You\'re using HTTPS but current config use HTTP, you can switch to HTTPS in Settings -> Advanced.');
		}
	}
	if(!check_writeable()){
		array_push($results, 'CloudArcade don\'t have permissions to modify files, uploaded files can\'t be saved and can\'t do backup or update. Change all folders and files CHMOD to 777 to fix this.');
	}
	if(!class_exists('ZipArchive')){
		array_push($results, '"ZipArchive" extension is missing or disabled. Can\'t do backup or update.');
	}
	if( !ini_get('allow_url_fopen') ) {
		array_push($results, '"allow_url_fopen" is disabled/off, please turn it on. Can\'t import thumbnails.'.ini_get('allow_url_fopen'));
	}
	return $results;
}
function is_https() {
	if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
		return true;
	} elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on') {
		return true;
	} else {
		return false;
	}
}
function check_writeable(){
	if (is_writable('../config.php') && is_writable('../site-settings.php') && is_writable('../admin/upload.php')) {
		return true;
	} else {
		return false;
	}
}
function get_cur_url(){
	if(SUB_FOLDER && SUB_FOLDER != ''){
		return DOMAIN . substr(str_replace(SUB_FOLDER, '', $_SERVER['REQUEST_URI']), 1);
	} else {
		return DOMAIN . substr($_SERVER['REQUEST_URI'], 1);
	}
}
function get_rating($type, $game){
	if($type == '5'){
		if($game->upvote+$game->downvote > 0){
			return round(($game->upvote/($game->upvote+$game->downvote))*5);
		} else {
			return 0;
		}
	} else if($type == '5-decimal'){
		if($game->upvote+$game->downvote > 0){
			return number_format(($game->upvote/($game->upvote+$game->downvote))*5, 1);
		} else {
			return 0;
		}
	}
}
function is_user_admin($username){
	$conn = open_connection();
	$sql = "SELECT * FROM users WHERE username = :username";
	$st = $conn->prepare($sql);
	$st->bindValue(":username", $username, PDO::PARAM_STR);
	$st->execute();
	$row = $st->fetch();
	if ($row) {
		if($row['role'] === 'admin'){
			return true;
		}
	}
	return false;
}
function scan_folder($path){
	$array = [];

	$dirs = scandir( ABSPATH . $path);
	$dirs = array_diff($dirs, array('.', '..'));

	foreach ($dirs as $dir) {
		if(is_dir( ABSPATH . $path . $dir)){
			if($dir != '.' || $dir != '..'){
				array_push($array, $dir);
			}
		}
	}

	return $array;
}
function scan_files($path){
	$directory = new \RecursiveDirectoryIterator(ABSPATH.$path);
	$iterator = new \RecursiveIteratorIterator($directory);
	$files = array();
	foreach ($iterator as $info) {
		if (is_file($info->getPathname())) {
			$files[] = str_replace(ABSPATH, '', $info->getPathname());
		}
	}
	return $files;
}
function delete_files($target) {
	if(is_dir($target)){
		$files = glob( $target . '*', GLOB_MARK );

		foreach( $files as $file ){
			delete_files( $file );      
		}

		rmdir( $target );
	} elseif(is_file($target)) {
		unlink( $target );  
	}
}

function add_to_zip($source, $destination, $ignores = []) {
	if (extension_loaded('zip') && is_login()) {
		if (file_exists($source)) {
			$zip = new ZipArchive();
			if ($zip->open($destination, ZIPARCHIVE::CREATE)) {
				if (is_dir($source)) {
					$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST);
					foreach ($files as $file) {

						$ignored = false;

						foreach ($ignores as $ignore) {
							if(strpos($file, $ignore)){
								$ignored = true;
								break;
							}
						}

						if($ignored){
							continue;
						}

						if (is_dir($file)) {
							if(count(glob("$file/*")) > 0){ //If folder not empty
								$zip->addEmptyDir(str_replace($source . DIRECTORY_SEPARATOR, '', $file . DIRECTORY_SEPARATOR));
							}
						} else if (is_file($file)) {
							$zip->addFromString(str_replace($source . DIRECTORY_SEPARATOR, '', $file), file_get_contents($file));
						}
					}
				} else if (is_file($source)) {
					$zip->addFromString(basename($source), file_get_contents($source));
				}
			}
			return $zip->close();
		}
	}
	return false;
}

function getIpAddr() {
	if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
		$ipAddr = $_SERVER["HTTP_CF_CONNECTING_IP"];
	} elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
		$ipAddr = $_SERVER['HTTP_CLIENT_IP'];
	} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$ipAddr = strtok($_SERVER['HTTP_X_FORWARDED_FOR'], ',');
	} else {
		$ipAddr = $_SERVER['REMOTE_ADDR'];
	}
	if(strlen($ipAddr) > 16){
		$ipAddr = '0.0.0.0';
	}
	return $ipAddr;
}

function get_user_avatar($username = null){
	global $login_user;
	$user;
	if(!$username){
		if($login_user){
			$username = $login_user->username;
			$user = $login_user;
		}
	} else {
		$cur_user = User::getByUsername($username);
		if($cur_user){
			$user = $cur_user;
		}
	}
	if($user){
		if(file_exists(ABSPATH.'images/avatar/'.$username.'.png')){
			return DOMAIN.'images/avatar/'.$username.'.png';
		} elseif($user->avatar){
			return DOMAIN.'images/avatar/default/'.$user->avatar.'.png';
		}
	}
	return DOMAIN.'images/default_profile.png';
}

$lang_data = [];

function load_language($type){
	global $lang_data;
	global $options;
	if($type === 'index'){
		$file = TEMPLATE_PATH.'/locales/'.$options['language'].'.json';
	} elseif($type === 'admin'){
		$file = ABSPATH.'locales/'.$options['language'].'.json';
	}
	if(file_exists($file)){
		$lang_data = json_decode(file_get_contents($file), true);
	}
}

function translate($str, $val1 = null, $val2 = null){
	global $lang_data;
	$translated = $str;
	if(isset($lang_data[$str])){
		$translated = $lang_data[$str];
	}
	if(!is_null($val1)){
		$translated = str_replace('%a', $val1, $translated);
	}
	if(!is_null($val2)){
		$translated = str_replace('%b', $val2, $translated);
	}
	return $translated;
}

function _t($str, $val1 = null, $val2 = null){
	return translate($str, $val1, $val2);
}

function _e($str, $val1 = null, $val2 = null){
	echo translate($str, $val1, $val2);
}

function get_custom_path($page_name){
	global $options;
	if(isset($options['custom_path']) && $options['custom_path']){
		if(isset($options['custom_path'][$page_name])){
			return $options['custom_path'][$page_name];
		}
	}
	return $page_name;
}

function convert_to_custom_path($page_name){
	global $options;
	if(isset($options['custom_path']) && $options['custom_path']){
		$custom_name = array_search($page_name, $options['custom_path']);
		if($custom_name){
			return $custom_name;
		}
	}
	return $page_name;
}

function str_encrypt($str, $key){
	$cipher = "AES-128-CTR";
	$ivlen = openssl_cipher_iv_length($cipher);
	$iv = '1234567891011121';
	return openssl_encrypt($str, $cipher, $key, $options=0, $iv);
}

function str_decrypt($str, $key){
	$cipher = "AES-128-CTR";
	$ivlen = openssl_cipher_iv_length($cipher);
	$iv = '1234567891011121';
	return openssl_decrypt($str, $cipher, $key, $options=0, $iv);
}

function show_alert($message, $type, $btn = true){
	echo '<div class="alert alert-'.$type.' alert-dismissible fade show" role="alert">'._t($message);
	if($btn){
		echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
	}
	echo '</div>';
}

function get_option($name){
	global $conn;
	$sql = "SELECT * FROM prefs WHERE name = :name";
	$st = $conn->prepare($sql);
	$st->bindValue(':name', $name, PDO::PARAM_STR);
	$st->execute();
	$row = $st->fetch();
	if($row){
		return $row['value'];
	} else {
		return null;
	}
}

function update_option($name, $value){
	global $conn;
	$sql = "SELECT id FROM prefs WHERE name = :name";
	$st = $conn->prepare($sql);
	$st->bindValue(':name', $name, PDO::PARAM_STR);
	$st->execute();
	$row = $st->fetch();
	if($row){
		$sql = "UPDATE prefs SET value = :value WHERE name = :name";
		$st = $conn->prepare($sql);
		$st->bindValue(':value', $value, PDO::PARAM_STR);
		$st->bindValue(':name', $name, PDO::PARAM_STR);
		$st->execute();
	} else {
		$sql = "INSERT INTO prefs (name, value) VALUES (:name, :value)";
		$st = $conn->prepare($sql);
		$st->bindValue(':value', $value, PDO::PARAM_STR);
		$st->bindValue(':name', $name, PDO::PARAM_STR);
		$st->execute();
	}
}

function register_sidebar( $args = array() ){
	global $registered_sidebars;

	$i = count( $registered_sidebars ) + 1;

	$id_is_empty = empty( $args['id'] );

	$defaults = array(
		'name'           => 'Sidebar X',
		'id'             => "sidebar-$i",
		'description'    => '',
	);

	$sidebar = merge_args($args, $defaults);

	$registered_sidebars[ $sidebar['id'] ] = $sidebar;

	return $sidebar['id'];
}

function merge_args($args, $defaults = array()){
	foreach ($args as $key => $value) {
		$defaults[$key] = $value;
	}
	return $defaults;
}

function widget_aside($name, $args = array()){
	global $stored_widgets;
	global $registered_sidebars;
	if(isset($registered_sidebars[$name])){
		if(isset($stored_widgets[$name])){
			$list = $stored_widgets[$name];
			if(count($list)){
				foreach ($list as $item) {
					$key = $item['widget'];
					$widget;
					if(widget_exists($item['widget'])){
						$widget = get_widget( $item['widget'], $item );
					} else {
						continue;
					}
					$widget->widget( $item );
				}
			}
		}
	}
}

function render_nav_children($array_menu, $args){
	$defaults = array(
		'no_ul'				=> false,
		'ul_id'				=> '',
		'ul_class'			=> 'dropdown-menu',
		'li_id'				=> '',
		'li_class'			=> 'nav-item-child',
		'a_class'			=> 'nav-link-child',
	);
 
	$args = merge_args( $args, $defaults );

	if(count($array_menu)){
		if(!$args['no_ul']){
			echo '<ul role="menu" ';
			echo !empty($args['ul_id']) ? ' id="'.$args['ul_id'].'"' : '';
			echo !empty($args['ul_class']) ? ' class="'.$args['ul_class'].'"' : '';
			echo '>';
		}
		foreach($array_menu as $menu){
			echo '<li';
			echo !empty($args['li_id']) ? ' id="'.$args['li_id'].'"' : '';
			echo !empty($args['li_class']) ? ' class="'.$args['li_class'].'"' : '';
			echo '>';
			echo '<a class="'.$args['a_class'].'" href="'.$menu['url'].'">';
			echo $menu['label'];
			echo '</a>';
			echo '</li>';
		}
		if(!$args['no_ul']){
			echo '</ul>';
		}
	}
}

function render_nav_menu($name = 'top_nav', $args = array()){
	$defaults = array(
		'container'			=> '',
		'container_id'		=> '',
		'container_class'	=> '',
		'no_ul'				=> false,
		'ul_id'				=> '',
		'ul_class'			=> '',
		'li_id'				=> '',
		'li_class'			=> 'nav-item',
		'li_class_parent'	=> 'nav-item-parent',
		'a_class'			=> 'nav-link',
		'after_parent'		=> '<i class="dropdown-icon fa fa-caret-down"></i>',
		'children'			=> array(),
	);
 
	$args = merge_args( $args, $defaults );

	$array_menu = nav_menu_array($name);

	if(count($array_menu)){
		if($args['container'] != ''){
			echo '<'.$args['container'];
			echo !empty($args['container_id']) ? ' id="'.$args['container_id'].'"' : '';
			echo !empty($args['container_class']) ? ' class="'.$args['container_class'].'"' : '';
			echo '>';
		}
		if(!$args['no_ul']){
			echo '<ul';
			echo !empty($args['ul_id']) ? ' id="'.$args['ul_id'].'"' : '';
			echo !empty($args['ul_class']) ? ' class="'.$args['ul_class'].'"' : '';
			echo '>';
		}
		foreach($array_menu as $menu){
			$parent_class = '';
			if(isset($menu['children'])){
				$parent_class = !empty($args['li_class_parent']) ? $args['li_class_parent'] : '';
			}
			echo '<li';
			echo !empty($args['li_id']) ? ' id="'.$args['li_id'].'"' : '';
			echo !empty($args['li_class']) ? ' class="'.$args['li_class'].' '.$parent_class.'"' : '';
			echo '>';
			if(isset($menu['children'])){
				$menu['url'] = '#';
			}
			echo '<a class="'.$args['a_class'].'" href="'.$menu['url'].'"';
			if(isset($menu['children'])){
				echo ' data-toggle="dropdown"';
			}
			echo '>';
			echo $menu['label'];
			if(isset($menu['children'])){
				echo $args['after_parent'];
			}
			echo '</a>';
			if(isset($menu['children'])){
				render_nav_children($menu['children'], $args['children']);
			}
			echo '</li>';
		}
		if(!$args['no_ul']){
			echo '</ul>';
		}
		if($args['container'] != ''){
			echo '</'.$args['container'].'>';
		}
	}
}

function nav_menu_array($name = 'top_nav'){
	function get_children($name, $parent_id = 0){
		global $conn;
		$items = [];
		$sql = "SELECT * FROM menus WHERE parent_id = :parent_id AND name = :name ORDER BY id ASC";
		$st = $conn->prepare($sql);
		$st->bindValue(":parent_id", $parent_id, PDO::PARAM_INT);
		$st->bindValue(":name", $name, PDO::PARAM_STR);
		$st->execute();
		$result = $st->fetchAll(PDO::FETCH_ASSOC);
		if (count($result)) {
			foreach ($result as $row) {
				$child = get_children($name, $row['id']);
				if($child){
					$row['children'] = $child;
				}
				$items[] = $row;
			}
		} else {
			$items = [];
		}
		return $items;
	}
	return get_children($name, 0);
}

function get_template_path(){
	return DOMAIN . TEMPLATE_PATH;
}

function get_category_icon($slug, $array = []){
	foreach ($array as $key => $item) {
		foreach ($item as $child) {
			if($child == $slug){
				return $key;
			}
		}
	}
	return 'other';
}

?>