<?php


require_once('TwitterAPIExchange.php');
require_once ('medoo.min.php');
require_once ('../../PhpMorphy/morphfunctions.php');


//получим список слов для захвата твитов

// Получаем многозначные слова
// составляем список
// лемматизируем
// удаляем одинаковые
// все записываем в БД
function getWordListFromDatabase($database){
    
    $datas = $database->select("words", "text", [
	"AND" => [
		"robot_meaning" => "0",
		"meaning_number[>]" => '1'
	]
]);

return $datas;
}
function getWordListWithTwitterFromDatabase($database){
    
    $datas = $database->select("words", "text", [
	"AND" => [
		"robot_meaning" => "1",
		"meaning_number[>]" => '1'
	]
]);    
   
    return $datas;
}


function insertWordsInTwitterList($data, $database){
    
    foreach ($data as $future_tweet){
        
        if (strlen($future_tweet) > 2)
        $database->insert('words_parse', [
	        'texts' => $future_tweet
        ]);
        
    }
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

    $words = getWordListFromDatabase($database); //которые не обработали 
    
    
    
   
    $tweets = getWordListWithTwitterFromDatabase($database); //все которые обработали

    foreach ($words as &$word){
       $word = getBaseFrom($word, $morphy);        
    }
    
    foreach ($tweets as &$word){
        $word = getBaseFrom($word, $morphy);        
    }
    
    $result = array_diff($words, $tweets);
    $result = array_unique($result);
    insertWordsInTwitterList($result, $database);
    
    
 
    var_dump( $result);


?>