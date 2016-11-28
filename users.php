<?php
session_start();
require_once(__DIR__.'/function.php');
try{	
	$dbh = db_connect($config);
	$active = setSessionUser($dbh);
	if(!$active){
		header('Location: login.php', TRUE, 303);
	}else{
		$sql_list_user = 'SELECT DISTINCT login FROM users WHERE login NOT IN (:login)';
		$sth_list_user = $dbh -> prepare($sql_list_user);
		$data['login'] = $_SESSION['login'];
		$sth_list_user -> execute($data);
		$results = $sth_list_user -> fetchAll();
		}
?>
<!DOCTYPE html>
<html>
 <head>
   <title>Список пользователей</title>
   <link rel="stylesheet" href="style.css">
   <meta charset="utf-8">
 </head>
	 <body>
	 	<div class="form">
	 	<?php
	 		foreach($results as $result){
				echo "<div class='button'><a href='profile.php?user=".$result['login']."'>".$result['login']."</a></div>";
			}
	 	?>
	 	</div>
	 </body>
</html>
<?php
} catch (PDOException $e) {
	echo $e->getMessage();
}