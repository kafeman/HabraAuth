<?php

// Подключаем HabraAuth
include __DIR__ . '/../HabraAuth.class.php';

// Небольшой конфиг
$config = array(
	// Адрес callback-страницы
	'callback' => 'http://localhost/sample/callback.php',

	// Соль
	'salt' => 'qwerty',

	// Куки от аккаунта, с которого будет слаться почта
	'cookies' => array(
		'PHPSESSID' => '8ba44cc67a851d1c43d740c356665061',
		'hsec_id' => 'c086a2c37f395cbb9aa7b064c8c712db',
	),
);

// Создаем новый объект HabraAuth
$habraAuth = new HabraAuth($config);

// Если был сабмит формы, то пробуем авторизовать пользователя
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
	$habraAuth->auth($_POST['login']);
	header('Location: http://habrahabr.ru/users/none/mail/');
	exit();
}

?>
<meta charset="utf-8">

<h1>Пример авторизации через Хабр</h1>

<form method="post">

	<p>Введите ваш ник на Хабре:</p>

	<p>
		<input type="text" name="login">
		<input type="submit" value="Авторизуй меня!">
	</p>

</form>
