<?php
session_start();
require_once(__DIR__.'/function.php');
$dbh = db_connect($config);
$active = setSessionUser($dbh);
if(!$active){
	header('Location: login.php', TRUE, 303);
}else{
	if($_SESSION['banned'] != 1){
		
		if(!empty($_POST)){
			if($_POST['new_password'] == $_POST['new_password1']){
				$active_user = $_SESSION['login'];
				$user = userDataByLogin($active_user, $dbh);
				$pass = saltPass($active_user, $_POST['new_password'], $user['salt']);
				if($user['password'] == $pass){
					$sql_pass = 'UPDATE users SET password=:password, salt=:salt WHERE login=:login';
					$sth_pass = $dbh -> prepare($sql_pass);
					$data['salt'] = salt();
					$data['password'] = saltPass($active_user, $_POST['new_password'], $data['salt']);
					$data['login'] = $active_user;
					$sth_pass -> execute($data);
					session_unset();
					session_destroy();
					header('Location: index.php', TRUE, 303);
				}
			}else{
				$danger = 'Новый пароль и его подтверждение не совпадают!';
			}
		}
	
	
?>
<!DOCTYPE html>
<html>
 <head>
   <title>Смена пароля</title>
   <link rel="stylesheet" href="style.css">
   <meta charset="utf-8">
 </head>
	 <body>
		 <div class="form">
		 <?php echo $danger; ?> 
		 	<form action="parol.php" method="post">
		 		<div class="button type">
		 			<input type="password" name="active_password" required>
		 			<label>Текущий пароль</label>
		 		</div>
		 		<div class="button type">
		 			<input type="password" name="new_password" required>
		 			<label>Новый пароль</label>
		 		</div>
		 		<div class="button type">
		 			<input type="password" name="new_password1" required>
		 			<label>Подтверждение пароля</label>
		 		</div>
		 		<div class="button type">
		 			<input type="submit" value="Сменить">
		 		</div>
		 	</form>
		 </div>
	 </body>
</html>

<?php
	}else{
			echo "<p class='alert'>".$_SESSION['login'].", Вы забанены до ".date("d.m.Y H:i:s", $_SESSION['data_end_ban'])."</p>";
			session_unset();
			session_destroy();
		}
}