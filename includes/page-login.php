<?php

if(is_login()){
	$user_data = get_user($_POST['username']);
	if($user_data['role'] === 'admin'){
		header('Location: '.DOMAIN.'admin/dashboard.php');
		return;
	} else {
		header('Location: '.get_permalink('user', $_SESSION['username']));
		return;
	}
}

$errors = array();

if (defined('GOOGLE_LOGIN')){
	if(isset($_POST['credential'])){
		$payload = json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $_POST['credential'])[1]))), true);
		if(isset($payload['sub'])){
			$username = str_replace(' ', '_', $payload['name']);
			$user_data = get_user($username);
			if(!$user_data){
				//User not exist
				//Register new user
				$user = new User;
				$_POST['username'] = $username;
				$_POST['password'] = password_hash($payload['sub'], PASSWORD_DEFAULT);
				$_POST['email'] = $payload['email'];
				$_POST['birth_date'] = date('Y-m-d');
				$_POST['gender'] = 'unset';
				$user->storeFormValues($_POST);
				$user->insert();
			}
			//
			$_POST['username'] = $username;
			$_POST['password'] = $payload['sub'];
			$_POST['login'] = true;
			$_POST['remember'] = true;
		}
	}
}

if ( isset( $_POST['login'] ) ) {
	$user_data = get_user($_POST['username']);
	if($user_data){
		if(password_verify($_POST['password'], $user_data['password'])){
			$_SESSION['username'] = $_POST['username'];

			if(isset($_POST['remember'])){
				CA_Auth::insert(str_encrypt($_SESSION['username'], 'f'));
			}

			if($user_data['role'] === 'admin'){
				header('Location: '.DOMAIN.'admin/dashboard.php');
				update_login_history('success');
				return;
			} else {
				header('Location: '.get_permalink('user', $_SESSION['username']));
				return;
			}
		}
	}
	$errors[] = _t('Incorrect username or password.');
}

if (isset($_POST['login'])) {
	$timer            = time() - 30;
	$ip_address      = getIpAddr();
	// Getting total count of hits on the basis of IP
	$conn = open_connection();
	$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM loginlogs WHERE TryTime > :timer and IpAddress = :ip_address";
	$st = $conn->prepare($sql);
	$st->bindValue(":timer", $timer, PDO::PARAM_INT);
	$st->bindValue(":ip_address", $ip_address, PDO::PARAM_STR);
	$st->execute();
	$sql = "SELECT FOUND_ROWS() AS total_count";
	$totalRows = $conn->query($sql)->fetch();
	$total_count     = $totalRows[0];
	if ($total_count == 10) {
		$errors[] = _t('To many failed login attempts. Please login after 30 sec.');
	} else {
		$total_count++;
		$rem_attm = 10 - $total_count;
		if ($rem_attm == 0) {
			$errors[] = _t('To many failed login attempts. Please login after 30 sec.');
		} else {
			$errors[] = _t('%a attempts remaining.', $rem_attm);
		}
		$try_time = time();;
		$sql = "INSERT INTO loginlogs(IpAddress,TryTime) VALUES(:ip_address, :try_time)";
		$st = $conn->prepare($sql);
		$st->bindValue(":ip_address", $ip_address, PDO::PARAM_STR);
		$st->bindValue(":try_time", $try_time, PDO::PARAM_INT);
		$st->execute();
	}
}

function update_login_history($status = 'null'){
	$ip_address = getIpAddr();
	$data = array(
		'username' => $_POST['username'],
		'password' => '***',
		'date' => date("Y-m-d H:i:s"),
		'status' => $status,
		'agent' => 'null',
		'country' => 'null',
		'city' => 'null',
	);
	$ip_info = false;//@json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=".$ip), true);
	if($ip_info){
		$data['country'] = $ip_info['geoplugin_countryName'];
		$data['city'] = $ip_info['geoplugin_city'];
	}
	if($_SERVER['HTTP_USER_AGENT']){
		$data['agent'] = $_SERVER['HTTP_USER_AGENT'];
	}
	$conn = open_connection();
	$sql = "INSERT INTO login_history(ip, data) VALUES(:ip_address, :data)";
	$st = $conn->prepare($sql);
	$st->bindValue(":ip_address", $ip_address, PDO::PARAM_STR);
	$st->bindValue(":data", json_encode($data), PDO::PARAM_STR);
	$st->execute();

	$sql = "SELECT * FROM login_history";
	$st = $conn->prepare($sql);
	$st->execute();
	$count = $st->rowCount();
	if($count > 100){
		$sql = "DELETE FROM login_history ORDER BY id ASC LIMIT 10";
		$st = $conn->prepare($sql);
		$st->execute();
	}
}

?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Login | <?php echo SITE_TITLE ?></title>
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
		<link rel="stylesheet" type="text/css" href="<?php echo DOMAIN ?>admin/style/bootstrap.min.css">
		<!-- Material Design Bootstrap -->
		<link href="<?php echo DOMAIN ?>vendor/mdbootstrap/mdb.min.css" rel="stylesheet">
		<!-- Font Awesome icons (free version)-->
		<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
		<link rel="stylesheet" type="text/css" href="<?php echo DOMAIN ?>admin/style/admin.css">
		<?php
		if(file_exists( ABSPATH . TEMPLATE_PATH . '/css/custom.css')){
			echo '<link rel="stylesheet" type="text/css" href="'.get_template_path().'/css/custom.css">';
		} elseif(file_exists( ABSPATH . TEMPLATE_PATH . '/style/custom.css')){
			echo '<link rel="stylesheet" type="text/css" href="'.get_template_path().'/style/custom.css">';
		}
		if(defined('GOOGLE_LOGIN')){
			echo '<script src="https://accounts.google.com/gsi/client" async defer></script>';
		}
		?>
	</head>
	<body class="login-body">
		<div class="login-container">
			<div class="login-form">
				<div class="container">
					<div class="login-logo text-center">
						<img src="<?php echo DOMAIN ?>images/login-logo.png">
					</div>
					<form action="<?php echo '/'.SUB_FOLDER ?>admin.php?action=login" method="POST">
						<?php
						if(count($errors) > 0){
							foreach ($errors as $msg) {
								echo '<div class="alert alert-warning" role="alert">'.$msg.'</div>';
							}
						}
						?>
						<input type="hidden" name="login" value="true" />
						<div class="form-group">
							<input type="text" id="username" name="username" placeholder="<?php _e('Username') ?>" class="form-control" value="" required>
						</div>
						<div class="form-group">
							<input type="password" id="password" name="password" autocomplete="new-password" placeholder="<?php _e('Password') ?>" class="form-control" value="" type="password" required>
						</div>
						<div class="form-check">
							<input type="checkbox" class="form-check-input" name="remember" id="remember-me" checked>
							<label class="form-check-label" for="remember-me"><?php _e('Remember me') ?></label>
						</div>
						<br>
						<button type="submit" class="btn btn-info btn-block"><?php _e('Login') ?></button>
						<?php if(defined('GOOGLE_LOGIN')){
							render_google_login_btn();
						} ?>
						<?php if($options['user_register'] === 'true'){ ?>
							<br>
							<div class="text-center"><?php _e('Or') ?> <a href="<?php echo get_permalink('register') ?>"><?php _e('Register') ?></a></div>
						<?php } ?>
						<div class="text-center mt-3"><a href="<?php echo DOMAIN ?>">< <?php _e('Back to Home') ?></a></div>
					</form>
				</div>
			</div>
		</div>
		<script type="text/javascript" src="<?php echo DOMAIN ?>js/jquery-3.5.1.min.js"></script>
		<!-- MDB core JavaScript -->
		<script type="text/javascript" src="<?php echo DOMAIN ?>vendor/mdbootstrap/mdb.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jsrsasign/8.0.20/jsrsasign-all-min.js"></script>
	</body>
</html>