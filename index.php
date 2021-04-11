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
  line-height: normal;
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
div.container-edu-info {
      width: 30em;
      max-width: 100%;
      box-sizing: border-box;
      width: 100%;
      border: 0px solid black;
      padding-left: 1px;
      padding-right: 1px;
      padding-top: 0px;
      padding-bottom: 0px;
      margin-top: 4px;
      margin-bottom: 15px;
}
.edu-row1 {
       border: 0px solid #008800;
       margin: 0px
       padding: 1px;
}
.more-top-margin {
      margin-top: 8px;
}
.more-bottom-margin {
    margin-bottom: 30px;
}
#main {
    position: absolute;
    left: 30%;
    right: 30%;
    border: 0px orange solid;
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
    echo
    '<div class="more-top-margin absolute-left" id="top_left">

      <img src="IMG_20151115_150532519PathinPark.jpg">
      <p class="more-top-margin">The path ahead leads into the trees,</p>
      <p>And I wished I could follow it</p>
      <p>long I looked until I dared.</p>
      <br />
      <div>
      <p class="more-top-margin justify">This is an example of a simple website and database
            that is suitable for a relatively small organization or business
            with less than 50,000 members, but is it modeled for future growth
            into a large organization.
      </p>
      </div>
    </div>
   <div id="top_right" class="more-top-margin absolute-right">
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
    echo('<p class="center more-line-height-3x">');
        echo '<a class="anchor-button more-right-margin" href="add.php">Make New Profile</a> <a' ;
        echo ' class="anchor-button" href="logout.php">Logout</a>' ;
    echo '</p>';
    //$ips = isPassWordSet($pdo);
    $mysqlfield = 'password_time';
    $ips = isPasswordSet($_SESSION['email'],$mysqlfield,$pdo);
    flashMessages();
    $sqlUsrCount = "SELECT COUNT(*) FROM Profile WHERE user_id = :sid";
    $stmtUsrCount = $pdo->prepare($sqlUsrCount);
    $stmtUsrCount->execute(array(':sid' => $_SESSION['user_id']));
    $rowCnt = $stmtUsrCount->fetch(PDO::FETCH_ASSOC);
    $tableRows = array_values($rowCnt)[0];
    // echo '<pre><p>User logged-in. There are ';
    // var_dump($tableRows);
    // echo ' profiles.</p></pre>';
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
    // If logged-in
    // $sqlAll = "SELECT profile_id,
    //  user_id, first_name, last_name, email,
    //                     profession resume_type FROM Profile ORDER BY last_name";

    $sqlAll = "SELECT profile_id, user_id, first_name, last_name, email,
                    profession, resume_style FROM Profile ORDER BY last_name";
    $stmtAll = $pdo->query($sqlAll);

    $sqlUsr = "SELECT profile_id, user_id, first_name, last_name, email,
                       profession, resume_style FROM Profile
                        WHERE user_id = :sid ORDER BY last_name";

    //$stmtUsr = $pdo->query($sqlUsr);
    // fetch resume type
    // logged-in: Provide edit and delete options
    // Show table with column title row only
    echo('<table class="double-space" border=2>');
    echo '<tr><th>';
    echo('Name');
    echo('</th><th>');
    echo('Profession');
    echo('</th><th>');
    echo('Action</th>');
    echo('</tr>');
    if ( isset($_SESSION['user_id']) && strlen($_SESSION['user_id']) > 0 ) {
      $stmtUsr = $pdo->prepare($sqlUsr);
      $stmtUsr->execute(array(':sid' => $_SESSION['user_id']));
      //$row = $stmtUsr->fetch(PDO::FETCH_ASSOC);
      while ( $row = $stmtUsr->fetch(PDO::FETCH_ASSOC) ) {
          $resume_style = $row['resume_style'];
          if($resume_style == 'student') {
              $view = 'resume.';
          } else if ($resume_style == 'employed') {
            $view = 'profile.';
          } else  {
              $view = 'portfolio.';
          }
          echo '<tr><td><p class="zero-bottom-margin more-line-height-1p5em">'
            .htmlentities($row['first_name']).' '
            .htmlentities($row['last_name'])
            .'</p></td><td><p class="zero-bottom-margin more-line-height-1p5em">'
            .htmlentities($row['profession'])
            .'</p></td><td><p class="zero-bottom-margin more-line-height-1p5em">
               <a href="'.$view.'php?profile_id='.$row['profile_id'].'">Resume</a>&nbsp
               <a href="edit.php?profile_id='.$row['profile_id'].'">Edit</a>&nbsp
               <a href="delete.php?profile_id='.$row['profile_id'].'">Delete</a>
               </p>
            </td></tr>';
          } // end while loop
        } else {
       // not logged in, guest profile will be hidden
           // Hide profile names for guest;
           // $row['user_id'] is a field name in the database,
           // which is not the same as $_SESSION['user_id']
           while ( $rowAll = $stmtAll->fetch(PDO::FETCH_ASSOC) ) {
             $resume_style = $rowAll['resume_style'];
             if($resume_style == 'student') {
                 $view = 'resume.';
             } else if ($resume_style == 'employed') {
               $view = 'profile.';
             } else  {
                 $view = 'portfolio.';
             }
             echo '<tr><td>'
                   .htmlentities($rowAll['first_name']).' '
                   .htmlentities($rowAll['last_name'])
                   .'</td><td>'
                   .htmlentities($rowAll['profession'])
                   .'</td><td>
                       <a href="'.$view.'php?profile_id='
                         .$rowAll['profile_id'].'">Details</a>
                 </td></tr>';
            } // end of loop over all profiles
        } // end of logged-in or not logged-in
        echo("</table>");
        //echo 'There are '.$tableRows.' profiles in the database.';
        //print_r('There are '.$tableRows.' profiles in the database.');
} else {
    echo ('<p class="center">No profiles yet.</p>');
} // end of conditional block for all rows
if ( isset($_SESSION['user_id']) && strlen($_SESSION['user_id']) > 0 ) {
    echo '<p class="more-top-margin-2x center"> You can change your password '
             .'<a href="changePassword.php">here</a>.'
          .'</p>';
}
//<a href="http://localhost/index.php"> main page</a>.//
echo '<p class="justify left">Take me to the
          <a href="http://marcel-merchat.com/index.php"> main page</a>.
      </p>';
?>
</div> <!-- end main -->
<script>
$(document).ready(function() {
  window.console && console.log('Document ready called ');
  isMobileDevice = Boolean("<?php echo isMobile() ?>" == 1);
  isLargeDevice = !isMobileDevice;
  window.console && console.log('Mobile device = ' + isMobileDevice);
  var w = $( window ).width();
  window.console && console.log('The window width is = ' + w);
  adjustWindow();
  //var tagIdcss = $('#main').attr("id");
  // $( "#getmain" ).click(function() {
  // showWidth( "main div", $( "#main" ).width() );
  // });
  // $("#getw").click(function() {
  // showWidth( "window", $( window ).width() );
  // });
  // $( "#getd" ).click(function() {
  // showWidth( "document", $( document ).width() );
  // });
});
</script>
</body>
</html>
