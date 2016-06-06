<?php
class Database{

  private $dbFolderName = "dbFiles";
  private $db;

  function __construct($dbName){
    $this->db = $dbName;
    return $this->checkDbBaseFolder();
  }

  private function getJsonData(){
    return file_get_contents("$this->dbFolderName/$this->db.json");
  }

  private function createFolder($name){
    if(!file_exists($name)){
      $oldmask = umask(0);
      mkdir ($name, 0755);
      umask($oldmask);
    }
  }

  private function checkDbBaseFolder(){
    return $this->createFolder($this->dbFolderName);;
  }

  private function removeObject($key){
    $oldFile = (object) json_decode($this->getJsonData());
    $db = $this->db;
    unset($oldFile->$db->$key);
    $newFile = json_encode($oldFile);
    return file_put_contents("$this->dbFolderName/$this->db.json", $newFile);
  }

  private function updateFile($key , $value){
    $oldFile = (object) json_decode($this->getJsonData());
    $db = $this->db;
    $oldFile->$db->$key = $value;
    $newFile = json_encode($oldFile);
    return file_put_contents("$this->dbFolderName/$this->db.json", $newFile , LOCK_EX );
  }

  private function restoreFromArchive(){
    if(file_exists("archived/$this->db.json")){
      return rename("archived/$this->db.json", "$this->dbFolderName/$this->db.json");
    }
    return false;
  }

  private function moveToArchive(){
    if(file_exists("$this->dbFolderName/$this->db.json")){
      $this->createFolder("archived");
      return rename ("$this->dbFolderName/$this->db.json", "archived/$this->db.json");
    }
    return false;
  }

  private function moveToBackup(){
    $this->createFolder("backups");
    if(file_exists("$this->dbFolderName/$this->db.json")){
        return copy("$this->dbFolderName/$this->db.json", "backups/backup_".date("Y-m-d")."_$this->db.json");
    }
    return false;
  }

  private function permanentDelete(){
    if(file_exists("$this->dbFolderName/$this->db.json")){
      return unlink("$this->dbFolderName/$this->db.json");
    }
    return false;
  }

  public function create(){
    if(!file_exists("$this->dbFolderName/$this->db.json")){
      $jsonTemplate = (object) array($this->db => "");
      return file_put_contents("$this->dbFolderName/$this->db.json",json_encode($jsonTemplate) , LOCK_EX);
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
    return $this->restoreFromArchive();
  }

  public function purge(){
    return $this->permanentDelete();
  }

  public function deleteBackup(){
    /* yet to come */
  }

  public function delete(){
    return $this->moveToArchive();
  }

  public function read(){
    echo $this->getJsonData();
  }

  public function update($key , $val){
    return $this->updateFile($key , $val);
  }

  public function backup(){
    return $this->moveToBackup();
  }

  public function listDatabases(){
    return print json_encode(array_values(preg_grep('/^([^.])/', scandir($this->dbFolderName))));
  }

}
