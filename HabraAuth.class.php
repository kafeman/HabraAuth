<?php
/**
 * Класс для авторизации на Хабрахабре
 *
 * @see https://github.com/kafeman/HabraAuth
 *
 * @author  kafeman <kafemanw@gmail.com>
 * @license No (Public Domain)
 * @version 0.1
 */
class HabraAuth {
	/**
	 * Конфиг, который нам передали в конструкторе
	 *
	 * @var array
	 */
	private $aConfig=array();
	/**
	 * URL хабропочты для запроса
	 *
	 * @var string
	 */
	private $sUrl='http://habrahabr.ru/json/conversations/send_message/';
	/**
	 * Дополнительные заголовки для запроса
	 *
	 * @var array
	 */
	private $aHeaders=array(
		'X-Requested-With: XMLHttpRequest',
		'Referer: http://habrahabr.ru/users/none/mail/write/'
	);
	/**
	 * Таймаут соединения
	 *
	 * @var integer
	 */
	private $iTimeout=5;
	/**
	 * Массив запроса
	 *
	 * @var array
	 */
	private $aQuery=array();
	/**
	 * Экземпляр cURL
	 *
	 * @var
	 */
	private $oCurl;
	/**
	 * Инициализируем cURL и сохраняем конфиг
	 */
	public function __construct($aConfig) {
		$this->oCurl=curl_init();
		$this->aConfig=$aConfig;
	}
	/**
	 * Закрываем сеанс cURL
	 */
	public function __destruct() {
		curl_close($this->oCurl);
	}
	/**
	 * Отправляет callback-ссылки по Хабропочте
	 *
	 * @param string $user Имя пользователя
	 * @param string $link Callback-ссылка
	 *
	 * @throws Exception Если при отправке письма произошла ошибка
	 */
	private function Mail($sUser, $sLink) {
		$this->aQuery = array(
			'respondent_login' => $sUser,
			'text' => 'Авторизация через Хабрахабр<br/><br/>' .
			          '<a href="' . $sLink .'">Войти</a>',
		);

		curl_setopt($this->oCurl, CURLOPT_URL, $this->sUrl);
		curl_setopt($this->oCurl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($this->oCurl, CURLOPT_HTTPHEADER, $this->aHeaders);
		curl_setopt($this->oCurl, CURLOPT_CONNECTTIMEOUT, $this->iTimeout);
		curl_setopt($this->oCurl, CURLOPT_POSTFIELDS, http_build_query($this->aQuery));
		curl_setopt($this->oCurl, CURLOPT_COOKIE, 'PHPSESSID=' . $this->aConfig['cookies']['PHPSESSID'] .
		                                         ';hsec_id='  . $this->aConfig['cookies']['hsec_id']);

		$resp = curl_exec($this->oCurl);
		$result = json_decode($resp);

		if (isset($result->system_errors[0]))
			throw new Exception($result->system_errors[0]);
	}
	/**
	 * Пробует авторизовать пользователя
	 */
	public function Auth($sUser) {
		$sLink = $this->aConfig['callback'] . '?user=' . $sUser .
		         '&hash=' . md5($sUser . $this->aConfig['salt']);
		try {
			$this->Mail($sUser, $sLink);
		} catch(Exception $e) {
			die('Mail error: ' . $e->getMessage());
		}
	}
	/**
	 * Проверяет callback-ссылку
	 */
	public function CheckAuth($sUser, $sHash) {
		if ($sHash == md5($sUser . $this->aConfig['salt'])) {
			return true;
		}
		return false;
	}
}

?>
