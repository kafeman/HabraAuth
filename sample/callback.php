<?php

// Если не пришло имя пользователя и хэш, то перенаправляем на форму логина
if (empty($_GET['user']) || empty($_GET['hash'])) {
	header('Location: /sample/login.php');
	exit();
}

// Подключаем библиотеку
include __DIR__ . '/../HabraAuth.class.php';

// Конфиг, тут надо задать только соль
$config = array('salt' => 'qwerty');

// Создаем новый объект HabraAuth
$habraAuth = new HabraAuth($config);

// Проверяем хэш
if (!$habraAuth->CheckAuth($_GET['user'], $_GET['hash'])) {
	header('Location: /sample/login.php');
	exit();
}

?>
<meta charset="utf-8">

<p>Привет, <b><?php echo $_GET['user']; ?></b>, ты авторизовался через Хабр!</p>
