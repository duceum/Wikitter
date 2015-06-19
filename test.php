<?php
require_once ('GraphBuilder.php');
require_once ('DBWrapper.php');
require_once ('../PhpMorphy/morphfunctions.php');
$forms = array();

$forms[] = 'президент';
$forms[] = 'президенту';

//$tweets = getAllTweetsFromDatabase($forms, $database); //твиты всех форм получаем из БД

$tweets = array(); 
$tweets[] = "принять закон первый запрет чтение алкогольный энергетик москва";
$tweets[] = "рада принять закон первый чтение призыв поправка президентский";
$graph = new GraphBuilder();


    
    foreach ($tweets as $tweet){
    
        $words = explode(" ", $tweet);
         $result = array();
        foreach ($words as &$word){
           
            if ($word != $q){ //если это не исследуемое слово, то обрабатываем
        
               $word = getBaseFrom($word, $morphy); //привели слово к нормальной форме
               if (($word == $q) || ($word == " ") || ($word == "") || ($word == NULL)){ unset($word); }else {$result[] = $word;}
               
            } else {
                //удаляем исследуемое слово, чтобы не мешал.
                unset($word);
            }
        
         //$tweet1 = implode(" ", $result);//возвращаем твит
         
         
        
            
         }
         var_dump($result);
         $graph->AddToGraph($result);
    
    }
    $context = $graph->getContexts();
    //все твиты добавлены
    return $context;