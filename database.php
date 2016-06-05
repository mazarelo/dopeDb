<?php
class Database{

  private $dbLocaltion = "dbFiles";
  private $db;

  function __construct($dbName){
    $this->db = $dbName;
    return $this->checkDbBaseFolder();
  }

  private function validateFile(){
    $handle = $this->readFile();
    if(!$handle){
      return $this->createFile();
    }
    return $handle;
  }

  private function getJsonData(){
    return file_get_contents("https://$_SERVER[HTTP_HOST]/backoffice/db/$this->dbLocaltion/$this->db.json");
  }

  private function checkDbBaseFolder(){
    if(!file_exists($this->dbLocaltion)){
      $oldmask = umask(0);
      mkdir ($this->dbLocaltion, 0755);
      umask($oldmask);
      return true;
    }
    return true;
  }

  private function removeObject($key){
    $oldFile = (object) json_decode($this->getJsonData());
    $db = $this->db;
    unset($oldFile->$db->$key);
    $newFile = json_encode($oldFile);
    return file_put_contents("$this->dbLocaltion/$this->db.json", $newFile);
  }

  private function updateFile($key , $value){
    $oldFile = (object) json_decode($this->getJsonData());
    $db = $this->db;
    $oldFile->$db->$key = $value;
    $newFile = json_encode($oldFile);
    return file_put_contents("$this->dbLocaltion/$this->db.json", $newFile);
  }

  public function create(){
    if(!file_exists("$this->dbLocaltion/$this->db.json")){
      $jsonTemplate = (object) array($this->db => "", 'date' => date("Y/m/d"));
      return file_put_contents("$this->dbLocaltion/$this->db.json",json_encode($jsonTemplate));
    }
    return true;
  }

  public function insert($key,$val){
    return $this->updateFile($key , $val);
  }

  public function remove($key){
    return $this->removeObject($key);
  }

  public function restore(){
    if(file_exists("archived/$this->db.json")){
      return rename("archived/$this->db.json", "$this->dbLocaltion/$this->db.json");
    }
  }

  public function delete(){
    if(file_exists("$this->dbLocaltion/$this->db.json")){
      if(!file_exists("archived")){
        $oldmask = umask(0);
        mkdir ("archived", 0755);
        umask($oldmask);
      }
      return rename ("$this->dbLocaltion/$this->db.json", "archived/$this->db.json");
    //return unlink("$this->dbLocaltion/$this->db.json");
    }
    return false;
  }

  public function read(){
    echo $this->getJsonData();
  }

  public function update($key , $val){
    return $this->updateFile($key , $val);
  }

  public function backupDb(){
    return true;
  }

  public function listDatabases(){
    return print json_encode(array_values(preg_grep('/^([^.])/', scandir($this->dbLocaltion))));
  }

}
