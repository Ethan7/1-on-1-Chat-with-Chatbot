<?
session_start();
$servername = "localhost";
$username = "****";
$password = "****";
$_SESSION['conn'] = mysqli_connect($servername, $username, $password, "CHATDB");

mysqli_query($_SESSION['conn'], "REVOKE SELECT ON CHATDB.CHATS TO 'chat'");
echo mysqli_error($_SESSION['conn']);

mysqli_close($_SESSION['conn']);
session_destroy();
?>