<?php
session_start();
?>
<html>
<head>
<script
  src="https://code.jquery.com/jquery-3.4.1.js"
  integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU="
  crossorigin="anonymous"></script>
  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script src="https://cdn.rawgit.com/mervick/emojionearea/master/dist/emojionearea.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.2.2/jquery.form.js"></script>
<script>
$(document).ready(function(){
	var nav = ["Home", "About", "Logout"];
	let ul = $("<ul>");
	$("body").append(ul);
	nav.forEach(function(item, index){
			let ele = $("<a>");
			//?page   <- GET variable
			//#page   <-inline link/scroll to
			//page.php <-relative link to separate page
			ele.attr("href", "?page="+item+".php");
			ele.text(item);
			ul.append($("<li>").append(ele[0]));
	});
	fetch_user();
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

	setInterval(function(){
		//update_last_activity();
		//fetch_user();
		//update_chat_history_data();
		fetch_group_chat_history();
	}, 5000);
	
	 /*$.ajax({
			url: "ajax/get.php", 
			method: "POST", 
			data: {"type":"login", "username":"bob", "password":"1234"}, 
			success: function(result){
					console.log(result);
					alert(result);
					result = JSON.parse(result);
					alert("Status: " + result.status);
			},
			fail: function(jqXHR, textStatus){
				console.log(jqXHR, textStatus);
			}
		});*/
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
		var chat_message = $('#public_chat_message').val();
		var action = 'insert_data';
		if(chat_message != '')
		{
			$.ajax({
				url:"public_chat.php",
				method:"POST",
				data:{chat_message:chat_message, action:action},
				success:function(data){
					$('#public_chat_message').val('');
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
});

</script>
</head>
<body>
Hello there, <?php echo $_SESSION['user']['name'];?><br>

<div class="col-md-2 col-sm-3">
					<input type="hidden" id="is_active_public_chat_window" value="no" />
					<button type="button" name="public_chat" id="public_chat" class="btn btn-warning btn-xs">Public Chat</button>
				</div>
<div id="public_chat_dialog" title="Public Chat Window">
	<div id="public_chat_history" style="height:400px; border:1px solid #ccc; overflow-y: scroll; margin-bottom:24px; padding:16px;">

	</div>
	<div class="form-group">
	<textarea name="public_chat_message" id="public_chat_message" class="form-control"></textarea>
	</div>
	<div class="form-group" align="right">
		<button type="button" name="send_public_chat" id="send_public_chat" class="btn btn-info">Send</button>
	</div>
</div>
		
</body>
</html>