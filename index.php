<?php
require_once "pdo.php";
require_once "util.php";
session_start();
?>
<!--  VIEW or HTML code for model-view-controller  -->
<!DOCTYPE HTML>
<html>
<head>
  <title>Resume Registry</title>
  <!--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css"> -->
<?php
  require_once 'header.php';
?>
<style nonce="3c071056f3ed4d9ea0ec26f5a2fad84b" type="text/css">
.adg-similar {
    background-color: yellow !important;
}
body {
  font-family: arial, sans-serif;
}
nav {
    background-color: lightgray;
    height : 3em;
    font-family: arial, sans-serif;
}
nav ul {
    list-style-type: none;
    padding-left: 0;
    vertical-align: middle;
}
nav li {
  display: inline-block;
  vertical-align: middle;
}
nav li a {
  color: darkblue;
  text-decoration: none;
}
.back {
    position: absolute;
    left: 20px;
}
.forward {
    position: absolute;
    right: 20px;
}
</style>
<meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<!--  VIEW or HTML code for model-view-controller  -->
<body>
<div class="adgurd-alert adguard-assistant-button-fixed adguard-assistant-button-bottom adguard-assistant-button-right"><div class="adgurd-alert-wrap"><div class="adgurd-alert-logo-big"></div>
<div class="adgurd-alert-cont" style="opacity: 0; display: none;"><div class="adgurd-alert-tail"></div><div class="adgurd-alert-head"></div><div class="adgurd-alert-text"></div>
<div class="adgurd-alert-more"></div></div></div></div>
<?php
if(! isMobile()) {
    echo '<div id="top_left">
      <div>
      <img src="IMG_20151115_150532519PathinPark.jpg">
      <p class="more-top-margin">The path ahead leads into the trees,</p>
      <p>And I wished I could follow it</p>
      <p>long I looked until I dared.</p>
      </div>
      <br />
      <div>
      <p class="more-top-margin">This is an example of a simple website and database
            that is suitable for a relatively small organization or business
            with less than 50,000 members, but is it modeled for future growth
            into a large organization.
      </p>
      </div>
   </div>
   <div id="top_right" class="more-top-margin">
      <img src="IMG_20180919_124000517FlowersinMeadow.jpg">
      <p class="more-top-margin">The beginning usually doesn\'t go well</p>
      <p>and things need time to develop,</p>
      <p>the beginning is still remembered</p>
      <p class = "set-in1">until it fades away.</p>
      <p class="center"></p>
    </div>';
}
else {

}

?>

<div class="center" id="main">
<h4 class="center">Job Candidates</h4>
<?php
// logged-in case
$tableRows = 0;
if ( isset($_SESSION['user_id']) && (strlen($_SESSION['user_id']) > 0) ) {
    echo '<p class="center">Profiles by '.$_SESSION['email'].'</p>';
    echo('<p class="center">');
        echo '<a class="anchor-button" href="add.php">Make New Profile</a> <a' ;
        echo ' class="anchor-button" href="logout.php">Logout</a>' ;
    echo '</p>';
    //$ips = isPassWordSet($pdo);
    $mysqlfield = 'password_time';
    $ips = isPasswordSet($_SESSION['email'],$mysqlfield,$pdo);
    // echo $ips;
    // if($ips=="SetTimeout"){
    //    echo '<p class="center message">Password must be <a href="changePassword.php">reset</a> before the 30 minute timeout.</p>';
    // } else if ($ips=="PastTimeout"){
    //    echo '<p class="center message">Temporary password expired after 30-minutes.'
    //           .'Go to the <a href="changePassword.php">replace password</a> page to obtain a new password.</p>';
    // }
    flashMessages();
    $sqlUsrCount = "SELECT COUNT(*) FROM Profile WHERE user_id = :sid";
    $stmtUsrCount = $pdo->prepare($sqlUsrCount);
    $stmtUsrCount->execute(array(':sid' => $_SESSION['user_id']));
    $rowCnt = $stmtUsrCount->fetch(PDO::FETCH_ASSOC);
    //$row =  $stmt1->fetch(PDO::FETCH_ASSOC);
    //echo('User record count is '.array_values($rowCnt)[0]);
    $tableRows = array_values($rowCnt)[0];
    //echo '<pre><p>User logged-in. There are ';
    //var_dump($tableRows);
    //echo ' profiles.</p></pre>';
    //echo 'var_dump is '; // type and value
    //echo htmlspecialchars($row_count).' &lt'. ' &gt';
} else {
    echo('<h4 class="double-space center">
        <a href="login.php">Login</a>
       </h4>
       <div>
           <p class="small">
              Using the login link, you can log in as \'guest@mycompany.com\'
              with the password \'guest123\'. There is a link to get your own
              password too.
           </p>
        </div>');
    $stmtCountRows = $pdo->query("SELECT COUNT(*) FROM Profile");
    $cnt =  $stmtCountRows->fetch(PDO::FETCH_ASSOC);
    $tableRows = array_values($cnt)[0];
}
if($tableRows > 0) {
    // Show table with column title row only
    echo('<table class="double-space" border=2>');
    echo '<tr><th>';
    echo('Name');
    echo('</th><th>');
    echo('Profession');
    echo('</th><th>');
    echo('Action</th>');
    echo('</tr>');
    // If logged-in
    $sqlAll = "SELECT profile_id, user_id, first_name, last_name, email,
                        profession FROM Profile ORDER BY last_name";
    $stmtAll = $pdo->query($sqlAll);
    if ( isset($_SESSION['user_id']) && strlen($_SESSION['user_id']) > 0 ) {
    // logged-in: Provide edit and delete options
       $sqlUsr = "SELECT profile_id, user_id, first_name, last_name, email,
                          profession FROM Profile WHERE user_id = :sid";
       $stmtUsr = $pdo->prepare($sqlUsr);
       $stmtUsr->execute(array(':sid' => $_SESSION['user_id']));
       while ( $row = $stmtUsr->fetch(PDO::FETCH_ASSOC) ) {
          echo '<tr><td>'
            .htmlentities($row['first_name']).' '
            .htmlentities($row['last_name'])
            .'</td><td>'
            .htmlentities($row['profession'])
            .'</td><td>
               <a href="view.php?profile_id='.$row['profile_id'].'">Details</a>&nbsp
               <a href="edit.php?profile_id='.$row['profile_id'].'">Edit</a>&nbsp
               <a href="delete.php?profile_id='.$row['profile_id'].'">Delete</a>
            </td></tr>';
        }
    } else {
       // not logged in, guest profile will be hidden
      $sqlUsr = "SELECT profile_id, user_id, first_name, last_name, email,
                        profession FROM Profile ORDER BY last_name";
      $stmtUsr = $pdo->query($sqlUsr);
        while ( $row = $stmtUsr->fetch(PDO::FETCH_ASSOC) ) {
           // Hide profile names for guest;
           // $row['user_id'] is a field name in the database,
           // which is not the same as $_SESSION['user_id']
           if ( $row['user_id'] != 2 ) {
              echo
                '<tr><td>'
                   .htmlentities($row['first_name']).' '
                   .htmlentities($row['last_name'])
                   .'</td><td>'
                   .htmlentities($row['profession'])
                   .'</td><td>
                       <a href="view.php?profile_id='
                         .$row['profile_id'].'">Details</a>
                 </td></tr>';
            }
        } // end while loop
     }
     echo("</table>");
     //echo 'There are '.$tableRows.' profiles in the database.';
     //print_r('There are '.$tableRows.' profiles in the database.');
} else {
    echo ('<p class="center">No profiles yet.</p>');
}
if ( isset($_SESSION['user_id']) && strlen($_SESSION['user_id']) > 0 ) {
    echo '<p class="center"> You can change your password '
             .'<a href="changePassword.php">here</a>.'
          .'</p>';
}
?>
</div> <!-- end main -->
</body>
</html>
