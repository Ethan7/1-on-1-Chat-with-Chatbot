<?php
session_start();

if(isset($_SESSION['name'])){
	$servername = "localhost";
	$username = "****";
	$password = "****";

	// Create connection
	$conn = mysqli_connect($servername, $username, $password, "CHATDB");

	$text = $_POST['text'];
	$date = gmdate("Y-m-d G:i");
	$chatid = $_SESSION['chatid'];
	$name = $_SESSION['name'];
	mysqli_query($conn, "INSERT INTO MESSAGES (ChatID, senderName, mDate, `message`) VALUES ('$chatid', '$name', '$date', '$text')");

	mysqli_query($conn, "UPDATE CHATS SET msgCount = msgCount + 1 WHERE ID = $chatid");
	
	$fp = fopen($_SESSION['log'], 'a');
	fwrite($fp, "<div class='msgln'>($date) <b>".$_SESSION['name']."</b>: ".stripslashes(htmlspecialchars($text))."<br></div>");
	fclose($fp);

	mysqli_close($conn);
}
?>