<?php
function var_dump_($var){echo '<pre>'; var_dump($var); die('</pre>');}
include './GuestbookClass.php';

$actions = array('read', 'create', 'update');
if (!isset($_GET['action'])) {die('Не передан необходимый набор параметров!');}
if (!in_array($_GET['action'], $actions)){
	die('Некорректные параметры!');
}

$guestbook = new Guestbook( '../messages.xml' );
try {
	if ($_GET['action'] == 'read'){
		// читаем сообщения

		$messages = $guestbook->getMessages();
		die(json_encode($messages));

	} elseif ($_GET['action'] == 'update'){
		// обновление сообщения

		if (!isset($_POST['id']) || !isset($_POST['msg_text']) || !trim($_POST['id']) || !trim($_POST['msg_text'])){
			throw new Exception("Не переданы необходимые данные!", 1);
		}
		$message = array(
			'id' => $_POST['id'],
			'msg_text' => $_POST['msg_text']
		);
		$result = $guestbook->updateMessage($message);
		if (!$result){
			throw new Exception('Не удалось сохранить запись, попробуйте позже.', 1);
		}
		die(json_encode(array('success' => true)));
	} elseif ($_GET['action'] == 'create'){
		// добавление сообщения

		if (!isset($_POST['username']) || !isset($_POST['msg_text']) || !trim($_POST['username']) || !trim($_POST['msg_text'])){
			throw new Exception("Не переданы необходимые данные! Пожалуйста, введите имя и текст записи.", 1);
		}
		if (isset($_POST['parent_id']) && $_POST['parent_id']){
			$parent_id = $_POST['parent_id'];
		} else {
			$parent_id = 0;
		}
		$message = array(
			'username' => mb_substr(htmlspecialchars(strip_tags($_POST['username'])), 0, 100),
			'msg_text' => mb_substr(htmlspecialchars(strip_tags($_POST['msg_text'])), 0, 20000),
			'msg_date' => date('d.m.Y H:i:s'),
			'id' => uniqid(''),
			'parent_id' => $parent_id
		);
		$msg = $guestbook->createMessage($message);
		if ($msg['parent_id'] == '0'){
			$msg['level'] = 0;
		} else{
			$parent_id = explode('_', $msg['parent_id']);
			if (count($parent_id) == 1){
				$msg['level'] = 1;
			} else {
				$msg['level'] = 2;
			}
		}
		$messages = array($msg);
		if (!$msg){
			die('Не удалось сохранить сообщение.');
		}

		ob_start();
		include('message_template.php');
		$html = ob_get_contents();
		ob_end_clean();
		die(json_encode(array('success' => true, 'html' => $html, 'id' => $msg['id'])));


	}
} catch (Exception $e){
	die($e->getMessage());
}