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
			echo "<p class='alert'>У Вас не достаточно прав для просмотра страницы!</p>";
			echo "<p class='button'><a href='logout.php'>Выйти</a></p>";
			echo "<p class='button'><a href='index.php'>Главная</a></p>";
		}else{
			$sql_users = 'SELECT id, login, email, status, banned, data_end_ban FROM users';
			$sth_users = $dbh -> prepare($sql_users);
			$sth_users -> execute();
			$results = $sth_users -> fetchAll();

			switch($_GET['opt']){
				case 1:
				$message = "<p class='alert'> Пользователь успешно забанен!</p>";
					break;
				case 10:
				$message = "<p class='alert'> Пользователь успешно разбанен!</p>";
					break;
				case NULL:
				$message = '';
					break;
				default:
				$message = "<p class='alert'> Не корректные данные для бана!</p>";
					break;
			}
			$sql_count = 'SELECT banned, status, COUNT(status) as number FROM users GROUP BY banned, status';
			$sth_count = $dbh -> prepare($sql_count);
			$sth_count -> execute();
			$result_count = $sth_count -> fetchAll();
?>		
<!DOCTYPE html>
<html>
 <head>
   <title>Панель администратора</title>
   <link rel="stylesheet" href="style.css">
   <meta charset="utf-8">
 </head>
	 <body>
	 <?php echo $message; ?>
		 <table>
		 	<tbody>
		 		<tr>
		 			<th>id</th>
		 			<th>Логин</th>
		 			<th>email</th>
		 			<th>Статус</th>
		 			<th>Редактировать</th>
		 			<th>Забанить</th>
		 			<th>Удалить</th>
		 		</tr>
		 		<?php
		 		foreach($results as $user){
					echo '<tr>';
					echo '<td>'.$user['id'].'</td>';
					echo '<td>'.$user['login'].'</td>';
					echo '<td>'.$user['email'].'</td>';
					echo "<td><p>".$status[$user['status']]."</p><p><a href='change_status.php?user=".$user['login']."'>Смена статуса</a></p></td>";
					echo "<td><a href='profile_redactor.php?user=".$user['login']."'>Редактировать профиль</a></td>";
					if($user['banned'] == 0){
						echo "<td><p><a href='ban_user.php?user=".$user['login']."&opt=1'>Забанить на час</a></p><p><a href='ban_user.php?user=".$user['login']."&opt=2'>Забанить на сутки</a></p><p><a href='ban_user.php?user=".$user['login']."&opt=3'>Забанить на неделю</a></p></td>";
						}else{
							echo "<td><a href='ban_user.php?user=".$user['login']."&opt=10'><p>Дата окончания бана:</p><p class='alert'>".date("d.m.Y H:i:s", $user['data_end_ban'])."</p><p>Разбанить</p></a></td>";
						}
					echo "<td><a href='delete_profile.php?user=".$user['login']."'>Удалить пользователя</a></td>";
					echo '</tr>';
				}
				?>
				<tr>
					<td colspan="3">
						Пользователей
					</td>
					<td colspan="4">
						<?php echo $result_count[0]['number']; ?>
					</td>
				</tr>
				<tr>
					<td colspan="3">
						Модераторов
					</td>
					<td colspan="4">
						<?php echo $result_count[1]['number']; ?>
					</td>
				</tr>
				<tr>
					<td colspan="3">
						Администраторов
					</td>
					<td colspan="4">
						<?php echo $result_count[2]['number']; ?>
					</td>
				</tr>
				<tr>
					<td colspan="3">
						Забанено
					</td>
					<td colspan="4">
						<?php echo $result_count[3]['number']+$result_count[4]['number']+$result_count[5]['number']; ?>
					</td>
				</tr>
				<tr>
					<td colspan="3">
						Всего участников
					</td>
					<td colspan="4">
						<?php echo $result_count[0]['number']+$result_count[1]['number']+$result_count[2]['number']+$result_count[3]['number']+$result_count[4]['number']+$result_count[5]['number']; ?>
					</td>
				</tr>
				<?php
			}
		 		?>
		 	</tbody>
		 </table>
	 </body>
</html>
<?php
	}
} catch (PDOException $e) {
	echo $e->getMessage();
}