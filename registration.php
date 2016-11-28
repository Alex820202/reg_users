<?php
try{
	require_once(__DIR__.'/function.php');
	$config = __DIR__.'/config.php';
	$dbh = db_connect($config);
	
	if(!empty($_POST) && $_GET['flag'] != 4){
		$user = user_data();
		$val = validUser($user, $language);
		if($val == 100){
			regUser($dbh, $user);	
		}
	}
	
?>	

<!DOCTYPE html>
<html>
 <head>
   <title>Registration users</title>
   <link rel="stylesheet" href="style.css">
   <meta charset="utf-8">
 </head>
 <body>
 <div class="form">
 <?php 
 
 	if($_GET['flag'] == 1){
		echo '<p class="alert">Не совпадают пароли!</p>';
		include __DIR__.'/form.html';
	}elseif($_GET['flag'] == 3){
		echo '<p class="alert">Такой <b>Логин</b> уже существует!</p>';
		include __DIR__.'/form.html';
	}elseif($_GET['flag'] == 2){
		echo 'Вы успешно зарегистрированы, Ваши учетные данные будут высланы на e-mail, указанный при регистрации!';
	}else{
		include __DIR__.'/form.html';
	}
 	
?>
</div>
 </body>
 </html>
<?php 
} catch (PDOException $e) {
	echo 'Ошибка базы данных!<br>'.$e->getMessage();
}

?>