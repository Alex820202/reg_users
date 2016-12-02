<?php
session_start();
require_once(__DIR__.'/function.php');
try{	
	$dbh = db_connect($config);
	$active = setSessionUser($dbh);
		
?>
<!DOCTYPE html>
<html>
 <head>
   <title>Сайт</title>
   <link rel="stylesheet" href="style.css">
   <meta charset="utf-8">
 </head>
	 <body>
	 	<div class="form">
	 	<div class="container">
	 		<div class="left">
			<?php if($active && $_SESSION['banned'] != 1){
					$login = $_SESSION['login'];
				echo "<p class='button'><a href='profile.php?user=".$login."'>".$login."</a></p>";
				echo "<p class='button'><a href='logout.php'>Выйти</a></p>";
			}elseif(!$active){
				echo "<p class='button'><a href='login.php'>Войти</a></p>";
				echo "<p class='button'><a href='registration.php'>Зарегистрироваться</a></p>";
			}elseif($active && $_SESSION['banned'] == 1 ){
				echo "<p class='button alert'>".$_SESSION['login'].", Вы забанены до ".date("d.m.Y H:i:s", $_SESSION['data_end_ban'])."</p>";
				session_unset();
				session_destroy();
			} ?>
			</div>
			<div class="right">
		<?php if($active && $_SESSION['banned'] != 1){ ?>
				<div class="button type"><a href='users.php'>Список пользователей</a></div>
				<div class="button type"><a href='post_users.php'>Личные сообщения</a></div>
		<?php } if(isAccess('10')){?>
				<div class="button type"><a href='admin.php'>Панель администратора</a></div>
		<?php } ?>
			</div>
			</div>
	 	</div>
	 </body>
</html>
<?php

} catch (PDOException $e) {
	echo $e->getMessage();
}