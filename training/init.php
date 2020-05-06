<?php //By Ethan Hughes
//8/23/2019
session_start();
$servername = "localhost";
$username = "****";
$password = "****";

// Create connection
$_SESSION['conn'] = mysqli_connect($servername, $username, $password, "CHATDB");

$vocab = [];
$maxlen = 0;

$allwords = fopen('words.txt', 'r');
while(($result = fgets($allwords, 45)) != NULL){
    $vocab[$result] = [[], []];
    $vocab[$result][0][" "] = 0;
    $vocab[$result][1][" "] = 0;
    if(strlen($result) > $maxlen){
        $maxlen = strlen($result);
    }
}

//Add punctuation to tokens
$punctuation = ".?!$%-+=*/&@#()<>,";

for($i = 0; $i < strlen($punctuation); $i++){
    $key = substr($punctuation, $i, 1);
    $vocab[$key] = [[], []];
    $vocab[$key][0][" "] = 0;
    $vocab[$key][1][" "] = 0;
}

$dictionary = fopen('dictionary.csv', 'r');
while(($line = fgetcsv($dictionary, 1000)) != FALSE){
    $word = strtolower($line[0]);
    $words = str_replace(";", " ;", str_replace(",", " ,", str_replace("?", " ?", str_replace(".", " .", str_replace("!", " !", str_replace(")", "", str_replace("(", "", str_replace("  ", " ", strtolower($line[2])))))))));//.replace('-','').replace('\"', '')
    $wordlist = array_unique(explode(" ", $words), SORT_STRING);
    $newword = array_fill_keys($wordlist, 1);
    if(!array_key_exists($word, $vocab)){
        $vocab[$word] = [$newword, []];
        $vocab[$word][0][" "] = 1;
        $vocab[$word][1][" "] = 0;
        if(strlen($word) > $maxlen){
            $maxlen = strlen($word);
        }
    } else {
        $vocab[$word][0][" "] += 1;
        foreach($wordlist as $linkword){
            if(!array_key_exists($linkword, $vocab[$word][0])){
                $vocab[$word][0][$linkword] = 1;
            } else {
                $vocab[$word][0][$linkword] += 1;
            }
        }
    }
}
fclose($dictionary);

$serial = serialize($vocab);
file_put_contents('model', $serial);

mysqli_query($_SESSION['conn'], "INSERT INTO MAXLEN (`length`) VALUES ('$maxlen')");
mysqli_close($_SESSION['conn']);
	
session_destroy();
?>