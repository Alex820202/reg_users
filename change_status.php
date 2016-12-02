<?php
session_start();
require_once(__DIR__.'/function.php');
try{	
	$dbh = db_connect($config);
	$active = setSessionUser($dbh);
	if(!$active){
		header('Location: login.php', TRUE, 303); // Если не запущена сессия, то переходим на старницу авторизации
	}else{	
		if(!isAccess('10')){
			header('Location: index.php', TRUE, 303); // Если не админ, то автоматом перекидывает на главную.
		}else{
			if(empty($_GET['user'])){
				$danger = "<p class='alert'>Не указан пользователь!</p>";
			}else{
				
				$user = trim(strip_tags($_GET['user']));
				$status = array('1'=>'пользователь', '2'=>'модератор','10'=>'администратор');
				if(!empty($_POST)){
					$status_new = (int)trim(strip_tags($_POST['status']));
					$sql_status = "UPDATE users SET status=:status WHERE login=:login";
					$sth_status = $dbh -> prepare($sql_status);
					$data['login'] = $user;
					$data['status'] = $status_new;
					$sth_status -> execute($data);
					header('Location: admin.php', TRUE, 303);
				}
			}
			
		}	
			
		?>
		
		<!DOCTYPE html>
<html>
 <head>
   <title>Смена статуса</title>
   <link rel="stylesheet" href="style.css">
   <meta charset="utf-8">
 </head>
	 <body>
	 <?php
	 if(empty($_GET['user'])){
	 	echo $danger;
	 }else{
	 	?>
	 <h1>Смена статуса</h1>

	 <div class="form">
		 	<form action="change_status.php?user=<?php echo $user; ?>" method="post">
		 	<p><select name="status">
		 		<option selected value='1'>Пользователь</option>
		 		<option value="2">Модератор</option>
		 		<option value="10">Администратор</option>
		 	</select></p>
		 	<p><input type="submit" value="Сменить статус" ></p>
		 	</form>
	</div>
	 </body>
</html>
<?php
		}
	}
} catch (PDOException $e) {
	echo $e->getMessage();
}