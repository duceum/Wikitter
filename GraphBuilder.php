<?php
require_once 'vendor/autoload.php';


use \Fhaculty\Graph\Graph as Graph;


class GraphBuilder{
    
    private $Graph_c = "";
   
    function __construct() {
       
        $this->Graph_c = new Graph();
   }
   
   
   
   public function AddToGraph($words){
        
        $this->addVertexes($words);
       
        $this->addEdges($words);
    }
   
   
   public function getContexts(){
       
       $limit = $this->getLimit();
       
       //удалили ребра
       $this->removeEdges($limit);
       $this->removeVerses();//удалили одинокие вершины
       
       $clusters =  $this->findClusters();
       $visited = "";
       return $clusters;
   }
   
   
    
 
    private $visited = array(); 
 
 
    private function findClusters(){
       $clusters = array();
       foreach ($this->Graph_c->getVertices() as $vers){
           
           if (!$this->visited[$vers->getID()]){ //начинаем с самой первой непосещенной
               $examples = array();
               $examples[]= $vers->getID(); //начинаем записывать компоненту связности
               $this->visited[$vers->getID()] = true;
               $examples = searchDFS($vers, $examples);
               $clusters[] = $examples; //добавили все вершины в кластер, когда обход был закончен
           }
       }
       return $clusters;

   }
 
    private function searchDFS($Verse, $examples){
        
        $this->visited[$Verse->getID()] = true;
        
        foreach ($this->Graph_c->getVerticesEdgeTo() as $vers){
            
             if (!$this->visited[$vers->getID()]){
                 $examples[]= $vers->getID();
                 $examples = searchDFS($vers, $examples);
                 return $examples;
             }
        }
        
    }
   
   
   
   
  
    

   private function getLimit(){
       
       $max = 0;
       $min = 0;
       $weight_count = array();
       
        //обходим ребра
        foreach ( $this->Graph_c->getEdges() as $edge){
        
            
            
                $weight = $edge->getWeight();
                $weight_count[$weight]++;
            
        
        }
        
        $max = max($weight_count);
        //$min = min($weight_count);
        $limit = 0;    
        for ($i = 0; $i < $weight_count; $i++){
            if ($count == $max){
                $limit = $i;
                break;
            }
        }
        
        var_dump($limit);
        return $limit+1;
   }
   
   
   
   private function removeVerses(){
   
   
    foreach ($this->Graph_c->getVertices() as $vers){
        
        if ($vers->getEdges() == NULL){
            $vers->destroy();
        }
        
        
    }
    
    
    
}
   
   private function removeEdges($limit){
    
    foreach ($this->Graph_c->getEdges() as $edge){
        
        if ($edge->getWeight()<=$limit  ){
            
            $edge->destroy();
        }
        
    }
    
    return $graph;
    
}
   
   private function addVertexes($words){
    
    //добавили вершины
    foreach ($words as &$word){
        
        
        if (!$this->Graph_c->hasVertex($word)){
            
            //если нет, то создаем
            $this->Graph_c->createVertex($word)->setBalance($b = 1);
            
            
        }else{
            
            $weight = $this->Graph_c->getVertex($word)->getBalance();
            $this->Graph_c->getVertex($word)->setBalance($weight+1);
            
        }
    
    }
    
    
}


    


    
  private function addEdges($words){
   
    $max = count($words);
    $current_word = 0; //начинаем c первого слова
       //устанавливаем текущее значение слова
     //добавили вершины, теперь добавляем ребра.
    foreach ($words as &$word){
        
        
        
        
        $next_word = $current_word;
        
        while ($next_word < $max) {
            
            $next_word++;
            echo $next_word; echo "</br>";
            
           
            if (!$this->Graph_c->getVertex($word)->hasEdgeFrom($this->Graph_c->getVertex($words[$next_word]))){
                //ребра нет
               
                $edge = $this->Graph_c->getVertex($word)->createEdge($this->Graph_c->getVertex($words[$next_word]));
                $edge->setWeight($weight = 1);
                echo $this->Graph_c->getVertex($word)->getID(); echo "11 <br>";
                echo $this->Graph_c->getVertex($words[$next_word])->getID();echo "22 <br>";
                
            }else{
                
                 foreach($this->Graph_c->getVertex($word)->getEdges() as $edge){
                     
                     
                         
                         $weight = $edge->getWeight();$weight++;
                         $edge->setWeight($weight);
                     
                 }
                
                
                
                      
                   //     -
                        
                //
                
                $this->Graph_c->getVertex($word)->getEdges()->getEdgeIndex(2)->setWeight($weight);
                 
                
            }
            
            
       
        };
        
        
        $current_word++;
        
        
        
        
        }
    
    
    
    }
  
    
    
    
}