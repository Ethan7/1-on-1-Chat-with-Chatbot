<?php //By Ethan Hughes
//8/23/2019
session_start();
set_time_limit(0);
$servername = "localhost";
$username = "****";
$password = "****";

// Create connection
$_SESSION['conn'] = mysqli_connect($servername, $username, $password, "CHATDB");

$vocab = [];
$maxlen = 46;

$query = mysqli_query($_SESSION['conn'], "SELECT `length` FROM MAXLEN LIMIT 1");
if(mysqli_num_rows($query) > 0){
	$row = mysqli_fetch_assoc($query);
	$maxlen = $row['length'];
}

function refwords($words, $vocab, $maxlen){
	//global $maxlen;
	$wordlist = [];
	$start = 0;
	while(strlen($words) > $start){
		$correct = 0;
		for($length = 1; $length < $maxlen; $length++){
			if($length > strlen($words) - $start){
				break;
			}
			$current = substr($words, $start, $length);
			if($current != " " && array_key_exists($current, $vocab) && $length > $correct){
				$correct = $length;
			}
		}
		if($correct > 0){
			$newword = substr($words, $start, $correct);
			array_push($wordlist, $newword);
			$start += $correct;
		} else {
			$start += 1;
		}
	}
	return $wordlist;
}

function memorize($previous, $current, $vocab, $maxlen){
	$prev = refwords($previous, $vocab, $maxlen);
	$next = refwords($current, $vocab, $maxlen);
    if(count($prev) == 0 || count($next) == 0){
		return;
	}
    //for word in responselist:
    //    if word not in vocab:
    //        vocab[word] = {0: 0};
    //        if len(word) > maxlen:
	//            maxlen = len(word);
	$prevword = "";
	$prevrespword = "";
    foreach($prev as $word){
		//Memorize word to word connection
		if($prevword != ""){
			$vocab[$prevword][1][" "] += 1;
			if(!array_key_exists($word, $vocab[$prevword][1])){
				$vocab[$prevword][1][$word] = 1;
			} else {
				$vocab[$prevword][1][$word] += 1;
			}
		//Memorize words to start with
		} else {
			$vocab["."][1][" "] += 1;
			if(!array_key_exists($word, $vocab["."][1])){
				$vocab["."][1][$word] = 1;
			} else {
				$vocab["."][1][$word] += 1;
			}
		}
		$prevword = $word;

		//Memorize message to response connection
        $vocab[$word][0][" "] += 1;
        foreach($next as $respword){
            if(!array_key_exists($respword, $vocab[$word][0])){
				$vocab[$word][0][$respword] = 1;
			} else {
				$vocab[$word][0][$respword] += 1;
			}
		}
	}
	//Memorize words to end with
	if($prevword != "" && $prevword != "." && $prevword != "?" && $prevword != "!"){
		$vocab[$prevword][1][" "] += 1;
		if(!array_key_exists(".", $vocab[$prevword][1])){
			$vocab[$prevword][1]["."] = 1;
		} else {
			$vocab[$prevword][1]["."] += 1;
		}
	}
}

$serial = file_get_contents('model');
$vocab = unserialize($serial);

$currentSender = "";
$prevMessage = "";
$currentMessage = "";
$chatID = -1;
$query = mysqli_query($_SESSION['conn'], "SELECT MESSAGES.message, MESSAGES.senderName, CHATS.ID FROM CHATS JOIN MESSAGES ON CHATS.ID=MESSAGES.ChatID WHERE CHATS.msgCount > 1 AND CHATS.matchName IS NOT NULL ORDER BY CHATS.ID, MESSAGES.mDate");
if(mysqli_num_rows($query) > 0){
	while($row = mysqli_fetch_assoc($query)){
		if($chatID != $row['ID']){
			$prevMessage = "";
			$chatID = $row['ID'];
		}
		if($currentSender == $row['senderName']){
			$currentMessage .= " ".$row['message'];
		} else {
			if($prevMessage != ""){
				$vocab = memorize($prevMessage, $currentMessage, $vocab, $maxlen);
			}
			$prevMessage = $currentMessage;
			$currentSender = $row['senderName'];
			$currentMessage = $row['message'];
		}
	}
	if($prevMessage != ""){
		$vocab = memorize($prevMessage, $currentMessage, $vocab, $maxlen);
	}
	//Finish out final word to word connections.
	$current = refwords($currentMessage, $vocab, $maxlen);
	$prevword = "";
	foreach($current as $word){
		//Memorize word to word connection
		if($prevword != ""){
			$vocab[$prevword][1][" "] += 1;
			if(!array_key_exists($word, $vocab[$prevword][1])){
				$vocab[$prevword][1][$word] = 1;
				//echo $prevword." ";
			} else {
				$vocab[$prevword][1][$word] += 1;
			}
		//Memorize words to start with
		} else {
			$vocab["."][1][" "] += 1;
			if(!array_key_exists($word, $vocab["."][1])){
				$vocab["."][1][$word] = 1;
			} else {
				$vocab["."][1][$word] += 1;
			}
		}
		$prevword = $word;
	}
	//Memorize words to end with
	if($prevword != "" && $prevword != "." && $prevword != "?" && $prevword != "!"){
		$vocab[$prevword][1][" "] += 1;
		if(!array_key_exists(".", $vocab[$prevword][1])){
			$vocab[$prevword][1]["."] = 1;
		} else {
			$vocab[$prevword][1]["."] += 1;
		}
	}
}

$serial = serialize($vocab);
file_put_contents('trained-model', $serial);

mysqli_close($_SESSION['conn']);
	
session_destroy();
?>