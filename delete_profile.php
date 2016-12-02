<?php
session_start();
require_once(__DIR__.'/function.php');
try{	
	$dbh = db_connect($config);
	$active = setSessionUser($dbh);
	if(!$active){
		header('Location: login.php', TRUE, 303); // Если не запущена сессия, то переходим на старницу авторизации
	}else{	
			if(empty($_GET['user'])){
				$user_delete = $_SESSION['login']; // Если не передан параметром логин пользователя для удаления, то удалять будем свой собственный профиль
			}else{
				$user_delete = trim(strip_tags($_GET['user'])); // Если передан, то записываем в переменную $user
			}
			
			if(isAccess('10') || $user == $_SESSION['login']){
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
							$data_delete_user['login'] = $user_delete;
							$sth_delete_user -> execute($data_delete_user);
							if($user_delete == $_SESSION['login']){
								session_unset();
								session_destroy();
								header('Location: index.php', TRUE, 303);
							}elseif(isAccess('10')){
								header('Location: admin.php', TRUE, 303);
							}
						}
					}
				
				}
			}else{ // Если страница не твоя или ты не администратор, то выводим предупреждение
				$danger = "<p class='alert'> У Вас не достаточно прав для удаления пользователя!</p><p class='button'><a href='logout.php'>Выйти</a></p><p class='button'><a href='index.php'>Главная</a></p>";
				$flag = TRUE;
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
	 <h1>Для удаления профиля введите пароль</h1>
	 <?php echo $danger; 
	 if(!$flag){?>
	 <div class="form">
		 	<form action="delete_profile.php?user=<?php echo $user_delete; ?>" method="post">
		 	<p><input type="password" name="pass1">
		 	<label>Пароль</label></p>
		 	<p><input type="password"name="pass2">
		 	<label>Подтверждение пароля</label></p>
		 	<p><input type="submit" value="Удалить профиль с сайта"></p>
		 	</form>
	</div>
	<?php
	} 
	?>
	 </body>
</html>
<?php
} catch (PDOException $e) {
	echo $e->getMessage();
}