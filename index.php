<?php
session_start();
$servername = "localhost";
$username = "****";
$password = "****";

// Create connection
$_SESSION['conn'] = mysqli_connect($servername, $username, $password, "CHATDB");

if(isset($_GET['logout'])){
	
	//Simple exit message
	$fp = fopen($_SESSION['log'], 'a');
	fwrite($fp, "<div class='msgln'><i>User ".$_SESSION['name']." has left the chat session.</i><br></div>");
	fclose($fp);

	$log = $_SESSION['log'];
	mysqli_query($_SESSION['conn'], "DELETE FROM `WAITING` WHERE `filename` = '$log'");
	//Delete log file here << TODO

	//$fp = fopen("queue.txt", "r");
	//$match = fread($fp, 80); //check this number later
	//fclose($fp);
	//if($match == $_SESSION['log']){
	//	$fn = fopen("queue.txt", 'w');
	//	fclose($fn);
	//}
	mysqli_close($_SESSION['conn']);
	
	session_destroy();
	header("Location: index.php"); //Redirect the user
}

function loginForm(){
	echo'
	<div id="loginform">
	<form action="index.php" method="post">
		<p>Please enter your name to continue:</p>
		<label for="name">Name:</label>
		<input type="text" name="name" id="name" maxlen="80"/>
		<input type="submit" name="enter" id="enter" value="Enter" />
	</form>
	</div>
	';
}

if(isset($_POST['enter'])){
	if($_POST['name'] != ""){
		$_SESSION['name'] = stripslashes(htmlspecialchars($_POST['name']));

		//AI RESOURCES
		$serial = file_get_contents('training/trained-model');
		$_SESSION['long-term'] = unserialize($serial);
		$_SESSION['working'] = []; #Stores last ten things with fewest connections making them the most unique and important as they're likely not to be reached by looking at the other words and likely define the conversation the best. They will be connected to counter which determine how much longer they can stay and only once there is a free space can new knowledge come in.
		$query = mysqli_query($_SESSION['conn'], "SELECT `length` FROM MAXLEN LIMIT 1");
		if(mysqli_num_rows($query) > 0){
			$row = mysqli_fetch_assoc($query);
			$_SESSION['maxlen'] = $row['length'];
		}

		//Check waiting list for chat matches
		$match = "";
		$fetchID = "";
		$query = mysqli_query($_SESSION['conn'], "SELECT `filename`, ChatID FROM WAITING LIMIT 1");
		if(mysqli_num_rows($query) > 0){
			$row = mysqli_fetch_assoc($query);
			$match = $row['filename'];
			$fetchID = $row['ChatID'];
		}
		//$fp = fopen("queue.txt", "r");
		//$match = fread($fp, 80); //check this number later
		//fclose($fp);

		$_SESSION['log'] = "";
		if($match != ""){
			$_SESSION['log'] = $match;
			$_SESSION['chatid'] = $fetchID;
			$name = $_SESSION['name'];
			mysqli_query($_SESSION['conn'], "UPDATE `CHATS` SET matchName = '$name' WHERE id = '$fetchID'");
			$fl = fopen($_SESSION['log'], 'a');
			fwrite($fl, "Matched with ".$_SESSION['name']);
			fclose($fl);

			mysqli_query($_SESSION['conn'], "DELETE FROM `WAITING` WHERE `filename` = '$match'");
			//$fn = fopen("queue.txt", 'w');
			//fclose($fn);
		} else {
			$date = gmdate("Y-m-d-H-i-s");
			$chat = "logs/log-".$date."-".$_SESSION['name'].".html";

			$name = $_SESSION['name'];
			mysqli_query($_SESSION['conn'], "INSERT INTO `CHATS` (startName, startDate, msgCount) VALUES ('$name','$date', 0)");

			$query = mysqli_query($_SESSION['conn'], "SELECT LAST_INSERT_ID()");
			$row = mysqli_fetch_assoc($query);
			$insertID = $row['LAST_INSERT_ID()'];

			$_SESSION['chatid'] = $insertID;
			mysqli_query($_SESSION['conn'], "INSERT INTO `WAITING` (`filename`, ChatID) VALUES ('$chat', '$insertID')");
			//$fq = fopen("queue.txt", 'w');
			//fwrite($fq, $chat);
			//fclose($fq);
			
			$_SESSION['log'] = $chat;
			$fn = fopen($chat, 'w');
			fwrite($fn, "Waiting for match...");
			fclose($fn);
		}
	}
	else{
		echo '<span class="error">Please type in a name</span>';
	}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Chat - Customer Module</title>
<link type="text/css" rel="stylesheet" href="style.css" />
</head>

<?php
if(!isset($_SESSION['name'])){
	loginForm();
}
else{
?>
<div id="wrapper">
	<div id="menu">
		<p class="welcome">Welcome, <b><?php echo $_SESSION['name']; ?></b></p>
		<p class="logout"><a id="exit" href="#">Exit Chat</a></p>
		<div style="clear:both"></div>
	</div>	
	<div id="chatbox"><?php
	if(file_exists($_SESSION['log']) && filesize($_SESSION['log']) > 0){
		$handle = fopen($_SESSION['log'], "r");
		$contents = fread($handle, filesize($_SESSION['log']));
		fclose($handle);
		
		echo $contents;
	}
	?></div>
	
	<form name="message" action="">
		<input name="usermsg" type="text" id="usermsg" size="63" maxlen="1000"/>
		<input name="submitmsg" type="submit"  id="submitmsg" value="Send" />
	</form>
</div>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3/jquery.min.js"></script>
<script type="text/javascript">
// jQuery Document
$(document).ready(function(){
	//If user submits the form
	$("#submitmsg").click(function(){	
		var clientmsg = $("#usermsg").val();
		$.post("post.php", {text: clientmsg});				
		$("#usermsg").attr("value", "");
		return false;
	});
	
	//Load the file containing the chat log
	function loadLog(){	
		var url = window.location.href;
		var log = url.substring(0, url.lastIndexOf('/')+1)+"<?php echo $_SESSION['log'] ?>";
		var oldscrollHeight = $("#chatbox").attr("scrollHeight") - 20;
		$.ajax({
			url: log,
			cache: false,
			success: function(html){		
				$("#chatbox").html(html); //Insert chat log into the #chatbox div				
				var newscrollHeight = $("#chatbox").attr("scrollHeight") - 20;
				if(newscrollHeight > oldscrollHeight){
					$("#chatbox").animate({ scrollTop: newscrollHeight }, 'normal'); //Autoscroll to bottom of div
				}				
		  	},
		});
	}
	setInterval (loadLog, 2500);	//Reload file every 2.5 seconds
	
	//If user wants to end session
	$("#exit").click(function(){
		var exit = confirm("Are you sure you want to end the session?");
		if(exit==true){window.location = 'index.php?logout=true';}		
	});
});
</script>
<?php
}
?>
</body>
</html>