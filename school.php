<?php
require_once "pdo.php";
require_once "util.php";
session_start();
header('Content-Type: application/json; charset=utf-8');

// Online:
// 1. click in the text entry box
// 2. insert a character or more
// 3. click to sent GET 200 JSON
//    The entries that match the JSON are stored in the browser
// 4. type in another letter to sent GET 304 to see matching text selections

$t = $_GET['term'];
$var = '%'.$t.'%';
//print_r($var);
$stmt2 = $pdo->prepare('SELECT name FROM Institution
                      WHERE name LIKE :patt');
$stmt2->execute(array( ':patt' => $var));
$wordList = array();
while($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)){
    $wordList[] = $row2['name'];
    //var_dump($row2['name']);
}
//var_dump($wordList);
echo(json_encode($wordList));
