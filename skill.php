<?php
require_once "pdo.php";
session_start();
header('Content-Type: application/json; charset=utf-8');

$t = $_GET['term'];
$var = '%'.$t.'%';

error_log('Looking for type-ahead term '.$t);

$stmt = $pdo->prepare('SELECT name FROM Skill
                      WHERE name LIKE :patt');
$stmt->execute(array( ':patt' => $var));

$row = $stmt->fetch(PDO::FETCH_ASSOC);
$wordList = array();
while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    $wordList[] = $row['name'];
    //var_dump($row2['name']);
}

echo(json_encode($wordList, JSON_PRETTY_PRINT));
