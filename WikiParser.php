<?php
//ini_set('error_reporting', E_STRICT);
include 'simple_html_dom.php';
//ТОТАЛЬНОЕ ШАМАНСТВО
Class WikiParser{
    
protected $lang = "ru";
    
public function getWord( $word ){
    
    $html = $this->getHTML($word);
   
    if (($html != NULL ) && ($html != '')){
        
        
        $wiki_data = $this->getWikiData($html);
        return $wiki_data;
    }else{
        //если ничего не вернулось
        return NULL;
    }
} 

private function getHTML($word){
    
    $word = urlencode($word);
	$url = 'https://ru.wiktionary.org/w/index.php?title='.$word.'&printable=yes';
	$html = @file_get_html($url);
	
	return $html;
    
}

private function getWikiData($html){
    $array = array();
    
    //определяем часть речи
    $PartOfSpeech = $html->find('#mw-content-text p');
		$PartOfSpeech = $PartOfSpeech[3]->plaintext;
		$PartOfSpeech = preg_replace('/[^-\w\sЁёА-Яа-я]/u', '', $PartOfSpeech);
		$PartOfSpeech = str_replace(' - ', '', $PartOfSpeech);
		$PartOfSpeech = str_replace('160', '', $PartOfSpeech);
		$str=strpos($PartOfSpeech, " ");
		$PartOfSpeech=substr($PartOfSpeech, 0, $str); 
		$PartOfSpeech = mb_strtolower($PartOfSpeech, 'UTF-8');
		$array['Part-of-speech'] = $PartOfSpeech;



		if($array['Part-of-speech'] == 'существительное'){
			$Table = $html->find('table[rules=all]');
			$Table = $Table[0]->find('tr');
			
			//смотрим таблицу склонений
			$Sklonenie = array();
			for ($i = 1; $i < 7; $i++){
				$skl = $Table[$i]->find('td', 1)->plaintext;
				$skl = preg_replace('/[^-\w\sЁёА-Яа-я]/u', '', $skl);
				$skl = str_replace(' - ', '', $skl);
				$skl = preg_replace('|\s+|', ' ', $skl);
				$skl = str_replace('160', '', $skl);
				$Sklonenie[] = $skl;
			}
			
			$Sklonenie = implode('+', $Sklonenie);

			$array['Sklonenie'] = $Sklonenie;
		}



		$Znachenie = $html->find('ol', 0)->find('li');


		$Examples = array();

		for($i = 0; $i < count($Znachenie); $i++){
		
						
			foreach($Znachenie[$i]->find('span.example-block') as $element){
				$e = $element;
			
				$e->find('span.example-details', 0)->outertext=""; //Выдает warning, разобраться потом.
					
				$e = strip_tags($e);
				$e = preg_replace('/[^-\w\sЁёА-Яа-я]/u', '', $e);
				$e = str_replace(' - ', '', $e);
				$e = str_replace('160', '', $e);
				$e = preg_replace('|\s+|', ' ', $e);
				$e = mb_strtolower($e, 'UTF-8');
				
				$Examples[$i][] = $e;
				
				$element->outertext = "";
			}
			
			foreach($Znachenie[$i]->find('span.example-fullblock') as $element){
				$element->outertext = '';
			}
			
			foreach($Znachenie[$i]->find("span[style*=italic]") as $element){
				$element->outertext = '';
			}
			
			

			
			$Znachenie[$i] = strip_tags($Znachenie[$i]);
			$Znachenie[$i] = preg_replace('/[^-\w\sЁёА-Яа-я]/u', '', $Znachenie[$i]);
			$Znachenie[$i] = str_replace(' - ', '', $Znachenie[$i]);
			$Znachenie[$i] = str_replace('160', '', $Znachenie[$i]);
			$Znachenie[$i] = preg_replace('|\s+|', ' ', $Znachenie[$i]);
			$Znachenie[$i] = mb_strtolower($Znachenie[$i], 'UTF-8');
				
		}




	

		$Synonyms = $this->Onyms($html,"h4 span[id='.D0.A1.D0.B8.D0.BD.D0.BE.D0.BD.D0.B8.D0.BC.D1.8B']", 0);
		$Antonyms = $this->Onyms($html,"h4 span[id='.D0.90.D0.BD.D1.82.D0.BE.D0.BD.D0.B8.D0.BC.D1.8B']", 0);
		$Hyperonyms = $this->Onyms($html,"h4 span[id='.D0.93.D0.B8.D0.BF.D0.B5.D1.80.D0.BE.D0.BD.D0.B8.D0.BC.D1.8B']", 0);
		$Hyponyms = $this->Onyms($html,"h4 span[id='.D0.93.D0.B8.D0.BF.D0.BE.D0.BD.D0.B8.D0.BC.D1.8B']", 0);
		$Holonyms = $this->Onyms($html,"h4 span[id='.D0.A5.D0.BE.D0.BB.D0.BE.D0.BD.D0.B8.D0.BC.D1.8B']", 0);
		$Meronyms = $this->Onyms($html,"h4 span[id='.D0.9C.D0.B5.D1.80.D0.BE.D0.BD.D0.B8.D0.BC.D1.8B']", 0);

		$array['Defenitions'] = array();

		for($i = 0; $i < count($Znachenie); $i++){
			$array['Defenitions'][$i]['description'] = $Znachenie[$i];
			$array['Defenitions'][$i]['examples'] = $Examples[$i];
			$array['Defenitions'][$i]['synonyms'] = $Synonyms[$i];
			$array['Defenitions'][$i]['antonyms'] = $Antonyms[$i];
			$array['Defenitions'][$i]['hyperonyms'] = $Hyperonyms[$i];
			$array['Defenitions'][$i]['hyponyms'] = $Hyponyms[$i];
			$array['Defenitions'][$i]['holonyms'] = $Holonyms[$i];
			$array['Defenitions'][$i]['meronyms'] = $Meronyms[$i];
		}



		//echo '<pre>';
		//print_r($array);
		//echo '</pre>';
		//$json = json_encode($array);
		
		foreach ($array['Defenitions'] as &$def){
		    
		    if ($def['examples'][0] == "не указан пример употребления см рекомендации "){
		        
		        $def['examples'][0] = "";
		    }
		}
		
		
	
	return $array;
}

private	function Onyms($html, $selector, $pos){
			$Onyms = array();
			if($html->find($selector, $pos)->plaintext != ''){
				$search = $html->find($selector, $pos)->parent()->next_sibling();
				
				if($search->tag == 'ol'){
					
					foreach($search->find('li') as $element){
						$string ='';
						
						foreach($element->find('a, strong.selflink') as $e){
								
								$string.= ' '.$e;
														
						}
						$string = trim($string);
						
						$string = strip_tags($string);
						$string = preg_replace('/[^-\w\sЁёА-Яа-я]/u', '', $string);
						$string = str_replace(' - ', '', $string);
						$string = str_replace('160', '', $string);
						$string = preg_replace('|\s+|', ' ', $string);
						$Onyms[] = $string;
					}
				}
			}
			
			
			
			return $Onyms;
		}
    
}

?>