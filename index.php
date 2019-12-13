<!--
//index.php
!-->

<?php

include('database_connection.php');

session_start();

if(!isset($_SESSION['user_id']))
{
	header("location:login.php");
}

?>

<html>  
    <head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>ChatterBox</title>  
		<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  		<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.2.2/jquery.form.js"></script>
    </head>  
    <body>  
        <div class="container">
			<br />
			
			<h3 align="center">ChatterBox</h3><br />
			<br />
			<div class="row">
				<div class="col-md-8 col-sm-6">
					<h4>Online Users</h4>
				</div>
				<div class="col-md-2 col-sm-3">
					<input type="hidden" id="is_active_public_chat_window" value="no" />
					<button type="button" name="public_chat" id="public_chat" class="btn">Public Chat</button>
				</div>
				<div class="col-md-2 col-sm-3">
					<p align="right">Hi - <?php echo $_SESSION['username']; ?> - <a href="logout.php">Logout</a></p>
				</div>
			</div>
			<div class="table-responsive">
				
				<div id="user_details"></div>
				<div id="user_model_details"></div>
			</div>
			<br />
			<br />
			
		</div>
		
    </body>  
</html>

<style>

.chat_message_area
{
	position: relative;
	width: 100%;
	height: auto;
	background-color: #FFF;
    border: 1px solid #CCC;
    border-radius: 3px;
}

#public_chat_message
{
	width: 100%;
	height: auto;
	min-height: 80px;
	overflow: auto;
	padding:6px 24px 6px 12px;
}

</style>  

<div id="public_chat_dialog" title="Public Chat Window">
	<div id="public_chat_history" style="height:400px; border:1px solid #ccc; overflow-y: scroll; margin-bottom:24px; padding:16px;">

	</div>
	<div class="form-group">
		<div class="chat_message_area">
			<div id="public_chat_message" contenteditable class="form-control">

			</div>
		</div>
	</div>
	<div class="form-group" align="right">
		<button type="button" name="send_public_chat" id="send_public_chat" class="btn btn-info">Send</button>
	</div>
</div>


<script>  
$(document).ready(function(){

	fetch_user();

	setInterval(function(){
		fetch_user();
		update_chat_history_data();
		fetch_public_chat_history();
	}, 5000);

	function fetch_user()
	{
		$.ajax({
			url:"fetch_user.php",
			method:"POST",
			success:function(data){
				$('#user_details').html(data);
			}
		})
	}

	function make_chat_dialog_box(to_user_id, to_user_name)
	{
		var modal_content = '<div id="user_dialog_'+to_user_id+'" class="user_dialog" title="Conversation with '+to_user_name+'">';
		modal_content += '<div style="height:400px; border:1px solid #ccc; overflow-y: scroll; margin-bottom:24px; padding:16px;" class="chat_history" data-touserid="'+to_user_id+'" id="chat_history_'+to_user_id+'">';
		modal_content += fetch_user_chat_history(to_user_id);
		modal_content += '</div>';
		modal_content += '<div class="form-group">';
		modal_content += '<textarea name="chat_message_'+to_user_id+'" id="chat_message_'+to_user_id+'" class="form-control chat_message"></textarea>';
		modal_content += '</div><div class="form-group" align="right">';
		modal_content+= '<button type="button" name="send_chat" id="'+to_user_id+'" class="btn btn-info send_chat">Send</button></div></div>';
		$('#user_model_details').html(modal_content);
	}

	$(document).on('click', '.start_chat', function(){
		var to_user_id = $(this).data('touserid');
		var to_user_name = $(this).data('tousername');
		make_chat_dialog_box(to_user_id, to_user_name);
		$("#user_dialog_"+to_user_id).dialog({
			autoOpen:false,
			width:400
		});
		$('#user_dialog_'+to_user_id).dialog('open');
		$('#chat_message_'+to_user_id).emojioneArea({
			pickerPosition:"top",
			toneStyle: "bullet"
		});
	});

	$(document).on('click', '.send_chat', function(){
		var to_user_id = $(this).attr('id');
		var chat_message = $.trim($('#chat_message_'+to_user_id).val());
		if(chat_message != '')
		{
			$.ajax({
				url:"insert_chat.php",
				method:"POST",
				data:{to_user_id:to_user_id, chat_message:chat_message},
				success:function(data)
				{
					//$('#chat_message_'+to_user_id).val('');
					var element = $('#chat_message_'+to_user_id).emojioneArea();
					element[0].emojioneArea.setText('');
					$('#chat_history_'+to_user_id).html(data);
				}
			})
		}
		else
		{
			alert('Type something');
		}
	});

	function fetch_user_chat_history(to_user_id)
	{
		$.ajax({
			url:"fetch_user_chat_history.php",
			method:"POST",
			data:{to_user_id:to_user_id},
			success:function(data){
				$('#chat_history_'+to_user_id).html(data);
			}
		})
	}

	function update_chat_history_data()
	{
		$('.chat_history').each(function(){
			var to_user_id = $(this).data('touserid');
			fetch_user_chat_history(to_user_id);
		});
	}

	$(document).on('click', '.ui-button-icon', function(){
		$('.user_dialog').dialog('destroy').remove();
		$('#is_active_public_chat_window').val('no');
	});

	$('#public_chat_dialog').dialog({
		autoOpen:false,
		width:400
	});

	$('#public_chat').click(function(){
		$('#public_chat_dialog').dialog('open');
		$('#is_active_public_chat_window').val('yes');
		fetch_public_chat_history();
	});

	$('#send_public_chat').click(function(){
		var chat_message = $.trim($('#public_chat_message').html());
		var action = 'insert_data';
		if(chat_message != '')
		{
			$.ajax({
				url:"public_chat.php",
				method:"POST",
				data:{chat_message:chat_message, action:action},
				success:function(data){
					$('#public_chat_message').html('');
					$('#public_chat_history').html(data);
				}
			})
		}
		else
		{
			alert('Type something');
		}
	});

	function fetch_public_chat_history()
	{
		var public_chat_dialog_active = $('#is_active_public_chat_window').val();
		var action = "fetch_data";
		if(public_chat_dialog_active == 'yes')
		{
			$.ajax({
				url:"public_chat.php",
				method:"POST",
				data:{action:action},
				success:function(data)
				{
					$('#public_chat_history').html(data);
				}
			})
		}
	}

	$(document).on('click', '.remove_chat', function(){
		var chat_message_id = $(this).attr('id');
		if(confirm("Are you sure you want to remove this chat?"))
		{
			$.ajax({
				url:"remove_chat.php",
				method:"POST",
				data:{chat_message_id:chat_message_id},
				success:function(data)
				{
					update_chat_history_data();
				}
			})
		}
	});
	
});  
</script>