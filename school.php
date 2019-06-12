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
// Alternate Method
//error_log('Looking for type-ahead term '.$t);
//$stmt = $db->query('SELECT * FROM table');
//$row_count = $stmt->rowCount();
//$stmtCount = $pdo->prepare('SELECT * FROM Institution
  //                  WHERE `name` LIKE :patt');
//$stmtCount->bindValue(1, $var);
//$stmtCount ->execute();
//$stmtCount->execute(array(':patt' => $var));
//$stmtCnt = $stmtCount->fetch(PDO::FETCH_ASSOC);
//$resultCnt = $stmtCount->rowCount();
$i = 0;
$resultCnt = 0;
//$hit = 'junk';
//$cnt2 = 0;
$stmt2 = $pdo->prepare('SELECT name FROM Institution
                      WHERE name LIKE :patt');
$stmt2->execute(array( ':patt' => $var));
$wordList = array();
//$detect = false;
//$cnter = 0;
//while ($cnter < 1 ) {
  //$cnter = $cnter + 1;
//}
while($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)){
    //if(!$detect) {
     // There is a possible offensive language match
        $wordList[] = $row2['name'];
        //$resultCnt = $resultCnt + 1;
    //}
}
echo(json_encode($wordList));
