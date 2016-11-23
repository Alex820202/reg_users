<?php
require_once(__DIR__.'/function.php');
$length = mt_rand(6, 10);
$pass = passGen($length);

?>
<!DOCTYPE html>
<html>
 <head>
   <title>Генерация пароля</title>
   <meta charset="utf-8">
 </head>
 <body>
 <form action="index.php?flag=4" method="post">
 		<p><input type="text" name="name" value="<?php echo $_POST['name']; ?>" hidden></p>
 			<p><input type="text" name="family" value="<?php echo $_POST['family']; ?>" hidden></p>
 			<p><input type="text" name="age" value="<?php echo $_POST['age']; ?>" hidden></p>
 			<p><input type="text" name="email" value="<?php echo $_POST['email']; ?>" hidden></p>
 			<p><input type="text" name="city" value="<?php echo $_POST['city']; ?>" hidden></p>
 			<p><input type="text" name="lang"  value="<?php echo $_POST['lang']; ?>" hidden></p>
 			<p><input type="text" name="login" value="<?php echo $_POST['login']; ?>" hidden>
 	<input type="text" name="password" value="<?php echo $pass; ?>">
 	<input type="submit" value="Вставить в форму регистрации">
 	
 </form>
 </body>
 </html>