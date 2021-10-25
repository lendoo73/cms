<?php

require "setEnv.php";
require "pdoDatabase.php";

define( "SERVERNAME", getenv("SERVERNAME") );
define( "USERNAME", getenv("USERNAME") );
define( "PASSWORD", getenv("PASSWORD") );
define( "DBNAME", getenv("DBNAME") );

// ------------------------------ connect to the database: ------------------------------
$pdo = new UsePDO(SERVERNAME, USERNAME, PASSWORD, DBNAME);

?>