<?php

require_once('TwitterAPIExchange.php');
require_once ('medoo.min.php');
function deleteStopWords($text){
    //$s = trim(preg_replace('#\s(и|в|во|не|что|он|на|я|с|со|как|а|то|все|она|так|его|но|да|ты|к|у|же|вы|за|бы|по|только|ее|мне|было|вот|от|меня|еще|нет|о|из|ему|теперь|когда|даже|ну|вдруг|ли|если|уже|или|ни|быть|был|него|до|вас|нибудь|опять|уж|вам|сказал|ведь|там|потом|себя|ничего|ей|может|они|тут|где|есть|надо|ней|для|мы|тебя|их|чем|была|сам|чтоб|без|будто|человек|чего|раз|тоже|себе|под|жизнь|будет|ж|тогда|кто|этот|говорил|того|потому|этого|какой|совсем|ним|здесь|этом|один|почти|мой|тем|чтобы|нее|кажется|сейчас|были|куда|зачем|сказать|всех|никогда|сегодня|можно|при|наконец|два|об|другой|хоть|после|над|больше|тот|через|эти|нас|про|всего|них|какая|много|разве|сказала|три|эту|моя|впрочем|хорошо|свою|этой|перед|иногда|лучше|чуть|том|нельзя|такой|им|более|всегда|конечно|всю|между|мной|мною|тебе|тобой|тобою|нему|эи|ею|нэи|нею|оно|нам|нами|вами|ими|ними|собой|собою|эта|это|эты|этих|этому|этим|этою|этими|та|те|ту|той|тех|тому|тою|теми|весь|вся|всей|всему|всем|всею|всеми|сама|само|сами|самого|саму|самих|самой|самому|самим|самою|самими|самом|нельзя)\s#uis', ' ', ' '.$text.' '));
    $s = trim(preg_replace('#\s(и|в|же|RT|во|вы|чьей|не|что|за|он|на|я|с|со|как|а|то|все|она|так|его|но|да|ты|к|у|же|вы|за|бы|по|только|ее|мне|было|вот|от|меня|еще|нет|о|из|ему|когда|даже|ну|вдруг|ли|если|уже|или|ни|быть|был|него|до|вас|нибудь|опять|уж|вам|ведь|там|себя|ничего|ей|может|они|тут|где|есть|надо|ней|для|мы|тебя|их|чем|была|сам|чтоб|без|будто|чего|раз|тоже|себе|под|ж|тогда|кто|этот|того|потому|этого|какой|ним|этом|один|почти|мой|тем|чтобы|нее|были|куда|зачем|всех|при|об|хоть|над|тот|через|эти|нас|про|всего|них|какая|много|разве|три|эту|моя|свою|этой|перед|иногда|лучше|чуть|том|такой|им|более|конечно|всю|между|мной|мною|тебе|тобой|тобою|нему|эи|ею|нэи|нею|оно|нам|нами|вами|ими|ними|собой|собою|эта|это|эты|этих|этому|этим|этою|этими|та|те|ту|той|тех|тому|тою|теми|я|вся|всей|всему|всем|всею|всеми|сама|само|сами|самого|саму|самих|самой|самому|самим|самою|самими|самом|нельзя)\s#uis', ' ', ' '.$text.' '));
    return $s;
    
}


function clearText($text) {

    $clean_text = "";

    // Match Emoticons
    $regexEmoticons = '/[\x{1F600}-\x{1F64F}]/u';
    $clean_text = preg_replace($regexEmoticons, '', $text);

    // Match Miscellaneous Symbols and Pictographs
    $regexSymbols = '/[\x{1F300}-\x{1F5FF}]/u';
    $clean_text = preg_replace($regexSymbols, '', $clean_text);

    // Match Transport And Map Symbols
    $regexTransport = '/[\x{1F680}-\x{1F6FF}]/u';
    $clean_text = preg_replace($regexTransport, '', $clean_text);

    // Match Miscellaneous Symbols
    $regexMisc = '/[\x{2600}-\x{26FF}]/u';
    $clean_text = preg_replace($regexMisc, '', $clean_text);

    // Match Dingbats
    $regexDingbats = '/[\x{2700}-\x{27BF}]/u';
    $clean_text = preg_replace($regexDingbats, '', $clean_text);

    $clean_text = mb_strtolower($clean_text, 'UTF-8');

    $regSymbols = '#(\.|\?|!|\(|\)){3,}#';
    $clean_text = preg_replace($regSymbols, '', $clean_text);
    
    $regSymbols = '/@([a-z0-9_\.-]+)/';
    $clean_text = preg_replace($regSymbols, '', $clean_text);
    
    //$regSymbols = '#([a-я0-9_\.-]){1,}#';
    $clean_text = preg_replace("|\b[\d\w]{0,1}\b|i","",$clean_text); 
     
    $regUrl = '#(?<!\])\bhttp://[^\s\[<]+#i';
    $clean_text = preg_replace($regUrl, '', $clean_text);
    
    $regUrl = '#(?<!\])\bhttps://[^\s\[<]+#i';
    $clean_text = preg_replace($regUrl, '', $clean_text);
    
    
    // Удаляем знаки припенания 
    
    $vowels = array("#", ":", ";","–" ,".",",","(",")", "!","|","…", "«", "»", '"',"?", "&",'=',']','[');
    $clean_text = str_replace($vowels, "", $clean_text);
    
    $clean_text = preg_replace('/\d+/', '',$clean_text);

    return $clean_text;
}



function getTweets($q, $max){
    
    
    
    $settings = array(
    'oauth_access_token' => "96160166-lAHu0oaniNuQBVcaegWKQRJXJPise1786w1EnSxDi",
    'oauth_access_token_secret' => "S1LKtCdFiEu6lHoxc3fyNq6CqQDLi66uhgoUm2xIR0s6w",
    'consumer_key' => "WFtlzCFzlaADa6CpOEeVeooz8",
    'consumer_secret' => "yejE2VV2IUknvF2RFl7vxOyi40WinwNcurkO2wAeY8cEbfAzAp"
);
    
    $url = 'https://api.twitter.com/1.1/search/tweets.json';
    
    if (intval($max) > 0){
        $getfield = "?q=$q&count=100&result_type=recent&max_id=$max&lang=ru ";
    }else{
        $getfield = "?q=$q&count=100&result_type=recent&lang=ru ";
    }
    
   
    $requestMethod = 'GET';

    $twitter = new TwitterAPIExchange($settings);
     $tweets =  $twitter->setGetfield($getfield)
    ->buildOauth($url, $requestMethod)
    ->performRequest();

    return $tweets;
}
function getQ(){
    return (!empty( $_GET['q'])) ?  $_GET['q'] : false;
}

//Используем простой синтаксис библиотеки medoo
function getTweetsFromDatabase($q, $database){
    
    $data = $database->select('tweets', [ 'tweet' ],
        [ 'query_key' => $q ]);
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
// %23 - код хештега.
$max_id = "";
$q = getQ();
$result = array();




if ($q){
        
        $tweets = getTweetsFromDatabase($q, $database);
        
        foreach ($tweets as $tweet){
    
                $result[] = $tweet['tweet'];
        }
        
        if (count($tweets) < 300){
            //обращаемся к твиттеру
            $getfield = "?q=$q&count=100&result_type=recent";
            for ($i = 1; $i <= 25; $i++) {
    
                $tweets = getTweets($q, $max_id);
                
                $tweets = array(json_decode($tweets,  true));  
                $tweets = $tweets[0];
    
                if ($tweets['statuses'] != NULL){
                    foreach ($tweets['statuses'] as $tweet){
    
                        $max_id = intval($tweet['id']-1);
                        $text = $tweet['text'];
   
                        $text = clearText($text);
                        $text = deleteStopWords($text);
    
                        $text = trim($text);
                        $result[] = $text;
                    }
                    
                    
                }else{
        
                    break;
                }
            }
            
            
            $result = array_unique($result);
            insertTweets($result, $q, $database);
            
            
        //получаем твиты из БД    
        }
    
    header("HTTP/1.0 200 Ok");        
    header("Content-type: application/json; charset=utf-8");
    echo json_encode(array('tweets' => $result));
    exit();

    
}else{
    header("HTTP/1.0 400 Bad Params");        
    header("Content-type: application/json; charset=utf-8");
    echo json_encode(array('error' => "400 Error. Bad params.")); 
    exit();
}











 
 
 
 




















?>