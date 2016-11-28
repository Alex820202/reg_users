<?php
session_start();
require_once(__DIR__.'/function.php');
try{	
	$dbh = db_connect($config);
	$active = setSessionUser($dbh);
	if(!$active){
		header('Location: login.php', TRUE, 303);
	}else{
		/*
		* Все сообщения пишутся в одну таблицу (id, кто отправил, кому отправил, новое ли оно для принимающего
		*, время отправки). Идет выборка сообщений между пользователями. Итоговый массив, содержит список
		* пользователей, с которыми есть переписки и есть ли от них не прочитанные сообщения.
		*/
		$sql_users = "SELECT T1.login AS login, ifnull(T2.new, '0') AS new FROM ((SELECT DISTINCT user1 AS login FROM posts WHERE user2=:login) UNION (SELECT DISTINCT user2 as login FROM posts WHERE user1=:login)) AS T1 LEFT JOIN (SELECT DISTINCT user1 AS login, new AS new FROM posts WHERE user2=:login AND new=:new) AS T2 ON T1.login = T2.login";
		$sth_users = $dbh -> prepare($sql_users);
		$data['login'] = $_SESSION['login'];
		$data['new'] = 1;
		$sth_users -> execute($data);
		$results = $sth_users -> fetchAll();
		
		foreach($results as $result){
			
		}
	}
		
?>
<!DOCTYPE html>
<html>
 <head>
   <title>Переписки</title>
   <link rel="stylesheet" href="style.css">
   <meta charset="utf-8">
 </head>
	 <body>
	 <h1>Текущие переписки</h1>
	 <?php foreach($results as $result){
	 	if($result['new'] == 1){
			$class = 'new_message';
		}else{
			$class = '';
		}
	 	echo "<div class='button'><a  class='".$class."' href='post_user.php?user=".$result['login']."'>".$result['login']."</a></div>";
 
	 } ?>
		
	</body>
</html>
<?php
} catch (PDOException $e) {
	echo $e->getMessage();
}
