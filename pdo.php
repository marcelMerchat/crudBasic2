<?php
try{

//$pdo = new PDO('mysql:host=localhost;port=8889;dbname=team',
  //   'umsi', 'php123');
// See the "errors" folder for details...
//$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


//$pdo = new PDO('mysql: host=159.89.183.50;port=3306;dbname=team',
//$pdo = new PDO('mysql:host=localhost;port=8889;dbname=team',
   //'umsi', 'php123');
$pdo = new PDO('mysql:host=localhost;port=8889;dbname=team',
      'gramps77', 'mcp2tWc');

//See the "errors" folder for details...
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); }

catch (PDOException $e){
     echo 'ERROR: '. $e->getMessage();

};
