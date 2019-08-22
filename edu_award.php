<?php
require_once "pdo.php";
session_start();
header('Content-Type: application/json; charset=utf-8');

$t = $_GET['term'];
//$_SESSION['message'] = ' Received the term '.$t;
//error_log('Looking for type-ahead term '.$t);

$var = '%'.$t.'%';
$stmt2 = $pdo->prepare('SELECT name FROM Award
                      WHERE name LIKE :patt');
$stmt2->execute(array( ':patt' => $var));
$wordList = array();
while($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)){
    //print_r($row2);
    $wordList[] = $row2['name'];
}
//print_r($wordList);
echo(json_encode($wordList));
