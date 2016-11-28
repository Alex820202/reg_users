<?php
session_start();
require_once(__DIR__.'/function.php');
try{
	logout($config);
	header('Location: index.php', TRUE, 303);
} catch (PDOException $e) {
	echo 'Ошибка базы данных: <br>'.$e->getMessage();
}
?>