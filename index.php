<?php
// var_dump($_COOKIE['guestbook_key']);
// die();
include('php/GuestbookClass.php');
$guestbook = new Guestbook('messages.xml');
$messages = $guestbook->getMessages();
?>
<html>
<head>
	<title>Гостевая книга</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="style.css" rel="stylesheet" media="all" />
	<script type="text/javascript" src="js/jquery-1.11.3.min.js"></script>
	<script type="text/javascript" src="js/main.js"></script>
</head>
<body>
<h1>Гостевая книга</h1>
<div class="add_new_message"><a href="javascript:;">добавить запись</a></div>

<div class="new_message_block">
	Представьтесь, пожалуйста:<Br />
	<input type="text" name="username" /><br />
	Новый отзыв:
	<textarea name="new_msg_text_level1"></textarea><Br />
	<a href="javascript:;" class="save_new_message_1level">добавить</a>
	<a href="javascript:;" class="cancel_btn">отмена</a>
</div>

<?php include('php/message_template.php') ?>


<center id="footer">&copy; 2015</center>

</body>
</html>