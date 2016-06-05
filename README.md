# jsonDb
Json+php light Database class

Super easy database with Json files.

<h2>Initialize DB</h2>
```
/* Use this to initialize the DB */
$database = new Database("users");

/* Create the file users.json  */
$database->create();

/* Insert data in the file */
$key = "firstName";
$value = "Joao";
$database->insert($key, $value);

/* Read a Json file */
$database->read();

/* Update json */
$key = "firstName";
$updatedValue = "Joao";
$database->insert($key, $updatedValue);

/* Remove Object from json */
$key = "firstName";
$database->remove($key);

/* Delete a Json file */
$database->delete();

/* List all files */
$database->listDatabases();

```
