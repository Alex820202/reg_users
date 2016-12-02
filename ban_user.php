<?php
$status = array('1'=>'пользователь', '2'=>'модератор','10'=>'администратор');
session_start();
require_once(__DIR__.'/function.php');
try{	
	$dbh = db_connect($config);
	$active = setSessionUser($dbh);
	if(!$active){
		header('Location: login.php', TRUE, 303);
	}else{
		if(!isAccess('10')){
			echo "<p class='alert'>У Вас не достаточно прав для выполнения скрипта!</p>";
			echo "<p class='button'><a href='logout.php'>Выйти</a></p>";
			echo "<p class='button'><a href='index.php'>Главная</a></p>";
		}else{
			$t = time();
			$user = trim(strip_tags($_GET['user']));
			$option = trim(strip_tags($_GET['opt']));
			$ban = array(1, 2, 3, 10);
			if(in_array($option, $ban)){
				switch($option){
					case 1:
						$data['time']  = $t + 3600;
						$opt = 1;
						$data['banned'] = 1;
						break;
					case 2:
						$data['time']  = $t + 24*3600;
						$opt = 1;
						$data['banned'] = 1;
						break;
					case 3:
						$data['time']  = $t + 7*24*3600;
						$opt = 1;
						$data['banned'] = 1;
						break;
					case 10:
						$data['time']  = 0;
						$data['banned'] = 0;
						$opt = 10;
						break;
				}
				$sql_ban = "UPDATE users SET banned=:banned, data_end_ban=:time WHERE login=:login";
				$sth_ban = $dbh -> prepare($sql_ban);
				$data['login'] = $user;
				$sth_ban -> execute($data);
				header('Location: admin.php?opt='.$opt, TRUE, 303); // Пользователь забанен или разбанен
			}else{
				header('Location: admin.php?opt=2', TRUE, 303); // Не корректные параметры бана.
			}
			
		}	
	}			
} catch (PDOException $e) {
	echo $e->getMessage();
}