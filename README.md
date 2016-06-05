# jsonDb
Json+php light Database class

Super easy database with Json files.

<h2>Initialize DB</h2>
```
/* Use this to initialize the DB */
$database = new Database("users");
```
<h2>Generate users.json file</h2>
```
$database->create();
```
<h2>Insert data in the file</h2>
```
$key = "firstName";
$value = "Joao";
$database->insert($key, $value);
```
<h2>Read a Json file</h2>
```
$database->read();
```
<h2>Update values in the json file</h2>
```
$key = "firstName";
$updatedValue = "Joao";
$database->insert($key, $updatedValue);
```
<h2>Remove Object from json</h2>
```
$key = "firstName";
$database->remove($key);
```
<h2>Delete a Json file</h2>
```
$database->delete();
```
<h2>List all files</h2>
```
$database->listDatabases();
```
