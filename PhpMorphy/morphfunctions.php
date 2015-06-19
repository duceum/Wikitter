<?php
// first we include phpmorphy library
set_include_path(__DIR__ . '/src/' . PATH_SEPARATOR . get_include_path());
require('phpMorphy.php');

// set some options
$opts = array(
    // storage type, follow types supported
    // PHPMORPHY_STORAGE_FILE - use file operations(fread, fseek) for dictionary access, this is very slow...
    // PHPMORPHY_STORAGE_SHM - load dictionary in shared memory(using shmop php extension), this is preferred mode
    // PHPMORPHY_STORAGE_MEM - load dict to memory each time when phpMorphy intialized, this useful when shmop ext. not activated. Speed same as for PHPMORPHY_STORAGE_SHM type
    'storage' => PHPMORPHY_STORAGE_FILE,
    // Enable prediction by suffix
    'predict_by_suffix' => true, 
    // Enable prediction by prefix
    'predict_by_db' => true,
    // TODO: comment this
    'graminfo_as_text' => true,
);

// Path to directory where dictionaries located
$dir = __DIR__ . '/dicts/utf-8';
$lang = 'ru_RU';


function getBaseFrom($word, $morphy){

    $word = mb_convert_case($word, MB_CASE_UPPER, "UTF-8");
    
    if(function_exists('iconv')) {
        
        $word = iconv('utf-8', $morphy->getEncoding(), $word);
        $base = $morphy->getBaseForm($word);
        
        return mb_convert_case($base[0] , MB_CASE_LOWER, "UTF-8");
   
    }    
    
    
}


function getFirstForms($word, $morphy){

    $word = mb_convert_case($word, MB_CASE_UPPER, "UTF-8");
    
    if(function_exists('iconv')) {
        
        $word = iconv('utf-8', $morphy->getEncoding(), $word);
        $base = $morphy->getAllForms($word);
        
        $forms = array();
        $j = 0;
        if ($base['0'] != NULL) {
            foreach ($base as $form){
                if ($j < 6){
                    $forms[] = mb_convert_case($form , MB_CASE_LOWER, "UTF-8");
                    $j++;
                }else{
                    break;
                }
            }
        }
        
        
        return $forms;
   
    }    
    
    
}



$morphy = "";
// Create phpMorphy instance
try {
    $morphy = new phpMorphy($dir, $lang, $opts);
} catch(phpMorphy_Exception $e) {
    die('Error occured while creating phpMorphy instance: ' . PHP_EOL . $e);
}

// All words in dictionary in UPPER CASE, so don`t forget set proper locale via setlocale(...) call
// $morphy->getEncoding() returns dictionary encoding

//$word = 'история';
//echo getBaseFrom($word, $morphy);
//var_dump (getFirstForms($word, $morphy));



