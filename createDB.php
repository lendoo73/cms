<?php

require "setEnv.php";
require "pdoDatabase.php";

define( "SERVERNAME", getenv("SERVERNAME") );
define( "USERNAME", getenv("USERNAME") );
define( "PASSWORD", getenv("PASSWORD") );


// ------------------------------ connect to SQL: ------------------------------
$pdo = new UsePDO(SERVERNAME, USERNAME, PASSWORD);

// create cms database
$pdo->createDatabase("cms");
var_dump($pdo);

// ------------------------------ create articles table: ------------------------------
$sql = "
DROP TABLE IF EXISTS articles;
CREATE TABLE articles
(
  id              smallint unsigned NOT NULL auto_increment,
  publicationDate date NOT NULL,                              # When the article was published
  title           varchar(255) NOT NULL,                      # Full title of the article
  summary         text NOT NULL,                              # A short summary of the article
  content         mediumtext NOT NULL,                        # The HTML content of the article

  PRIMARY KEY     (id)
);
";

$pdo->createTable($sql);

?>