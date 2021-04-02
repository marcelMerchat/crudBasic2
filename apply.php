<?php
session_start();
require 'timeout.php';
require_once 'pdo.php';
require_once "util.php";
if ( isset($_POST['cancel'] ) ) {
    header('Location: index.php');
    return;
}
$_SESSION['success'] = false;
$myname = "";
$mymail = "";
// 'if' statement fails for GET requests; there is no POST data.
if( (
       (isset($_POST['name'])  && (strlen($_POST['name']) > 0  ) )
                               ||
       (isset($_POST['email']) && (strlen($_POST['email']) > 0 ) )
     )
) {
  $myname = trim(htmlentities($_POST['name']));
  $mymail = trim(htmlentities($_POST['email']));
  if ( strlen($_POST['name']) >= 1 && strlen($_POST['email']) >= 1 ) {
     unset($_SESSION['name']);  // Logout current user
     unset($_SESSION['user_id']);
     $numErrors = 0;
     $_SESSION['error'] = "";

  // validate name
     $userName = trim($_POST['name']);
     $validName = validateName($userName,$pdo);
     if(!$validName) {
        $_SESSION['error'] = $_SESSION['error'].' Please check name entry. ';
        $numErrors = $numErrors + 1;
     }
  // Validate email
     $email = trim($_POST['email']);
     if (strpos($_POST['email'], '@') === FALSE ) {
        $_SESSION['error'] = $_SESSION['error'].' Invalid email address'.$_POST['email'].'. ';
        $numErrors = $numErrors + 1;
     }
     $sql = "SELECT Count(*) FROM users WHERE email = :em";
     $stmt = $pdo->prepare($sql);
     $stmt->execute(array( ':em' => $email));
     $CountArray = $stmt->fetch(PDO::FETCH_ASSOC);
     $myCount = array_values($CountArray)[0];
     if ($myCount > 0) {
          $_SESSION['error'] = $_SESSION['error']
             .' That e-mail address already exists.'
             .' Please choose a different one. ';
          $numErrors = $numErrors + 1;
     } else if ($myCount === 0) {
          $valid_email = validateEmail($pdo);
          if (!$valid_email){
              $_SESSION['error'] = $_SESSION['error']
                  .' The email address was rejected. ';
              $numErrors = $numErrors + 1;
          }
     }
 //  Error handling
      if ($numErrors > 0){
        $_SESSION['success'] = false;
        header( 'Location: apply.php' ) ;
        return;
     }
     $sql = "SELECT block FROM users WHERE email = :em";
     $stmt = $pdo->prepare($sql);
     $stmt->execute(array( ':em' => $email));
     $row = $stmt->fetch(PDO::FETCH_ASSOC);
     if($row['block'] == 1) {
          $_SESSION['error'] =
              'That account was locked. Contact administrator regarding login.';
          unset($_SESSION['user_id']);
          error_log('Login blocked: '.$_POST['email']);
          header('Location: index.php');
          return;
     }
//   Application accepted
     $_SESSION['userName'] = $userName;
     $_SESSION['email'] = $email;
     $salt = generateRandomString(10);
     $pass = generateRandomString(8);
     $hashed_pass = hash('md5', $salt.$pass);
     //Create time string
     $current_time = date("Y-m-d H:i:s");
//   Generate date object with string
//   $date = new DateTime($current_time)
//   Change database with protection from user input.
     $sql = "INSERT INTO users (name,email,password,random,password_time, block)
               VALUES (:nm, :em, :hpw, :rnd, :pwt, :blck)";
     $stmt = $pdo->prepare($sql);
     $stmt->execute(array(
         ':nm' => $_POST['name'],
         ':em' => $email,
         ':hpw' => $hashed_pass,
         ':rnd' => $salt,
         ':pwt' => $current_time,
         ':blck' => 0
     ));
     $user_id = $pdo->lastInsertId() + 0;
     $_SESSION['email'] = $email;
     $_SESSION['emailSubject'] = 'New Login Account';
     $_SESSION['emailMessage'] =
         '<html><body style="'
        .'font-size:1.2em;color:#886600;'
        .'background-color: #FDF0D0; border: solid 3px ;padding: 20px;">'
        .'<p style="font-size:1.3em;color:#008800;margin-bottom:30px">'
        .' Attention: '.$userName
        .'</p><p>'
        .'A new login was added at www.marcel-merchat.com for email address '
        .$email
        .'. You may login using the temporary password '.$pass
        .' at this <a href="http://www.marcel-merchat.com/crudBasic/login.php">'
        .'address.</a>'
        .'</p>'
        .'</body></html>';
        // .' with hint '.$_POST['hint']
      error_log('New password application for '.$email);
      require 'gmailer.php';
      if ($_SESSION['success'] === false) {
          $_SESSION['emailMessage'] =
                         'Something went wrong trying to send an email. ';
          header( 'Location: apply.php' );
          return;
      }
      $_SESSION['success'] =
            'A temporary password has been sent to your '.$mymail.' address.';
      header('Location: login.php');
      return;
    } else {
      $_SESSION['error'] = 'Name and e-mail are required.';
    } // non-zero length for email and password
} else {
    $_SESSION['success'] = 'Please enter name and e-mail and select enter.';
    $myname = '';
    $mymail = '';
}
?>
<!DOCTYPE html>
<!--  VIEW or HTML code for model-view-controller  -->
<html>
<head>
<?php
    require_once 'header.php';
?>
<title>New Account</title>
<style type="text/css">
   input {
       width: 18em;
       background-color: #eff;
       font-size: 0.85em;
   }
   input:-internal-autofill-selected {
       background-color: rgb(255, 255, 100);
       /*background-image: none !important;*/
       color: rgb(225, 150, 50) !important;
   }
</style>
</head>
<body>
<div class="center" id="main">
<form method="POST" name="apply.php">
    <h2 class="center">Get New User Login</h2>
    <p class="justify">You should receive an email containing
           a temporary password to login within a few minutes.
    </p>
<?php
  flashMessages();
?>
    <div class="big center">
      <p class="center zero-bottom-margin padding-zero">
             <label for="username">Name</label>
      </p><p class="center zero-top-margin">
             <input class="text-box" type="text" name="name"
                    value='<?= $myname ?>' id="username"/>
          </p>
    </div><div class="center-entry">
      <p class="center zero-bottom-margin"><label for="email">Email</label>
      </p><p class="center zero-top-margin">
           <input class="text-box" type="text" name="email"
                  value='<?= $mymail ?>' id="email"/></p>
    </div>
    <p class="center double-space">
            <input class="button-submit wide-15char" type="submit"
                   onclick="return validateApplication();"
                   value="Assign My Login">
            &nbsp;
            <input class="button-submit wide-10char" type="submit" name="cancel"
                   value="Cancel">
     </p>
</form>
      <p class="justify">All submitted information is added to the database. As
         this site is public and only a demonstration, do not enter any real
         identification or credit information here for yourself or anyone else
         including but not limited to phone numbers, addresses, account numbers,
          answers to typical security questions, or any similar information
          in any of the form fields, either now or later when logged-in.
      </p>
<script>
$(document).ready(function() {
  window.console && console.log('Document ready called ');
  isMobileDevice = Boolean("<?php echo isMobile() ?>" == 1);
  isLargeDevice = !isMobileDevice;
  window.console && console.log('Mobile device = ' + isMobileDevice);
  var w = $( window ).width();
  window.console && console.log('The window width is = ' + w);
  adjustWindow();
});
function doValidate() {
    console.log('Validating...');
    try {
        uName = document.getElementById('username').value;
        addr = document.getElementById('email').value;
        //console.log("Validating addr="+addr+" pw="+pw);
        if (addr == null || addr == "" || uName == null || uName == "" ) {
            alert("Name and e-mail are required.");
            return false;
        }
        if ( addr.indexOf('@') == -1 ) {
            alert("Invalid email address");
            return false;
        }
        if(!validateEmail(form1.email)){
            return false;
        }
        return true;
    } catch(e) {
        return false;
    }
    return false;
  }
</script>
</body>
</html>
