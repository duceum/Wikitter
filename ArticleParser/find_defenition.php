<?php
header("Content-Type: text/html; charset=utf-8");
ini_set('error_reporting', E_STRICT);
include 'simple_html_dom.php';
require_once ('medoo.min.php');
require_once ('../../PhpMorphy/morphfunctions.php');
function getID(){
    return (!empty( $_GET['id'])) ?  $_GET['id'] : false;
}


function getArticleFromDatabase($id, $database){
    
     
    
    $datas = $database->select("words", "*", ['article_id' => $id]);
   
    return $datas;
}

function getFirstArticleFromDatabase($database){
    
     
    
    $datas = $database->select("articles", "article_id", ['flag_parse' => '0', "LIMIT" => '1']);
   
    return $datas[0];
}

function pasteRobotMeaningINDatabase($word_id, $meaning_number, $wiki_meaning, $database){
    
    $database->update('words', [
	'meaning_number' => $meaning_number,
	'meaning_desc' => $wiki_meaning
    ], [
	'word_id' => $word_id
    ]);
    
    return true;
}


function updateArticle($id, $database){
    
    $database->update('articles', [
	'flag_parse' => "1"
    ], [
	'article_id' => $id
    ]);
    
    return true;
}

function getDef($data){
    
     return count($data);
    
}

function getCurl($url){
    
    
        
        $curl_handle=curl_init();
        curl_setopt($curl_handle, CURLOPT_URL, $url);
        curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl_handle, CURLOPT_USERAGENT, 'wikitter');
        $query = curl_exec($curl_handle);
        curl_close($curl_handle);
        
        return $query;
    
}


$id = getID();
$result = array();
$database = new medoo([
	// required
	'database_type' => 'mysql',
	'database_name' => 'duceum_wikitter',
	'server' => '127.0.0.1',
	'username' => '044311002_wiki',
	'password' => '123456',
	'charset' => 'utf8',
 
]);




if ($id){
        
       
        
    $words = getArticleFromDatabase($id, $database);
    
    $j = 0;    
    foreach($words as &$word){ 
        
       
          $word_d = getBaseFrom($word['text'], $morphy);
          $word_d = urlencode($word_d);
          $url = "http://duceum.myjino.ru/wiki/?word=".$word_d;
          
          $wiki = getCurl($url);
          $data = json_decode($wiki, true);
          
          if (($data != NULL) || ($data != "") ){
              $count = getDef($data['word']['Defenitions']);
          }else{
              $count = 0;
          }
          
          if ($count < 2){
              $meaning_number = 0;
              $wiki_meaning = $data['word']['Defenitions'][0]['description']." "
              .$data['word']['Defenitions'][0]['antonyms']." "
              .$data['word']['Defenitions'][0]['synonyms']." "
              .$data['word']['Defenitions'][0]['meronyms']." "
              .$data['word']['Defenitions'][0]['hyponyms']." "
              .$data['word']['Defenitions'][0]['holonyms']." ";
              
              pasteRobotMeaningINDatabase($word['word_id'], $meaning_number, $wiki_meaning, $database);
          }else{
              $meaning_number = $count;
              $wiki_meaning = ' ';
              pasteRobotMeaningINDatabase($word['word_id'], $meaning_number, $wiki_meaning, $database);
          }
          
          echo $word['word_id'];
          echo "<br>";
          
          
          
         
    }   
    
    exit();

    
}else{
   
   $art_id = getFirstArticleFromDatabase($database);
   
   var_dump($art_id);
   $words = getArticleFromDatabase($art_id, $database);
   
   
    $j = 0;    
    foreach($words as &$word){ 
        
       
          $word_d = getBaseFrom($word['text'], $morphy);
          $word_d = urlencode($word_d);
          $url = "http://duceum.myjino.ru/wiki/?word=".$word_d;
          
          $wiki = getCurl($url);
          $data = json_decode($wiki, true);
          
          if (($data != NULL) || ($data != "") ){
              $count = getDef($data['word']['Defenitions']);
          }else{
              $count = 0;
          }
          
          if ($count < 2){
              $meaning_number = 0;
              $wiki_meaning = $data['word']['Defenitions'][0]['description']." "
              .$data['word']['Defenitions'][0]['antonyms']." "
              .$data['word']['Defenitions'][0]['synonyms']." "
              .$data['word']['Defenitions'][0]['meronyms']." "
              .$data['word']['Defenitions'][0]['hyponyms']." "
              .$data['word']['Defenitions'][0]['holonyms']." ";
              
              pasteRobotMeaningINDatabase($word['word_id'], $meaning_number, $wiki_meaning, $database);
          }else{
              $meaning_number = $count;
              $wiki_meaning = ' ';
              pasteRobotMeaningINDatabase($word['word_id'], $meaning_number, $wiki_meaning, $database);
          }
          
          
          
          
          
         
    }    
    updateArticle($art_id, $database);
    exit();
}


?>