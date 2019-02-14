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

//$cars = array("Volvo", "BMW", "Toyota","one");
// Did we find any existing schools
//if(count($wordList) < 10) {
//if($resultCnt > 0) {
  //$wordList[] = 'School match exists in above array from database');
   //array_push($wordList,"blue","yellow");
   // $wordList[0] = 'addme'.count($wordList);
  // echo(json_encode($cars));

//} else {
     //$stmt = $pdo->prepare("SELECT COUNT(*) FROM Dictionary WHERE Word = ?");
     //$stmt->execute($t);
     //$wordList[] = 'zz';
     //$wordList[] = 'zzz';
  //   $cnt = 1;
     //$cnt = $stmt->fetch(PDO::FETCH_ASSOC);
     //if($cnt > 0) {
       // Not in school list but words in dictionary
          //$goode[0] = 'yy';
          //echo(json_encode($good));
     //} else {
// //      // Check for offensive nature
          //$goode[0] = 'not in dict';
          //echo(json_encode($good));
//         $stmt = $pdo->prepare('SELECT COUNT(*) FROM Offensive WHERE ? LIKE CONCAT("%", word, "%")');
//         SELECT COUNT(*) FROM Offensive WHERE $t LIKE CONCAT ("%", word, "%");
//         $stmt->execute([$t]);
//         $cnt2 = $stmt->fetch(PDO::FETCH_ASSOC)
//         if(!$cnt2 && $cnt2 > 0) {
//         // There is a possible offensive language match
//            $offense = array();
//            $offense[] = ' language check: ';
//            echo(json_encode($offense));
//         }
     //}
     //echo(json_encode($good));
    // echo(json_encode($cars));
//}

$t = $_GET['term'];
$var = '%'.$t.'%';
//$var = $t;

//error_log('Looking for type-ahead term '.$t);
//$stmt = $pdo->prepare('SELECT name FROM Institution
  //                    WHERE name LIKE :prefix');
// University of Michigan Web Development Method
//$stmt->execute(array( ':prefix' => $var));
//$row = $stmt->fetch(PDO::FETCH_ASSOC);
//$retval[] = array();
//while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
//    $retval[] = $row['name'];
//}

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
$resultCnt = 0;
//foreach($stmtCount->fetchAll(PDO::FETCH_ASSOC) as $r){
//$row1 = $stmtCount->fetch(PDO::FETCH_ASSOC);
//$resultCnt = count($row1);
$i = 0;
$hit = 'junk';
//foreach ($;stmtCount->fetchAll() as $row1) {
//while($row1 = $stmtCount->fetch(PDO::FETCH_ASSOC)){
  //$wd = array_values($row1)[1];
  //$hit = $row1[1];
  //if(strlen($wd) > 0) {
  //if(!$row1) {
      //$resultCnt = $resultCnt + 1;
      //$resultCnt = 1;
      //$hit = $wd;
  //}

  //$resultCnt = $resultCnt + 1;
//}
$resultCnt = 0;
$hit = 'junk';
//$sql = "SELECT count(*) FROM `table` WHERE foo = bar";
//$result = $con->prepare($sql);
//$result->execute();
//$number_of_rows = $result->fetchColumn();

$cnt2 = 0;

$stmt2 = $pdo->prepare('SELECT name FROM Institution
                      WHERE name LIKE :patt');
$stmt2->execute(array( ':patt' => $var));
// $row = $stmt->fetch(PDO::FETCH_COLUMN);
$wordList = array();
$detect = false;

$cnter = 0;
while ($cnter < 1 ) {
  //$stmt = $pdo->prepare('SELECT COUNT(*) FROM Offensive WHERE ? LIKE CONCAT("%", word, "%")');
  //$stmt->execute(array($t));
  //$rowdata = $stmt->fetch(PDO::FETCH_ASSOC);
  //$cnt2 = array_values($rowdata)[0];
  //$detect3 = detectOffensive($t);
  //unset($_SESSION['error']);
  //$detect2 = filterWord($pdo, $t);
  //$detect = true;
  //if($_SESSION['error'] === 'offensive') {
  //if($cnt2 > 0) {
      //$detect = true;
      //$wordList[] = $t.' language trigger';
      //unset($_SESSION['error']);
  //}
  $cnter = $cnter + 1;
}

while($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)){
    //$data[] = array($catagory => $question);
    //$data[] = array($row['name']);
    //$data += [$category => $question];

    if(!$detect) {
     // There is a possible offensive language match
        $wordList[] = $row2['name'];
        //array_push($wordList, $row['name']);
        //$wordList[] = 'i'.$resultCnt;
        $resultCnt = $resultCnt + 1;
    }
}
// $stmt = $pdo->prepare('SELECT name FROM Institution
//                       WHERE name LIKE :prefix');
// $stmt->execute(array( ':prefix' => $t.'%'));
// $row = $stmt->fetch(PDO::FETCH_ASSOC);
// $retval[] = array();
// while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
//     $retval[] = $row['name'];
// }
echo(json_encode($wordList));
