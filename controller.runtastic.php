<?php
  include_once("class.runtastic.php");
  
  class Runtastic_Controller{
    private $Runtastic, $Activities, $Username,$Password, $Map_Height, $Map_Width, $Display_Language;
    public $error = false;
    
    public function __construct($username,$password,$Display_Language, $Map_Height =  "125" , $Map_Width ="150") {
      $this->Runtastic = New Runtastic();
      $this->Runtastic->setUsername($username);
      $this->Runtastic->setPassword($password);
    	$this->Runtastic->setTimeout(30);
    	if ($this->Runtastic->login()) {
    	  $this->Activities = $this->Runtastic->getActivities();
    	}else{
    	  $this->error = true;
    	}
        $this->Map_Height = $Map_Height;
        $this->Map_Width = $Map_Width;
        $this->Display_Language = $Display_Language;
    }

    
    public function GetLastActivity(){ 
     $data = array();
      $data["type"] = $this->formatType($this->Activities[0]->type_id, $this->Display_Language);
      $data["duration"] = $this->formatDuration($this->Activities[0]->duration);
      $data["distance"] = $this->formatDistance($this->Activities[0]->distance);
      $data["pace"] = $this->formatPace($this->Activities[0]->pace);
      $data["map"] = $this->formatMap((string)$this->Activities[0]->map_url);
      return $data;
    }
    
    
    
    private function formatType($type, $display_language){
      if($display_language == "Englisch" OR $display_language == "English"){
                include('RuntasticIDs_Englisch.php'); 
    }else{
        include('RuntasticIDs_Deutsch.php'); 
    } 
      
      if(empty($RuntasticIDs[$type])){
        return "Nicht defininiert";
      }else{
        return $RuntasticIDs[$type];     
      }
          
    }
    
    private function formatDuration($duration){
      return gmdate("H:i:s",$duration/1000);
    }
    
    private function formatDistance($distance){
      return round($distance/1000,2) . " km";
    }
    
    private function formatPace($pace){
      $pace = explode(".",$pace);
      $Min = $pace[0];
      // Str_Replace um bei großen Zahlen das Komma zu entfernen
      $Sec = substr(str_replace(".", "", $pace[1]*60),0,2);
      return $Min . ":" . $Sec ." min/km";
    }
    
    private function formatMap($map){
      $map = str_replace("width=50","width=" . $this->Map_Width,$map);
      $map = str_replace("height=70", "height=" . $this->Map_Height, $map);
      
      $strLength = strlen($map)-1;
      $map = substr($map,2,$strLength);  
      return "http://" . $map;
    }
    
  }

?>
