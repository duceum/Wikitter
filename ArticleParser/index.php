<?php
header("Content-Type: text/html; charset=utf-8");
ini_set('error_reporting', E_STRICT);
include 'simple_html_dom.php';
require_once ('medoo.min.php');
require_once ('../Functions/functions.php');


function insertArticle($url, $sentence_count, $words_count, $database){
    
    
        var_dump ($words_count);
        $database->insert('articles', [
	        'url' => $url,
	        'sentence_count' => $sentence_count,
	        'words_count' => $words_count
        ]);
        
    
    
    
    return $database->pdo->lastInsertId();
   
}



function insertWords($id, $words, $database){
    
        $sentence_count = 0;
        foreach($words as &$sentence){
            
           foreach ($sentence as &$word){
               
             echo  $word;
             if (strlen($word) > 1){
                 $database->insert('words', [
	            'article_id' => $id,
	            'sentence_id' => $sentence_count,
	            'text' => $word,
	            'meaning_desc' => " "
                ]);
             } 
           }
           echo " добавили предложение $sentence_count";
           $sentence_count++;
        }
        
        
        
    
    
    
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




if(isset($_GET['url'])){
    $url = $_GET['url'];
    
    //$url = urlencode(url);
	$html = @file_get_html($url);
    $text = "";
    $b = $html->find('.b-text blockquote',0);
    $b->outertext = '';
    
     foreach($html->find('.b-text p') as $paragraph){
         
         $text =  $text.strip_tags(str_replace('<br>', ' ',$paragraph->innertext))." ";
        
     }
     
     
     $text = deleteStopWords($text);
     $text = clearText($text);
     
     
     
    $items =  preg_split("/[.?!] /", $text); 
       
    
    //Заносим в БД
    
    $words_count1 = split(" ",$text); //считаю количество слов разделенных пробелами 
    
    $words_array = array();
    foreach($items as &$sentence){
        
         $sentence = clearDOT($sentence);
         $words = split(" ",$sentence);
         
         $words_array[] = $words;
     }
     
     
    
    $id = insertArticle($url, count($items), sizeof($words_count1), $database);
    
    echo "Добавили статью <br>";
    
    $id = insertWords($id, $words_array, $database);
}else{
    echo "неверный формат данных;";
}

    

?>