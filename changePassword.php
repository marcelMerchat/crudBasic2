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
if ( isset($_POST['pass'])) {
  if ( strlen($_POST['pass']) > 5) {
        $sql = "SELECT user_id, email, random, block FROM users WHERE email = :em";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array( ':em' => $_SESSION['email']));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row === false) {
            $_SESSION['error'] = 'Incorrect password: The e-mail was not found: Please try again.';
            error_log('Login failure: '.$_SESSION['email'].' is not in database. Please check spelling');
            header( 'Location: login.php' );
            return;
        }
        if($row['block'] == 1) {
            $_SESSION['error'] = 'Account locked. Contact administrator regarding this login.';
            unset($_SESSION['user_id']);
            error_log('Login blocked: '.$_SESSION['email']);
            header( 'Location: login.php' );
            return;
        }
        $salt = $_SESSION['random'];
        //$salt = 'XyZzy12*_';
        $hashed_pass = hash('md5', $salt.$_POST['pass']);
        $u = (int) $_SESSION['user_id'];
    //  $_SESSION['email'] was set at previous forgotpass.php page;
        unset($_SESSION['error']);
        $_SESSION['emailSubject'] = 'Password';
        $_SESSION['emailMessage'] = '<html><body style="'
            .'font-size:1.2em; color:#886600;background-color: #FDF0D0;'
            .'border: 2px solid #886600;padding: 20px;">'
        .'<p style="font-size:1.3em;color:#008800;margin-bottom:30px">'
        .'Attention: '.$userName
        .'</p><p>'
        .'The password at www.marcel-merchat.com for email address '
           .$_SESSION['email']
           .' was changed. If you did not did not authorize this, please '
           .'email the database administrator at merchatDataTools@gmail.com or '
           .'call 773-852-1689.</p></body></html>';
        error_log('Password change was attemped for User-'.$u);
        require 'gmailer.php';
        if ($_SESSION['success'] === false) {
             $_SESSION['emailMessage'] = 'The email address was rejected. You may try again.';
             error_log('Password change was rejected for User-'.$u);
             header( 'Location: forgotpass.php' ) ;
             return;
        }
    //  Change database
        $sql = "UPDATE users SET password = ? WHERE user_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array($hashed_pass,$u));
        error_log('Password change was made for User-'.$u);

    //  Retrieve information from database while protecting users from
    //  scripts or html code such as redirection to another website
        $sql = 'SELECT name, email FROM `users` WHERE user_id = ?';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array($u));
        $row =  $stmt->fetch(PDO::FETCH_ASSOC);
        $mymail = htmlentities($row['email']);
        $_SESSION['success'] = 'The password has been changed. A confirmation has been forwarded to your '.$mymail.' address.';
        $_SESSION['emailSubject'] = 'Password Change at www.marcel-merchat.com';
        $_SESSION['emailMessage'] = 'Password changed for '.$mymail.'\.';
        $_SESSION['userName'] = "Administrator";
        //require 'gmailer.php';
        $_SESSION['userName'] = "";
        header('Location: login.php');
        return;
    } else {
		    $_SESSION['error'] = 'Incorrect password, please try again.';

    }
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
<div class="content center" id="main">
<br />
<form method="POST">
    <h1 class="center">Get New Password</h1>
<?php
  flashMessages();
?>
  <br/>
  <div>
    <p class="center less-bottom-margin"><label for="id_1723">Password</label></p>
    <p class="center less-bottom-margin less-top-margin"><input class="text-box"  type="password" name="pass" id="id_1723"></p>
  </div>
  <br/>
      <input class="button-submit" type="submit" onclick="return doValidatePass();" value="Assign Password">
      <input class="button-submit" type="submit" name="cancel" value="Cancel">
</form>
</div>
<script>
function doValidatePass() {
    console.log('Validating...');
    try {
        pss = document.getElementById('id_1723').value;
        console.log("Validating pss="+pss);
        if (pss == null || pss == "") {
            alert("password must be filled in.");
            return false;
        }
        if (pss.length < 8) {
            alert("Password must have length of at least 8 characters.");
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
