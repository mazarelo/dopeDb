# jsonDb
Json+php light Database class

Super easy database with Json files.

<h2>Initialize DB - pass a name on the parameter</h2>
```
/* Use this to initialize the DB */
$database = new Database("users");
```
<h2>Generate (choosen_name).json</h2>
```
$database->create();
```
<h2>Insert data in the .json</h2>
```
$key = "firstName";
$value = "Joao";
$database->insert($key, $value);
```
<h2>Read the .json</h2>
```
$database->read();
```
<h2>Update values in the .json</h2>
```
$key = "firstName";
$updatedValue = "Joao";
$database->insert($key, $updatedValue);
```
<h2>Remove Object from .json</h2>
```
$key = "firstName";
$database->remove($key);
```
<h2>Soft Delete - creates a archive folder and moves the .json there</h2>
```
$database->delete();
```
<h2>Restores the previous archived .json to the Database folder</h2>
```
$database->restore();
```
<h2>List all files in dbFiles folder</h2>
```
$database->listDatabases();
```
