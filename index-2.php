<?
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

	mysqli_query($_SESSION['conn'], "DELETE FROM `WAITING` WHERE `filename` = '".$_SESSION['log']."'");
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
			mysqli_query($_SESSION['conn'], "UPDATE `CHATS` SET matchName = '".$_SESSION['name']."' WHERE id = '".$fetchID."'");
			$fl = fopen($_SESSION['log'], 'a');
			fwrite($fl, "Matched with ".$_SESSION['name']);
			fclose($fl);

			mysqli_query($_SESSION['conn'], "DELETE FROM `WAITING` WHERE `filename` = '".$match."'");
			//$fn = fopen("queue.txt", 'w');
			//fclose($fn);
		} else {
			$date = gmdate("Y-m-d-H:i:s");
			$chat = "log-".$date."-".$_SESSION['name'].".html";

			mysqli_query($_SESSION['conn'], "INSERT INTO `CHATS` (startName, startDate) VALUES ('".$_SESSION['name']."','".$date."')");

			$query = mysqli_query($_SESSION['conn'], "SELECT LAST_INSERT_ID()");
			$row = mysqli_fetch_assoc($query);
			$insertID = $row['LAST_INSERT_ID()'];

			$_SESSION['chatid'] = $insertID;
			mysqli_query($_SESSION['conn'], "INSERT INTO `WAITING` (`filename`, ChatID) VALUES ('".$chat."', '".$insertID."')");
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
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<title>Ethan Hughes Portfolio</title>

		<!-- Bootstrap CSS -->
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">

		<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
			<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
			<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->
		
	</head>
	<body>
		<nav class="navbar navbar-expand-md navbar-dark bg-dark">
			<a href="#" class="navbar-brand">AI chat</a>
			<button class="navbar-toggler" data-toggle="collapse" data-target="#navbarMenu">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="navbarMenu">
				<ul class="navbar-nav">
					<li class="nav-item">
						<a class="nav-link" href="http://ethanhughes.me/">Home</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="#">Memory Training</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="#">Word Use Training</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="#">Live Model</a>
					</li>
				</ul>
			</div>
		</nav>
		<div class="container-fluid">
			
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
			<div class ="row bg-dark">
				<div class="col-lg-12">
					<footer class="page-footer">
						<a href="mailto:ethanol722@gmail.com">email</a>
						<a href="https://www.linkedin.com/in/ethan-hughes-75375a121/">linkedin</a>
						<a href="https://twitter.com/Ethan72294">twitter</a><br>
						© 2018 Ethan A Hughes
					</footer>
				</div>
			</div>
		</div>

		<!-- jQuery -->
		<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
		<!-- Popper -->
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
		<!-- Bootstrap JS -->
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js" integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T" crossorigin="anonymous"></script>
	</body>
</html>
