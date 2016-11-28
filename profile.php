<?php
session_start();
require_once(__DIR__.'/function.php');
$dbh = db_connect($config);
$active = setSessionUser($dbh);
if(!$active){
	header('Location: login.php', TRUE, 303);
}else{
	
	if(!empty($_GET)){
		if($_GET['user'] == $_SESSION['login']){
			$option = TRUE;
		}else{
			$option = FALSE;
		}
		$active_user = trim(strip_tags($_GET['user']));
	}else{
		$active_user = $_SESSION['login'];
		$option = TRUE;
	}
	$user = userDataByLogin($active_user, $dbh);
}
?>
<!DOCTYPE html>
<html>
 <head>
   <title>Профиль пользователя</title>
   <link rel="stylesheet" href="style.css">
   <meta charset="utf-8">
 </head>
	 <body>
	 <h1>Профиль пользователя <?php echo $active_user; ?></h1>
	 <?php if(!$option){ ?>
	 	<div class="button block"><a href='login.php'>Войти</a></div>
	 	<?php }else{ ?>
	 	<div class='button type'>
	 		<div class="left"><a href="profile_redactor.php">Изменить данные</a></div>
	 		<div class="right"><a href="logout.php">Выйти</a></div>
	 	</div>
	 	<?php } ?>
	 	<div class="form">
	 	<div class='button type'>
	 		<div class="left">Логин</div>
	 		<div class="right"><?php echo $user['login']; ?></div>
	 	</div>
	 	<div class='button type'>
		 	<div class="left">Фамилия</div>
		 	<div class="right"><?php echo $user['family']; ?></div>
	 	</div>
	 	<div class='button type'>
		 	<div class="left">Имя</div>
		 	<div class="right"><?php echo $user['name']; ?></div>
	 	</div>
	 	<div class='button type'>
		 	<div class="left">Возраст</div>
		 	<div class="right"><?php echo $user['age']; ?></div>
	 	</div>
	 	<div class='button type'>
		 	<div class="left">Город</div>
		 	<div class="right"><?php echo $user['city']; ?></div>
	 	</div>
	 	<div class='button type'>
		 	<div class="left">Язык</div>
		 	<div class="right"><?php echo $language[$user['lang']]; ?></div>
	 	</div>
	 	<div class='button type'>
		 	<div class="left">E-mail</div>
		 	<div class="right"><?php echo $user['email']; ?></div>
	 	</div>
	 	<div class='button type'>
		 	<div class="left">Дата регистрации</div>
		 	<div class="right"><?php echo date("d.m.Y",$user['data_reg']); ?></div>
	 	</div>
	 	<?php if(!$option){ ?>
	 <div class='button type'><a href="post_user.php?user=<?php echo $user['login']; ?>">Написать сообщение</a></div>
	 	<?php }else{ ?>
	 	<div class='button type'><a href="parol.php">Сменить пароль</a></div>
	 	<div class='button type'><a href="delete_profile.php">Удалить профиль</a></div>
	 	<?php } ?>
	 	</div>
	 </body>
</html>