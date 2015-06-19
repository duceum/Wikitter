<?php

require_once('TwitterAPIExchange.php');
require_once ('medoo.min.php');
require_once ('../PhpMorphy/morphfunctions.php');

function getRandomWord($database){
    
     
    $data = $database->query("SELECT * FROM words WHERE robot_meaning = 0 AND meaning_number > 1 LIMIT 1")->fetchAll();
    
   
    return $data;
}

function updateDatabase($word_id, $database){
    
    $database->update('words', [
	'robot_meaning' => "1"
    ], [
	'word_id' => $word_id
    ]);
    
    return true;
}
$database = new medoo([
	// required
	'database_type' => 'mysql',
	'database_name' => 'duceum_wikitter',
	'server' => '127.0.0.1',
	'username' => '044311002_wiki',
	'password' => '123456',
	'charset' => 'utf8',
 
]);
$word = getRandomWord($database);
$word = $word['0'];


$count = $database->count("tweets", [
	"query_key" =>$word['text'] 
]);
 
if ($count < 400){
    
    //$base = getBaseFrom($word['text'], $morphy);
    $forms = getFirstForms($word['text'], $morphy);
    
    $forms[] = "#".$forms['0'];
   
    foreach ($forms as $form){
    
        $form = urlencode($form);    
        $url = "http://duceum.myjino.ru/twitter/index.php?q=$form";
        
        
        $curl_handle=curl_init();
        curl_setopt($curl_handle, CURLOPT_URL, $url);
        curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl_handle, CURLOPT_USERAGENT, 'wikitter');
        $query = curl_exec($curl_handle);
        curl_close($curl_handle);
        
        
        //echo $json;
    }
    updateDatabase($word['word_id'], $database);
    var_dump($forms);
}else{
   
    updateDatabase($word['word_id'], $database); //отметим что слово было обработано
    echo "Уже в бд";
}


//var_dump($word);
//










?>