<?php
class Database{

  private $dbFolderName = "dbFiles";
  private $db;
  private $imageFolderName = "uploads";
  function __construct($dbName){
    $this->db = $dbName;
    return $this->checkDbBaseFolder();
  }

  /* ------------------------------------ helpers ---------------------------- */

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

  private function renameJsonFile($old,$new){
    return rename ("$old.json","$new.json");
  }

  private function checkIfJsonFileExists($location){
      return (file_exists($location) == true) ? true : false;
  }

  private function checkDbBaseFolder(){
    return $this->createFolder($this->dbFolderName);;
  }

  private function listAllImages($location){
    $images = glob("$location/*.{jpeg,gif,png}", GLOB_BRACE);
    return json_encode($images);
  }

  private function listAllJsonFiles($location){
    return json_encode(array_values(preg_grep('/^([^.])/', scandir($location))));
  }

  /* ---------------------------- Private functions --------------------------- */

  private function removeObject($key){
    $oldFile = (object) json_decode($this->getJsonData());
    $db = $this->db;
    unset($oldFile->$db->$key);
    $newFile = json_encode($oldFile);
    file_put_contents("$this->dbFolderName/$this->db.json", $newFile);
    return $this->getJsonData();
  }
  
  private function renameDatabase($new){
    $this->renameJsonFile("$this->dbFolderName/$this->db" , "$this->dbFolderName/$new");
    return $this->listAllJsonFiles($this->dbFolderName);
  }

  private function updateFile($key , $value){
    $oldFile = (object) json_decode($this->getJsonData());
    $db = $this->db;
    $oldFile->$db->$key = $value;
    $newFile = json_encode($oldFile);
    file_put_contents("$this->dbFolderName/$this->db.json", $newFile , LOCK_EX );
    return $this->getJsonData();
  }

  private function restoreFromArchive(){
    if($this->checkIfJsonFileExists("archived/$this->db.json")){
      $this->renameJsonFile("archived/$this->db","$this->dbFolderName/$this->db");
      return $this->listAllJsonFiles("archived");
    }
    return false;
  }

  private function moveToArchive(){
    if($this->checkIfJsonFileExists("$this->dbFolderName/$this->db.json")){
      $this->createFolder("archived");
      $this->renameJsonFile("$this->dbFolderName/$this->db","archived/$this->db");
      return $this->listAllJsonFiles($this->dbFolderName);
    }
    return false;
  }

  private function moveToBackup(){
    $this->createFolder("backups");
    if(checkIfJsonFileExists()){
        copy("$this->dbFolderName/$this->db.json", "backups/backup_".date("Y-m-d")."_$this->db.json");
        return $this->listAllJsonFiles($this->dbFolderName);
    }
    return false;
  }

  private function permanentDelete(){
    if($this->checkIfJsonFileExists("$this->dbFolderName/$this->db.json")){
      return unlink("$this->dbFolderName/$this->db.json");
    }
    return false;
  }
  /* to be worked on */
  private function queryJsonFile($query){
    $file = (array) json_decode($this->getJsonData());
    $q = strtolower($query);
    $arr = (object) array("results" => array());

    foreach ($file as $key => $value) {
      foreach($value as $sin => $val){
        if(strpos(strtolower($val), $q)  !== false ){
          $obj = (object) array($sin => $val);
          array_push( $arr->results , $obj);
        }
      }
    }
    return json_encode($arr);
  }

  private function uploadAndResizeImages(){
    if(isset($_FILES)){
      include "../app/classes/images.php";
      $num_files = count($_FILES['file']['name']);
      $this->createFolder($this->imageFolderName);
      $this->createFolder("$this->imageFolderName/$this->db");
      $i = 0;
      foreach($_FILES['file']['tmp_name'] as $key => $value) {
        $file_name = $_FILES['file']['name'][$key];
        $file_type = $_FILES['file']['type'][$key];
        $file_size = $_FILES['file']['size'][$key];
        $file_tmp  = $_FILES['file']['tmp_name'][$key];
        $picture =  $_FILES['file']['name'][$key];

        $handle = new upload($file_tmp);

        if ($handle->uploaded) {
          $handle->file_new_name_body   = 'image_resized'."_".$i;
          $handle->image_resize         = true;
          $handle->image_x              = 1500;
          $handle->image_ratio_y        = true;
          $handle->image_convert = 'jpg';
          $handle->jpeg_quality = 85;
          $handle->process("$this->imageFolderName/$this->db");

          if ($handle->processed) {
            echo 'image resized';
            $handle->clean();
          } else {
            echo 'error : ' . $handle->error;
          }
        }else{
          echo "wrong";
        }
        $i++;
      }
      return $this->listAllImages($location);
    }
  }

  private function createJsonFile(){
    if(!$this->checkIfJsonFileExists("$this->dbFolderName/$this->db.json")){
      $jsonTemplate = (object) array($this->db => "");
      file_put_contents("$this->dbFolderName/$this->db.json",json_encode($jsonTemplate) , LOCK_EX);
      return $this->listAllJsonFiles($this->dbFolderName);
    }
    return $this->listAllJsonFiles($this->dbFolderName);
  }

  /* ------------------------- Public functions ------------------------- */

  public function create(){
    return $this->createJsonFile();
  }

  public function query($query){
    /* yet to come */
    return $this->queryJsonFile($query);
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
    return $this->getJsonData();
  }

  public function update($key , $val){
    return $this->updateFile($key , $val);
  }

  public function rename($new){
      return $this->renameDatabase($new);
  }

  public function upload(){
    return $this->uploadAndResizeImages();
  }

  public function backup(){
    return $this->moveToBackup();
  }

  public function listDatabases(){
    return $this->listAllJsonFiles($this->dbFolderName);
  }

  public function listImages(){
    return $this->listAllImages("$this->imageFolderName/$this->db");
  }

}
