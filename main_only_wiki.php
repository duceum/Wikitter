<?php
require_once ('../PhpMorphy/morphfunctions.php');
//require_once ('medoo.min.php');
require_once ('../Functions/functions.php');
require_once ('DBWrapper.php');
require_once ('WikiParser.php');
/*function pasteRobotMeaningINDatabase($word_id, $robot_meaning, $database){
    
    $database->update('words', [
    'meaning_number' => $robot_meaning,
	'robot_meaning' => $robot_meaning
    ], [
	'word_id' => $word_id
    ]);
    
    return true;
}
*/


/*$database = new medoo([
	// required
	'database_type' => 'mysql',
	'database_name' => 'duceum_wikitter',
	'server' => '127.0.0.1',
	'username' => '044311002_wiki',
	'password' => '123456',
	'charset' => 'utf8',
 
]);*/



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

function LemmatizeString($string, $morphy){
    
    $words = explode(" ", $string);
    
    foreach ($words as &$word){
        
        
        $word = getBaseFrom($word, $morphy); //привели слово к нормальной форме
        
    }
    
    $response = implode(" ", $words);
    return $response;
}



function LemmatizeArticle($words, $morphy){
    
    foreach ($words['sentences'] as &$sentence){
        
        
        
        foreach ($sentence as &$word){
            //нормализуем слова из статьи
            $word['text'] = LemmatizeString($word['text'], $morphy);
            $word['meaning_desc'] = deleteStopWords($word['meaning_desc']);
            $word['meaning_desc'] = LemmatizeString($word['meaning_desc'], $morphy);
            
            
        }
    }
    
    return $words;
    
}

function full_trim($str)                             
{                                                    
    return trim(preg_replace('/\s{2,}/', ' ', $str));
                                                      
}

function getDictionary($word, $morphy){
    
    //$word_u = urlencode($word);
    //$url = "http://duceum.myjino.ru/wiki/?word=$word_u";
    //$dictionary = getCurl($url);
    $WikiParser = new WikiParser();
    $dictionary = $WikiParser->getWord($word);
    
    //отформатируем
    
    foreach ($dictionary['Defenitions'] as &$def){
        
        $def['description'] = $def['description']." "
        .$def['synonyms']." "
        .$def['antonyms']." "
        .$def['hyperonyms']." "
        .$def['hyponyms']." "
        .$def['holonyms']." "
        .$def['meronyms']." ";
        
        unset($def['synonyms']);
        unset($def['antonyms']);
        unset($def['hyperonyms']);
        unset($def['hyponyms']);
        unset($def['holonyms']);
        unset($def['meronyms']);
        
        $examples = implode(" ", $def['examples']);
        $def['description'] = $def['description']." ".$examples;
        
        unset($def['examples']);
        
        $def['description'] = full_trim($def['description']);
        $def['description'] = deleteStopWords($def['description']);
        $def['description'] = clearText($def['description']);
        
        $def['description'] = LemmatizeString($def['description'], $morphy);
        $def['description'] = full_trim($def['description']);
        
        //соединяем семантические свойства с примером и с самим значением
    }
    
    
    return $dictionary;
}
//получить идентификаторы статей

function getSentence($sent){
    $string = "";
    try {
        if ($sent){
            foreach($sent as &$word){
        
            $string = $string." ".$word['text']." ".$word['meaning_desc']; 
            }
            
        }
        
   
    } catch (Exception $e) {
        var_dump($word);
        var_dump($sent);
    }
    return full_trim($string);
    
}

function getContext($number, $words, $sent_counts){
   // $context = "";
    //if ($number != 0){
        
      //  $context =  getSentence($words['sentences'][$number-1]);
    //}
    
    
    $context = $context." ".getSentence($words['sentences'][$number]);
    
    
   // if ($number < $sent_counts-1){
        
     //   $context = $context." ".getSentence($words['sentences'][$number+1]);
    //}
    
    return full_trim($context);
}

//получить твиты.
function getTwitterExamples($word, $database, $morphy){
    $q = $word; //запомнили запрос
    
    $forms = getFirstForms($word, $morphy);
    
    $tweets = getAllTweetsFromDatabase($forms, $database); //твиты всех форм получаем из БД
    $graph = new GraphBuilder();
    foreach ($tweets as $tweet){
    
        $words = explode(" ", $tweet);
    
        foreach ($words as &$word){
            if ($word != $q){ //если это не исследуемое слово, то обрабатываем
        
               $word = getBaseFrom($word, $morphy); //привели слово к нормальной форме
               
            } else {
                //удаляем исследуемое слово, чтобы не мешал.
                unset($word);
            }
        
         $tweet = implode(" ", $words);//возвращаем твит
         $graph->AddToGraph($tweet);
        
    
         }  
    
    }
    $context = $graph->getContexts();
    //все твиты добавлены
    return $context;

}

function lesk_method($context, $word, $dictionary){
    
   $i = 0;
   $max_def = 1; //наибольшее число совпадений
   $def_id = 1; //по умолчанию выбираем первое значение
   //echo $context; echo "<br>"; echo "<br>";
   $context = explode(" ", $context);
   $context = array_unique($context); //удалим повторяющиеся
   
   foreach ($dictionary['Defenitions'] as $def) {
       
        //$def['description'] = str_replace($word, "", $def['description']);
        //echo $def['description']; echo "<br>"; echo "<br>";
        
        //similar_text($context, $def['description'], $sim);
        //echo "для слова ".$word." контекст похож на ".$i."значение словаря на  ".$sim;
        //echo "<br>";
        //$i++;
        
        
        $def = explode(" ", $def['description']);
        $def = array_unique($def);
        $result = count(array_intersect($context, $def));
        
        if ($result > $max_def){ //если число совпадений больше, чем у нас уже есть
            $max_def = $result;
            $def_id = $i+1; //потому что значение для однозначных слов - 0
        }
        
        $i++;
   }
   
   $res = array();
   $res['max'] = $max_def;
   $res['def_id'] = $def_id;
   $res['description'] = $dictionary['Defenitions'][$def_id-1]['description'];
   $res['text'] = $word;
   return $res;   
    
}

//$url = "http://duceum.myjino.ru/parser/get_articles.php";
//$articles = getCurl($url);
//$articles = json_decode($articles, true);

//$articles = $articles['articles'];

//случайно берем статью.
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$context = ""; //все слова из контекста - текущее предложение, предложение-1, предложение+1
$explore_word = "";  //исследуемое слово


$id = getFirstArticleFromDatabase($database);
echo $id;
//foreach ($articles as $id){
    
    //получить статью с сервера для обработки
    $url = "http://duceum.myjino.ru/parser/get_words.php?id=$id";
    $words = getCurl($url);
    
    $words = json_decode($words, true);
    $words = $words['article'];
    
    //words - нормализовали words.
    $words = LemmatizeArticle($words, $morphy);
    

    //ищем многозначные слова
    
    $back_up_words = $words;
    
    $sentence_number = 0;
    $sent_count = count($words['sentences']);
    foreach ($words['sentences'] as &$sentence){
        
        
        
        foreach ($sentence as &$word){
            
            //обходим каждое слово, если встречаем слово у которого meaning_numbrt != 0, значит оно многозначное
            if (intval($word['meaning_number']) != 0){
                
               $dictionary = getDictionary($word['text'], $morphy);
               //get tweets
               
               //получаем контекст для исследуемого слова следующим образом - берем текущее предложение, а также предыдущее и последующее
               $context1 = getContext($sentence_number, $back_up_words, $sent_count);
               
               //$twit_examples = getTwitterExamples($word, $database, $morphy);
               
               $result = lesk_method($context, $word['text'], $dictionary);
               var_dump($result);
                
               pasteRobotMeaningINDatabase($word['word_id'], $result['def_id'], $result['description'], $database);   
            }
            
        }
        //следующее предложение
        echo "_____________________________________________<br>";
        $sentence_number++;
    }
    
    
    //}



updateArticle($id, $database); //отмечаем что статья уже была проверена.












?>