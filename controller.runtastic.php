<?php
  include_once("class.runtastic.php");
  
  class Runtastic_Controller{
    private $runtastic, $activities, $map_height, $map_width, $display_language;
    public $error = false;
    
    public function __construct($username,$password,$display_language, $map_height =  "125" , $map_width ="150") {
      $this->runtastic = New Runtastic();
      $this->runtastic->setUsername($username);
      $this->runtastic->setPassword($password);
    	$this->runtastic->setTimeout(30);
    	if ($this->runtastic->login()) {
    	  $this->activities = $this->runtastic->getActivities();
    	}else{
    	  $this->error = true;
    	}
        $this->map_height = $map_height;
        $this->map_width = $map_width;
        $this->display_language = $display_language;
    }

    
    public function get_last_activity(){ 
     $data = array();
      $data["type"] = $this->format_type($this->activities[0]->type_id, $this->display_language);
      $data["duration"] = $this->format_duration($this->activities[0]->duration);
      $data["distance"] = $this->format_distance($this->activities[0]->distance);
      $data["pace"] = $this->format_pace($this->activities[0]->pace);
      $data["map"] = $this->format_map((string)$this->activities[0]->map_url);
      return $data;
    }
    
    
    
    private function format_type($type, $display_language){
    include('RuntasticIDs.php'); 
    
      
      if(empty($RuntasticIDs[$type])){
        return "Nicht defininiert";
      }else{
        return $RuntasticIDs[$type];     
      }
          
    }
    
    private function format_duration($duration){
      return gmdate("H:i:s",$duration/1000);
    }
    
    private function format_distance($distance){
      return round($distance/1000,2) . " km";
    }
    
    private function format_pace($pace){
      $pace = explode(".",$pace);
      $Min = $pace[0];
      // Str_Replace um bei großen Zahlen das Komma zu entfernen
      $Sec = substr(str_replace(".", "", $pace[1]*60),0,2);
      return $Min . ":" . $Sec ." min/km";
    }
    
    private function format_map($map){
      $map = str_replace("width=50","width=" . $this->map_width,$map);
      $map = str_replace("height=70", "height=" . $this->map_height, $map);
      
      $str_length = strlen($map)-1;
      $map = substr($map,2,$str_length);  
      return "http://" . $map;
    }
    
  }

?>
