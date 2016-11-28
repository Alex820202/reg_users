<?php
session_start();
require_once(__DIR__.'/function.php');
try{	
	$dbh = db_connect($config);
	$active = setSessionUser($dbh);
	if(!$active){
		header('Location: login.php', TRUE, 303);
	}else{
		if(!empty($_POST)){
			if($_POST['pass1'] != $_POST['pass2']){
				$danger = "<p class='alert'> Введенные пароли не совпадают!</p>";
			}else{
				$active_user = $_SESSION['login'];
				$user = userDataByLogin($active_user, $dbh);
				$pass = saltPass($active_user, $_POST['pass1'], $user['salt']);
				if($pass != $user['password']){
					$danger = "<p class='alert'> Не правильный пароль пользователя!</p>";
				}else{
					$sql_delete_user = 'DELETE FROM users WHERE login=:login';
					$sth_delete_user = $dbh -> prepare($sql_delete_user);
					$data['login'] = $active_user;
					$sth_delete_user -> execute($data);
					session_unset();
					session_destroy();
					header('Location: index.php', TRUE, 303);
				}
			}
		}
	}
	?>
<!DOCTYPE html>
<html>
 <head>
   <title>Удаление профиля</title>
   <link rel="stylesheet" href="style.css">
   <meta charset="utf-8">
 </head>
	 <body>
	 <h1>Для удаления своего профиля введите пароль</h1>
	 <?php echo $danger; ?>
	 <div class="form">
		 	<form action="delete_profile.php" method="post">
		 	<p><input type="password" name="pass1">
		 	<label>Пароль</label></p>
		 	<p><input type="password"name="pass2">
		 	<label>Подтверждение пароля</label></p>
		 	<p><input type="submit" value="Удалить профиль с сайта"></p>
		 	</form>
	</div>
	 </body>
</html>
<?php
} catch (PDOException $e) {
	echo $e->getMessage();
}