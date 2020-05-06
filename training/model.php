<?php //By Ethan Hughes
//8/23/2019
session_start();
$servername = "localhost";
$username = "****";
$password = "****";

// Create connection
$_SESSION['conn'] = mysqli_connect($servername, $username, $password, "CHATDB");

$_SESSION['long-term'] = [];
$_SESSION['working'] = []; #Stores last ten things with fewest connections making them the most unique and important as they're likely not to be reached by looking at the other words and likely define the conversation the best. They will be connected to counter which determine how much longer they can stay and only once there is a free space can new knowledge come in.

#We'll take working memory together with the last set of words entered and they're connection in the rememberence stream and use them for a response.
$maxlen = 46; #set permanent value for maxlen

$query = mysqli_query($_SESSION['conn'], "SELECT `length` FROM MAXLEN LIMIT 1");
if(mysqli_num_rows($query) > 0){
	$row = mysqli_fetch_assoc($query);
	$maxlen = $row['length'];
}

function refwords($words){
	$wordlist = [];
	$start = 0;
	while(strlen($words) > $start){
		$correct = 0;
		for($length = 1; $length < $maxlen; $length++){
			if($length > strlen($words) - $start){
				break;
			}
			$current = substr($words, $start, $length);
			if($current != " " && $current != "." && array_key_exists($current, $_SESSION['long-term']) && $length > $correct){
				$correct = $length;
			}
		}
		if($correct > 0){
			$newword = substr($words, $start, $correct);
			array_push($wordlist, $newword);
			if(count($_SESSION['long-term'][$newword][0]) < 10){ //tolerance = 10
			    $_SESSION['working'][$newword] = 0; #working mem life = 10
                if(count($_SESSION['working']) > 10){
                    $lowest = 0;
                    $lowword = "";
                    foreach($_SESSION['working'] as $word){
                        if($_SESSION['working'][$word] < $lowest){
                            $lowest = $_SESSION['working'][$word];
							$lowword = $word;
						}
					}
					unset($_SESSION['working'][$lowword]);
				}
			}
			$start += $correct;
		} else {
			$start += 1;
		}
	}
	return $wordlist;
}

function remember($words, $activator, $memlist){
    #recurs = False;
	$weights = [];
    foreach($words as $word){
        foreach($_SESSION['long-term'][$word][0] as $link){
            //if(!array_key_exists($link, $words)){
            if(!array_key_exists($link, $weights) && $link != " "){
                $weights[$link] = $_SESSION['long-term'][$word][0][$link]/$_SESSION['long-term'][$word][0][" "];
			} else if($link != " "){
				$weights[$link] += $_SESSION['long-term'][$word][0][$link]/$_SESSION['long-term'][$word][0][" "];
			}
			//}
		}
	}
    foreach($_SESSION['working'] as $word){
        foreach($_SESSION['long-term'][$word][0] as $link){
            //if(!array_key_exists($link, $_SESSION['working'])){
            if(!array_key_exists($link, $weights) && $link != " "){
                $weights[$link] = $_SESSION['long-term'][$word][0][$link]/$_SESSION['long-term'][$word][0][" "];
			} else if($link != " "){
				$weights[$link] += $_SESSION['long-term'][$word][0][$link]/$_SESSION['long-term'][$word][0][" "];
			}
			//}
		}
	}
    foreach($weights as $word){
        if($weights[$word] > $activator && !array_key_exists($word, $memlist)){
            array_push($memlist, $word);
            //array_merge($memlist, remember([$word], $activator+$weights[$word], $memlist));
			#recurs = True;
		}
	}
    #if sum(weights.values()) > activator and recurs: #Could be switched to extending the list starting with the remembered word with the smallest combined weight
    #    memlist.extend(remember(memlist, activator+sum(weights.values()), memlist));
    #    memlist = list(dict.fromkeys(memlist)); #Not sure if this is necessary now.
    //$memlist = array_unique($memlist);
	return $memlist;
}

function nextword($remembered, $lastword, $wordcount){
	$finallist = [];
	foreach(array_keys($_SESSION['long-term'][$lastword][1]) as $word){
		if($word != " "){
			$finallist[$word] = $_SESSION['long-term'][$lastword][1][$word]/$_SESSION['long-term'][$lastword][1][" "];
			if(array_key_exists($word, $remembered)){
				$finallist[$word] += $remembered[$word]/$wordcount;
			}
		}
	}
	$greatestprob = 0;
	$nextword = "";
	foreach($finallist as $word){
		if($finallist[$word] > $greatestprob){
			$greatestprob = $finallist[$word];
			$nextword = $word;
		}
	}
	return $nextword;
}

function respond($message){
	$wordlist = refwords(strtolower($message));
	$remembered = remember($wordlist, 0, []);
	//foreach($remembered as $word){
	//	echo $word;
	//}
	$lastword = ".";
	$response = "";
	$resplength = 0;
	while($next = nextword($remembered, $lastword, count($wordlist))){
		$resplength++;
		if($next == "." || $next == "?" || $next == "!"){
			$response .= $next;
			break;
		} else if($resplength >= 128){
			$response .= "...";
			break;
		}
		$lastword = $next;
		$response .= " ".$next;
	}

	foreach($_SESSION['working'] as $word){
		$_SESSION['working'][$word] -= 1;
		if($_SESSION['working'][$word] <= -10){
			unset($_SESSION['working'][$word]);
		}
	}

	return $response;
}

$serial = file_get_contents('trained-model');
$_SESSION['long-term'] = unserialize($serial);

mysqli_close($_SESSION['conn']);
	
session_destroy();
?>