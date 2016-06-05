<?php
  include_once "database.php";

  if($_GET["db_name"]){
    $database = new Database($_GET["db_name"]);

    if($_GET["method"]){
      switch($_GET["method"]){
        case "read";
          $data = $database->read();
        break;
        case "create":
          $database->create();
        break;
        case "update":
          $database->update($_GET["key"] , $_GET["val"]);
        break;
        case "insert":
          $database->insert($_GET["key"] , $_GET["val"]);
        break;
        case "remove":
          $database->remove($_GET["key"]);
        break;
        case "delete":
          $database->delete();
        break;
        case "listDatabases":
          $database->listDatabases();
        break;
      }
    }
  }
