<?php
session_start();
require_once(__DIR__.'/function.php');
try{
	
$dbh = db_connect($config);
$active = setSessionUser($dbh);
if(!$active){
	header('Location: login.php', TRUE, 303);
}else{
	$active_user = $_SESSION['login'];
	if(empty($_POST)){
		$user = userDataByLogin($active_user, $dbh);
	}else{
		$sql_update_user = 'UPDATE users SET name=:name, family=:family, age=:age, city=:city, email=:email, lang=:lang WHERE login=:login';
		$sth_update_user = $dbh -> prepare($sql_update_user);
		$data['name'] = trim(strip_tags($_POST['name']));
		$data['family'] = trim(strip_tags($_POST['family']));
		$data['age'] = trim(strip_tags($_POST['age']));
		$data['city'] = trim(strip_tags($_POST['city']));
		$data['email'] = trim(strip_tags($_POST['email']));
		$data['lang'] = trim(strip_tags($_POST['lang']));
		$data['login'] = $active_user;
		$sth_update_user -> execute($data);
		header('Location: profile_redactor.php?opt=1', TRUE, 303);
	}
}
?>
<!DOCTYPE html>
<html>
 <head>
   <title>Редактирование профиля</title>
   <link rel="stylesheet" href="style.css">
   <meta charset="utf-8">
 </head>
	 <body>
	 <?php if($_GET[opt] == '1'){
	 	echo "<p class='alert'> Данные успешно изменены! </p>";
	 } ?>
	 <h1>Редактирование профиля пользователя <?php echo $active_user; ?></h1>
		 <div class="form">
		 	<form action="profile_redactor.php" method="post">
			 	<p><input type="text" name="name" value="<?php echo $user['name']; ?>">
			 	<label>Имя</label></p>
			 	<p><input type="text" name="family" value="<?php echo $user['family']; ?>">
			 	<label>Фамилия</label></p>
			 	<p><input type="text" name="age" value="<?php echo $user['age']; ?>">
			 	<label>Возраст</label></p>
			 	<p><input type="text" name="city" value="<?php echo $user['city']; ?>">
			 	<label>Город</label></p>
			 	<p><input type="text" name="email" value="<?php echo $user['email']; ?>">
			 	<label>E-mail</label></p>
			 	<p><select name="lang">
			 		<?php
			 			foreach($language as $key => $value){
			 				if($key==$user['lang']){
								$select="selected";
							}else{
								$select = '';
							}
							echo "<option ".$select." value=".$key.">".$value."</option>";
						}
						?>
			 	</select>
			 	<label>Язык пользователя</label></p>
			 	<input type="submit" value="Изменить">			 	 
			 </form>
		 </div>
	</body>
</html>
<?php
} catch (PDOException $e) {
	echo $e->getMessage();
}