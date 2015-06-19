<?php

require_once ('library/medoo.min.php');


//Используем простой синтаксис библиотеки medoo
function getAllTweetsFromDatabase($forms, $database){
    
   $forms[] = "#".$forms['0'];
    
   $data =  $database->select("tweets", "tweet", [
		"query_key" => $forms
    ]);    
    
    
    return $data;   
}

function insertTweets($data, $q, $database){
    
    foreach ($data as $tweet){
        
        $database->insert('tweets', [
	        'hash' => md5($q),
	        'query_key' => $q,
	        'tweet' => $tweet
        ]);
        
    }
    return true;
   
}

//Используем простой синтаксис библиотеки medoo
function getTweetsFromDatabase($q, $database){
    
    $data = $database->select('tweets', [ 'tweet' ],
        [ 'query_key' => $q ]);
    return $data;
}


//функция для крона. Берем первое необработанное слово.
function getRandomWord($database){
    
     
    $data = $database->query("SELECT * FROM words WHERE robot_meaning <> 1 AND meaning_number > 1 LIMIT 1")->fetchAll();
    
   
    return $data;
}
//добавили твиты в бд для слова word, обновляем его в БД
function updateDatabase($word_id, $database){
    
    $database->update('words', [
	'robot_meaning' => "1"
    ], [
	'word_id' => $word_id
    ]);
    
    return true;
}


function getArticlesFromDatabase($database){
    
     
    
    $datas = $database->select("articles", "article_id");
   
    return $datas;
}

function getArticleFromDatabase($id, $database){
    
     
    
    $datas = $database->select("words", "*", ['article_id' => $id]);
   
    return $datas;
}

function getFirstArticleFromDatabase($database){
    
     
    
    $datas = $database->select("articles", "article_id", ['flag_parse' => '1', "LIMIT" => '1']);
   
    return $datas[0];
}

function pasteRobotMeaningINDatabase($word_id, $meaning_number, $wiki_meaning, $database){
    
    $database->update('words', [
	'meaning_number' => $meaning_number,
	'robot_meaning' => $meaning_number,
	'meaning_desc' => $wiki_meaning
    ], [
	'word_id' => $word_id
    ]);
    
    return true;
}


function pasteRobotMeaningINDatabase2($word_id, $robot_meaning, $wiki_meaning, $twitter_number, $database){
    
    $database->update('words', [
	'full_twitter_meaning' => $twitter_number,
	'robot_meaning' => $robot_meaning,
	'meaning_desc' => $wiki_meaning
    ], [
	'word_id' => $word_id
    ]);
    
    return true;
}


function updateArticle($id, $database){
    
    $database->update('articles', [
	'flag_parse' => "0"
    ], [
	'article_id' => $id
    ]);
    
    return true;
}

// Independent configuration
/////////////////////////////////////////////////////////////////////////////////////////
 
$database = new medoo([
	// required
	'database_type' => 'mysql',
	'database_name' => 'duceum_wikitter',
	'server' => '127.0.0.1',
	'username' => '044311002_wiki',
	'password' => '123456',
	'charset' => 'utf8',
 
]);

?>