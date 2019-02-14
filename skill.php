<?php
require_once "pdo.php";
session_start();
header('Content-Type: application/json; charset=utf-8');

$t = $_GET['term'];

error_log('Looking for type-ahead term '.$t);

$stmt = $pdo->prepare('SELECT name FROM Skill
                      WHERE name LIKE :prefix');
$stmt->execute(array( ':prefix' => $t.'%'));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$retval[] = array();
while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    $retval[] = $row['name'];
}

//echo(json_encode($retval, JSON_PRETTY_PRINT));
echo(json_encode($retval));
