<?php
session_start();
require_once(__DIR__.'/function.php');
try{
	
if(!empty($_POST)){
	$dbh = db_connect($config);
	$result_login = userDataByLogin($_POST['login'], $dbh);
	if(!empty($result_login)){
		$pass = saltPass($result_login['login'],$_POST['password'],$result_login['salt']);
		if($pass == $result_login['password']){
			$_SESSION['auth'] = 'true';
			$_SESSION['login'] = $result_login['login'];
			$_SESSION['banned'] = $result_login['banned'];
			$_SESSION['status'] = $result_login['status'];
			$_SESSION['data_end_ban'] = $result_login['data_end_ban'];
			if($_POST['auth_long']){
				$cookie = salt();
				setUserCookie($result_login['login'], $cookie, $dbh);
				$t = time();
				setcookie('auth_long', $cookie, $t+10*365*24*3600);
				setcookie('login', $result_login['login'], $t+10*365*24*3600);
				}
				setcookie('last_login_date', $t, $t+10*365*24*3600);
				
			header('Location: index.php', TRUE, 303);
			}else{
				$danger = "<p class='alert'>Не правильный пароль!</p>";
			}
		}else{
			$danger = "<p class='alert'>Нет такого пользователя!</p>";
		}
	
	
}
?>
<!DOCTYPE html>
<html>
 <head>
   <title>Login</title>
   <link rel="stylesheet" href="style.css">
   <meta charset="utf-8">
 </head>
	 <body>
		 <div class="form">
		 <?php echo $danger;?>
			 <form action="login.php" method="post">
			 	 <p>
				 	 <input type="text" name="login" value="<?php echo $_POST['login']; ?>">
				 	 <label>Логин</label>
			 	 </p>
			 	 <p>
			 	 	<input  type="password" name="password">
			 	 	<label>Пароль</label>
			 	 </p>
			 	 <p>
			 	 	<input type="checkbox" name="auth_long">
			 	 </p>
			 	 <p>
			 	 	<input type="submit" value="Авторизоваться">
			 	 </p>
			 </form>
		 
		 </div>
	 </body>
 </html>
 <?php
 } catch (PDOException $e) {
	echo 'Ошибка базы данных: <br>'.$e->getMessage();
}