<?php
require_once "pdo.php";
require_once "util.php";
session_start();
sleep(2);header('Content-Type: application/json; charset=utf-8');

// Online:
// 1. click in the text entry box
// 2. insert a character or more
// 3. click to sent GET 200 JSON
//    The entries that match the JSON are stored in the browser
// 4. type in another letter to sent GET 304 to see matching text selections

//$cars = array("Volvo", "BMW", "Toyota","one");
$t = $_GET['term'];
//$var = '%'.$t.'%';
//error_log('Looking for type-ahead term '.$t);
unset($_SESSION['error']);
$detect = $t;
$detect = 'good';
if(ofnsvCheck($pdo, $t)){
    $detect = 'bad';
}
//$detect = $t.' '.ofnsvJSON($pdo, $t);
//$detect = 'bad';
//if(isset($_SESSION['error']) && $_SESSION['error'] == "offensive"){
    //$_SESSION['error'] = $_SESSION['message'].' Word not recognized, please try again.';
    //header("Location: add.php");
    //return;
//}
$detectArray = array($detect);
$stuff = array('first' => $detect, 'second' => 'second thing');
//echo(json_encode($detectArray));
echo(json_encode($stuff));
