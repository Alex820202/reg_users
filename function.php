<?php
/*
* Таблица доступных языков
*/
$language = array('ru'=>'Русский', 'eng'=>'English', 'de'=>'Deutsch', 'it'=>'Italiano');
/*
 * Создание соединения с базой данных. На входе переменная, содержащая путь к файлу с настройками подключения, на выходе созданное подключение к базе данных
 */
function db_connect($config){
	require_once ($config);
	$dbh = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
	$dbh -> setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
	$dbh -> setAttribute( PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
	return $dbh;
}
/*
* Определяем данные пользователя на странице регистрации. В случае не совпадения пароля
* и подтверждения, отправляет на страницу заполнения данных.
*/
function user_data(){
	$user['name'] = trim(strip_tags($_POST['name']));
	$user['family'] = trim(strip_tags($_POST['family']));
	$user['age'] = (int)trim(strip_tags($_POST['age']));
	$user['email'] = trim(strip_tags($_POST['email']));
	$user['city'] = trim(strip_tags($_POST['city']));
	$user['lang'] = trim(strip_tags($_POST['lang']));
	$user['data_reg'] = time();
	$user['login'] = trim(strip_tags($_POST['login']));
	
	if($_POST['password'] == $_POST['password_two']){
		$user['password'] = trim($_POST['password']);
	}else{
		header('Location: index.php?flag=1', TRUE, 303);
	}
	return $user;
}
/*
* Функция регистрации пользователя.
*/
function regUser($dbh, $user){
		$salt = salt();
		$user['salt'] = $salt;
		$user['password'] = md5(md5($salt).md5($user['password']).md5($user['login']));	// алгоритм соления пароля.
		if(loginFree($dbh, $login)){
			$sql_reg = 'INSERT INTO users SET login=:login, name=:name, family=:family, age=:age, email=:email, city=:city, lang=:lang, data_reg=:data_reg, password=:password, salt=:salt';
			$sth_reg = $dbh -> prepare($sql_reg);
			$sth_reg -> execute($user);
			header('Location: index.php?flag=2', TRUE, 303);
		}else{
			header('Location: index.php?flag=3', TRUE, 303);
		}
	} 

/*
* Функция проверки заполненной формы на странице.
*/
function validUser($user, $language){ 
	if(!isset($language[$user['lang']])){
		return 1; //такого языка в системе нет.
	}
	if($user['lang'] == 'ru'){
		if(preg_match('#[^а-яА-ЯёЁ]#u',$user['name']) == 1){
			return 2; // Имя должно состоять только из русских букв.
		}
		if(preg_match('#[^а-яА-ЯёЁ]#u',$user['family']) == 1){
			return 3; // Фамилия должна состоять только из русских букв.
		}
		if(preg_match('#[а-яА-ЯёЁ]{2,}(-?\d{0,2})$#u',$user['city']) == 0){
			return 4; // Проверьте наименование города.
		}
	}else{
		if(preg_match('#[^a-zA-Z]#',$user['name']) == 1){
			return 5; // Имя должно состоять только из латинских букв.
		}
		if(preg_match('#[^a-zA-Z]#',$user['family']) == 1){
			return 6; // Фамилия должна состоять только из латинских букв.
		}
		if(preg_match('#[^a-zA-Z]#',$user['city']) == 1){
			return 7; // наименование города должно состоять только из латинских букв.
		}
	}
	if($user['age']<10 || $user['age']>100){
		return 8; // Не правильно указан возраст.
	}
	if(preg_match('#^[a-zA-Z0-9]+@[a-z]+\.[a-z]{2,3}$#',$user['email']) == 0){
		return 9; // не правильный e-mail
	}
	if(strlen($user['login']) < 4 || strlen($user['login']) > 12 || preg_match('#[^a-zA-Z0-9_-]#',$user['login']) == 1){
		return 10; // Логин должен быть длиной минимум 4, максимум 12 символов и состоять из английских букв, цифр и знаков - и _.
	}
	if(strlen($user['password'])<6 || strlen($user['password'])> 10){
		return 11; // Пароль должен быть от 6 до 10 символов.
	}
	return 100;
}
/*
* проверяем свободен ли введенный логин
*/
function loginFree($dbh, $login){
	$sql_login = 'SELECT * FROM users WHERE login=:login';
	$sth_login = $dbh -> prepare($sql_login);
	$data['login'] = $login;
	$sth_login -> execute($data);
	$result = $sth_login -> fetch();
	if(empty($result)){
		return TRUE;
	}else{
		return FALSE;
	}
	
}
/*
* Генератор пароля, на входе длина пароля.
*/
function passGen($length){
	$pass = '';
	$symbol = array(
      'a', 'b', 'c', 'd', 'e', 'f',
      'g', 'h', 'i', 'j', 'k', 'l',
      'm', 'n', 'o', 'p', 'q', 'r',
      's', 't', 'u', 'v', 'w', 'x',
      'y', 'z', 'A', 'B', 'C', 'D',
      'E', 'F', 'G', 'H', 'I', 'J',
      'K', 'L', 'M', 'N', 'O', 'P',
      'Q', 'R', 'S', 'T', 'U', 'V',
      'W', 'X', 'Y', 'Z', '1', '2',
      '3', '4', '5', '6', '7', '8',
      '9', '0', '#', '!', "?", "&"
    );
    $a = count($symbol);
     for ($i = 0; $i < $length; $i++){
	 	$pass .= $symbol[mt_rand(0, $a - 1)];
	 }
      
    return $pass;
}
/*
* Создаем соль для пароля.
*/
function salt(){
	$length = mt_rand(6,10);
	$salt = '';
	$symbol = array(
      'a', 'b', 'c', 'd', 'e', 'f',
      'g', 'h', 'i', 'j', 'k', 'l',
      'm', 'n', 'o', 'p', 'q', 'r',
      's', 't', 'u', 'v', 'w', 'x',
      'y', 'z', 'A', 'B', 'C', 'D',
      'E', 'F', 'G', 'H', 'I', 'J',
      'K', 'L', 'M', 'N', 'O', 'P',
      'Q', 'R', 'S', 'T', 'U', 'V',
      'W', 'X', 'Y', 'Z', '1', '2',
      '3', '4', '5', '6', '7', '8',
      '9', '0', '#', '!', "?", "&"
    );
    $a = count($symbol);
     for ($i = 0; $i < $length; $i++){
	 	$salt .= $symbol[mt_rand(0, $a - 1)];
	 }
      
    return $salt;
}
 function saltPass($login, $password, $salt){
 	return md5(md5($salt).md5($password).md5($login));
 }

?>