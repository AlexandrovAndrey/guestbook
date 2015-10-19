<?php foreach ($messages as $msg): ?>
<div class="message message_level<?php echo $msg['level'] ?>" id="message_<?php echo $msg['id'] ?>">
	<div class="message_header">
		<div class="message_author"><?php if (!$msg['username']) {var_dump($msg); die();} else {echo $msg['username'];} ?></div>
		<div class="message_date"><?php echo $msg['msg_date'] ?> <a href="#message_<?php echo $msg['id'] ?>">#</a></div>
	</div>
	<div class="msg_text"><?php echo nl2br($msg['msg_text']) ?></div>

	<div class="message_controls">
		<?php if (isset($_COOKIE['guestbook_key']) && $_COOKIE['guestbook_key'] == $msg['key']): ?><a href="javascript:showUpdateMsgBlock('<?php echo $msg['id'] ?>')">редактировать</a><?php endif; ?>
		<?php if ($msg['level'] != 2): ?><a href="javascript:showNewMsgBlock('<?php echo $msg['id'] ?>');">ответить</a><?php endif; ?>
	</div>

	<div class="answer_text_block" id="answer_text_block_<?php echo $msg['id']?>">
		Представьтесь, пожалуйста:<Br />
		<input type="text" /><br />
		Новый отзыв:
		<textarea></textarea><Br />
		<a href="javascript:;" class="reply" onclick="prepareSave('<?php echo $msg['id'] ?>')">добавить</a>
		<a href="javascript:;" class="cancel_btn" onclick="$('#answer_text_block_<?php echo $msg['id'] ?>').hide()">отмена</a>
	</div>

	<div class="answer_text_block edit_text_block" id="edit_text_block_<?php echo $msg['id']?>">
		<textarea></textarea><Br />
		<a href="javascript:;" class="reply" onclick="updateMessage('<?php echo $msg['id'] ?>')">сохранить</a>
		<a href="javascript:;" class="cancel_btn" onclick="$('#edit_text_block_<?php echo $msg['id'] ?>').hide()">отмена</a>
	</div>


</div>
<?php endforeach; ?>