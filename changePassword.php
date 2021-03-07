<?php
session_start();
require 'timeout.php';
require_once 'pdo.php';
require_once "util.php";

if ( ! isset($_SESSION['user_id']))  {
      die('ACCESS DENIED');
}

if ( isset($_POST['cancel'] ) ) {
    header('Location: index.php');
    return;
}
$_SESSION['success'] = false;
//  $_SESSION['email'] was set at login
$email = trim($_SESSION['email']);
if ( isset($_POST['pass'])) {
  //print_r('Past first if '.$_POST['pass']);
  if ( strlen($_POST['pass']) > 7) {
     // If user Name and password fields have entries:
        if ($email == 'guest@mycompany.com') {
          $_SESSION['error'] = 'The guest password cannot be changed. ';
          header( 'Location: login.php' );
          return;
        }
        $sql = "SELECT Count(*) FROM users WHERE email = :em";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array( ':em' => $email));
        $CountArray = $stmt->fetch(PDO::FETCH_ASSOC);
        $myCount = array_values($CountArray)[0];
    //  $myCount equals 1 if email was found
        if ($myCount == 0) {
    //    if($row === false) {
            $_SESSION['error'] = 'That e-mail was not found: Please try again. ';
            error_log('Login failure: '.$_SESSION['email'].' is not in database.');
            header( 'Location: index.php' );
            return;
        }
    //  Record was found
        $sql = "SELECT user_id, name, email, random, block FROM users WHERE email = :em";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array( ':em' => $_SESSION['email']));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        //var_dump($row);
        if($row['block'] == 1) {
            $_SESSION['error'] = 'Account locked. Contact administrator regarding this login.';
            unset($_SESSION['user_id']);
            error_log('Login blocked: '.$_SESSION['email']);
            header( 'Location: login.php' );
            return;
        }
        //$salt = $_SESSION['random'];
        $salt = $row['random'];
        $hashed_pass = hash('md5', $salt.$_POST['pass']);
        //print_r(' The new password is '.$hashed_pass.' derived from '.$_POST['pass'].' and random '.$salt);
        $user_id = $_SESSION['user_id'];
        $userName = $_SESSION['userName'];
        unset($_SESSION['error']);
        $_SESSION['emailSubject'] = 'Password';
        //if ( isset($_POST['hint']) && strlen($_POST['hint']) > 4 ) {
        //    $hashed_hint = hash('md5', $salt.$_POST['hint']);
        $_SESSION['emailMessage'] = '<html><body style="'
            .'font-size:1.2em; color:#886600;background-color: #FDF0D0;'
            .'border: 2px solid #886600;padding: 20px;">'
        .'<p style="font-size:1.3em;color:#008800;margin-bottom:30px">'
        .'Attention: '.$userName
        .'</p><p>'
        .'The password at www.marcel-merchat.com for email address '
           .$_SESSION['email']
           .' was changed. If you did not did not authorize this, please '
           .'email the database administrator at merchatDataTools@gmail.com.'
           .'</p></body></html>';
        error_log('Password change was made for User-'.$user_id);
        $mysqlfield = 'password_time';
        $totalsecs = getElapsedSeconds($email,$mysqlfield,$pdo);
        if($totalsecs > 60000){
            require 'gmailer.php';
        } else {
            $_SESSION['success'] = 'The password has been changed '
                                  .'successfully.';
        }
        if ($_SESSION['success'] === false) {
             $_SESSION['error'] = 'Something went wrong. '
                                .' Maybe trying logging in again or '
                                .'email the database administrator at '
                                .'merchatDataTools@gmail.com.';
             error_log('Password change was rejected for User-'.$user_id);
             header( 'Location: changePassword.php' ) ;
             return;
        }
    //  Change database
        $sql = "UPDATE users SET password = ? WHERE user_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array($hashed_pass,$user_id));
    // Unlock timeout.
        $sql = "UPDATE users set timeout = :tout WHERE email = :em";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(':tout' => 0, ':em' => $email));
        error_log('Password change was made for User-'.$user_id);
// WFgmKSJi WFgmKSJi 18LmHMAt
    //  Retrieve information from database while protecting users from
    //  scripts or html code such as redirection to another website
        //$sql = 'SELECT name, email FROM `users` WHERE user_id = ?';
        //$stmt = $pdo->prepare($sql);
        //$stmt->execute(array($user_id));
        //$row =  $stmt->fetch(PDO::FETCH_ASSOC);
        //$_SESSION['userName'] = row['name'];
        //$mymail = $row['email'];
        //$_SESSION['email'] = $mymail;
        //$_SESSION['success'] = 'The password has been changed. A confirmation'
        //  .' has been forwarded to your '.$email.' address.';
        //$_SESSION['success'] = 'The password has been changed. A confirmation has been forwarded to your '
        //           .$email.' address.'.$hashed_pass.' The new password is derived from '.$_POST["pass"].' and random '.$salt;
        //$_SESSION['emailSubject'] = 'Password Change at www.marcel-merchat.com';
        //$_SESSION['emailMessage'] = 'Password changed for '.$email.'.';
        header('Location: index.php');
        return;
    } else {
		    $_SESSION['error'] = 'Please check that password; '
        .' it should be at least eight characters long. Please try again.';
    } // long enough password
}
?>

<!--  VIEW or HTML code for model-view-controller  -->

<!DOCTYPE html>
<html>
<head>
<?php
    require_once 'header.php';

?>
<title>Change Password</title>
</head>
<body>
<div class="center" id="main">

<br />
<form method="POST">
    <h1 class="center">Get New Password</h1>
<?php
  flashMessages();
?>
  <br/>
  <!--<div><p class="center big">
      <label for="id_1723">Password</label>
      <input class="password"  type="password" name="pass" id="id_1723">
  </p>
</div> -->
  <div>
    <p class="center less-bottom-margin"><label for="id_1723">Password</label></p>
    <p class="center less-bottom-margin less-top-margin"><input class="text-box"  type="password" name="pass" id="id_pass"/></p>
  </div>
  <!-- <div>
    <p class="center less-bottom-margin"><label for="id_1723h">Hint</label></p>
    <p class="center less-bottom-margin less-top-margin"><input class="text-box"  type="password" name="hint" id="id_1723h"></p>
  </div> -->
  <br/>
      <input class="button-submit wide-15char" type="submit" onclick="return doValidatePass();" value="Assign Password"/>
      <input class="button-submit wide-10char" type="submit" name="cancel" value="Cancel"/>
</form>
  <p class="left"> Please enter a password at least 8 characters in length.
  </p>
</div>
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
function doValidatePass() {
    console.log('Validating...');
    //pss = document.getElementById('id_1723').value;
    var p = document.getElementById('id_pass');
    var textinfo = p.value;
    console.log("Validating pss="+p);
         //window.console && console.log('At JSON Dictionary. Tag id is ' + elementid);
         //var textinfo = document.getElementById(elementid).value;
         //window.console && console.log('info is '+textinfo);
    var len = textinfo.length;
    try {
        if (textinfo == null || textinfo == "") {
           alert("password must be filled in.");
           return false;
        }
        if (textinfo.length < 8) {
           alert("Password must have length of at least 8 characters.");
           return false;
        }
    } catch(e) {
           alert("Catch error. "+textinfo+" with length "+len);
           return false;
    }
      return true;
  }
</script>
</body>
</html>
