$(function(){
	// кнопка «добавить запись»
	$('.add_new_message').click(function(){
		$('.new_message_block').show();
	});

	// кнопка «отмена»
	$('.cancel_btn').click(function(){
		$('.new_message_block').hide();
		$('.new_message_block textarea').val('');
		$('.new_message_block input').val('');
	});

	// кнопка «добавить» - сохранение записи
	$('.save_new_message_1level').click(function(){
		$('.new_message_block a').hide();
		
		var params = {};
		params.username = $('.new_message_block input[name=username]').val();
		params.msg_text = $('.new_message_block textarea[name=new_msg_text_level1]').val();
		params.parent_id = 0;
		saveNewMessage(params);
	});
})

function saveNewMessage(msg){
	$.ajax({
		url: 'php/api.php?action=create',
		method: 'post',
		data: msg,
		dataType: 'json',
		success: function(data){
			if (1*msg.parent_id === 0){
				$( data.html ).insertBefore( "#footer" );
				$('.new_message_block').hide();
				$('.new_message_block textarea').val('');
				$('.new_message_block input').val('');
				$('.new_message_block a').show();
				window.scrollTo(0,document.body.scrollHeight);
			} else {
				$( data.html ).insertAfter( "#message_"+msg.parent_id);
				$('#answer_text_block_'+msg.parent_id+' input').val('');
				$('#answer_text_block_'+msg.parent_id+' textarea').val('');
				$('#answer_text_block_'+msg.parent_id).hide();
			}
		},
		error: function(data){
			alert('Непредвиденная ошибка.\n' + data.responseText);
			if (1*msg.parent_id === 0){
				$('.new_message_block a').show();
			}
		}
	});
}

function saveOldMessage(msg){
	$.ajax({
		url: 'php/api.php?action=update',
		method: 'post',
		data: msg,
		dataType: 'json',
		success: function(data){
			var update_block = $('#edit_text_block_'+msg.id);
			update_block.hide();

			var message_block = $('#message_' + msg.id);
			var msg_text = message_block.find('.msg_text').text(msg.msg_text);
		},
		error: function(data){
			alert('Непредвиденная ошибка.\n' + data.responseText);
			// if (1*msg.parent_id === 0){
			// 	$('.new_message_block a').show();
			// }
		}
	});
}

function showNewMsgBlock(msg_id){
	$('#answer_text_block_'+msg_id).show();
}

function showUpdateMsgBlock(msg_id){
	var update_block = $('#edit_text_block_'+msg_id);
	update_block.show();
	var message_block = $('#message_' + msg_id);
	var username = message_block.find('.message_author').val();
	var msg_text = message_block.find('.msg_text').text();
	update_block.find('textarea').val(msg_text);
}


function prepareSave(msg_id){
	var params = {};
	params.username = $('#answer_text_block_'+msg_id+' input').val();
	params.msg_text = $('#answer_text_block_'+msg_id+' textarea').val();
	params.parent_id = msg_id;
	saveNewMessage(params);
}

function updateMessage(msg_id){
	var params = {};
	params.msg_text = $('#edit_text_block_'+msg_id+' textarea').val();
	params.id = msg_id;
	saveOldMessage(params);
}