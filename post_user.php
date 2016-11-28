<?php
session_start();
require_once(__DIR__.'/function.php');
try{	
	$dbh = db_connect($config);
	$active = setSessionUser($dbh);
	if(!$active){
		header('Location: login.php', TRUE, 303);
	}else{
		$login = $_SESSION['login'];
		$user = trim(strip_tags($_GET['user']));
/*
* Выбираем из базы данных список сообщений между пользователями.
*/
			if(!empty($_GET['user']) && empty($_GET['opt'])){
				
				
				$sql_posts = 'SELECT * FROM posts WHERE (user1=:login AND user2=:user) OR (user1=:user AND user2=:login) ORDER BY date ASC';
				$sth_posts = $dbh -> prepare($sql_posts);
				$data['user'] = $user;
				$data['login'] = $login;
				$sth_posts -> execute($data);
				$results_post = $sth_posts -> fetchAll();
/*
* Если стоит галочка "Прочитано" (данная страница принимает все сообщение и параметр opt=1)
*/
			}elseif(!empty($_GET['user']) && $_GET['opt'] == 1 && !empty($_POST)){
				$sql_post_read = 'UPDATE posts SET new=:new WHERE id=:id, user2=:user2';
				$sth_post_read = $dbh -> prepare($sql_post_read);
				$data['new'] = 0;
				$data['id'] = trim(strip_tags($_POST['id']));
				$data['user2'] = $login;
				$sth_post_read -> execute($data);
				header('Location: post_user.php?user='.$user, TRUE, 303);
/*
* Если отправляется сообщение. Страница принимает сообщение и параметр opt=2.
*/
			}elseif(!empty($_GET['user']) && $_GET['opt'] == 2 && !empty($_POST)){
				$sql_post_save = 'INSERT INTO posts SET posts=:posts, user1=:user1, user2=:user2, new=:new, date=:date';
				$sth_post_save = $dbh -> prepare($sql_post_save);
				$data['posts'] = trim(strip_tags($_POST['post']));
				$data['user1'] = $login;
				$data['user2'] = $user;
				$data['new'] = 1;
				$data['date'] = time();
				$sth_post_save -> execute($data);
				header('Location: post_user.php?user='.$user, TRUE, 303);
/*
* Если нажимается кнопка удалить сообщение. Страница принимает сообщение и параметр opt=3.
*/				
			}elseif(!empty($_GET['user']) && $_GET['opt'] == 3 && !empty($_POST)){
				$sql_delete_post = 'DELETE FROM posts WHERE id=:id AND user1=:user1';
				$sth_delete_post = $dbh -> prepare($sql_delete_post);
				$data_delete_post['id'] = trim(strip_tags($_POST['id']));
				$data_delete_post['user1'] = $login;
				$sth_delete_post -> execute($data_delete_post);
				header('Location: post_user.php?user='.$user, TRUE, 303);
/*
* Если нет никаких входных параметров, то массив сообщений будет пустым.
*/					
			}else{
				$results_post = array();
			}
		}

?>
<!DOCTYPE html>
<html>
 <head>
   <title>Переписка</title>
   <link rel="stylesheet" href="style.css">
   <meta charset="utf-8">
 </head>
	 <body>
	 <h1>Обмен сообщениями</h1>
	 <?php
	 foreach($results_post as $result){
	 	if($result['user2'] == $login && $result['new'] == 1){
			$class = " class = 'new_post' ";
		}
		echo "<form action='post_user.php?user=".$user."&opt=1' method='post' >";
	 	echo "<p><input".$class." type='text' value='От: ".$result['user1']."' name='user1' readonly ></p>";
	 	echo "<p><input".$class." type='text' value='Кому: ".$result['user2']."' name='user2' readonly ></p>";
	 	echo "<p><input".$class." type='text' value='Дата: ".date("d.m.Y H:i:s", $result['date'])."' name='date' readonly ></p>";
	 	echo "<p><textarea".$class." name='posts' readonly>".$result['posts']."</textarea></p>";
	 	echo "<input type='text' value='".$result['id']."' name='id' hidden>";
	 	if($result['user2'] == $login && $result['new'] == 1){
	 		echo "<p><input type='submit' value='Прочитано'</p>";
		}
		if($result['user1'] == $login){
		echo "<p><button formaction='post_user.php?user=".$user."&opt=3' formmethod='post'>Удалить сообщение</button></p>";}
	 	echo "<hr>"; 
	 	echo "</form>";
	 }
	 ?>
	 <form action="post_user.php?user=<?php echo $user;?>&opt=2" method="post">
	 	<p><textarea name="post"></textarea></p>
	 	<input type="submit" value="Отправить сообщение">
	 </form>
	 </body>
</html>
<?php
} catch (PDOException $e) {
	echo $e->getMessage();
}