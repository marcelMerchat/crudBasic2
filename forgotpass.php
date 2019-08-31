<?php
session_start();
require 'timeout.php';
require_once 'pdo.php';
require_once "util.php";
unset($_SESSION['user_id']);
if ( isset($_POST['cancel'] ) ) {
    header('Location: index.php');
    return;
}
$_SESSION['success'] = false;

if ( isset($_POST['emale'])) {
  $mymail = trim(htmlentities($_POST['emale']));
  if ( strlen($_POST['emale']) >= 1) {
     unset($_SESSION['user_id']);
     $numErrors = 0;
     $_SESSION['error'] = "";
     if ($mymail == 'guest@mycompany.com') {
       $_SESSION['error'] = 'The guest password cannot be changed. ';
       header( 'Location: login.php' );
       return;
     }
  // Validate email
     if (strpos($_POST['emale'], '@') === FALSE ) {
        $_SESSION['error'] = $_SESSION['error'].' Invalid email address'.'. ';
        $numErrors = $numErrors + 1;
     }
     unset($_SESSION['user_id']);
     $sql = "SELECT Count(*) FROM users WHERE email = :em";
     $stmt = $pdo->prepare($sql);
     $stmt->execute(array( ':em' => $mymail));
     $CountArray = $stmt->fetch(PDO::FETCH_ASSOC);
     $myCount = array_values($CountArray)[0];
 //  $myCount equals 1 if email was found
     if ($myCount == 1) {
          $_SESSION['success'] = $_SESSION['success']
            .' Further instructions have been sent to that email address. ';
     } else if ($myCount === 0) {
          $_SESSION['error'] = $_SESSION['error']
            .' Something went wrong. Please try again. ';
          $numErrors = $numErrors + 1;
          header( 'Location: forgotpass.php' ) ;
          return;
     }
     if ($numErrors > 0){
        unset($_SESSION['success']);
        header( 'Location: forgotpass.php' ) ;
        return;
     }
     $sql = "SELECT user_id, name, block, random FROM users WHERE email = :em";
     $stmt = $pdo->prepare($sql);
     $stmt->execute(array( ':em' => $mymail));
     $row = $stmt->fetch(PDO::FETCH_ASSOC);
     if($row['block'] == 1) {
          $_SESSION['error'] = 'Something went wrong. Contact administrator.';
          unset($_SESSION['user_id']);
          error_log('Login blocked: '.$_POST['emale']);
          header('Location: index.php');
          return;
     }
     $userName = $row['name'];
//   Application accepted
     $salt = $row['random'];
     $_SESSION['random'] = $row['random'];
     $pass = generateRandomString(8);
     $hashed_pass = hash('md5', $salt.$pass);
     //print_r('The hashed password for '.$pass, ' is '.$hashed_pass);
     $current_time = date("Y-m-d H:i:s");
     $now = new DateTime();
     $now_string = $now->format('Y-m-d H:i:s');
     $sql = "SELECT user_id, name, block, random FROM users WHERE email = :em";
     $stmt = $pdo->prepare($sql);
     $stmt->execute(array( ':em' => $mymail));
     $sql = "UPDATE users SET password = :hpw, timeout = :tout WHERE email = :em ";
     $stmt = $pdo->prepare($sql);
     $stmt->execute(array(':hpw' => $hashed_pass,
                          ':tout' => 1, ':em' => $mymail ));
     error_log('Password change was made for '.$mymail.' at '.$current_time.'.');
     $_SESSION['email'] = $mymail;
     $_SESSION['emailSubject'] = 'Temporary Password';
     $_SESSION['emailMessage'] =
         '<html><body style="'
        .'font-size:1.2em;color:#886600;'
        .'background-color: #FDF0D0; border: solid 3px ;padding: 20px;">'
        .'<p style="font-size:1.3em;color:#008800;margin-bottom:30px">'
        .' Attention: '.$userName
        .'</p><p>'
        .'A new temporary password has been assigned at www.marcel-merchat.com '
        .' for email address '
        .$mymail
        .'. You may login using the temporary password '.$pass
        .' at this <a href="http://www.marcel-merchat.com/crudBasic/login.php">'
        .'address</a>. This temporary password will expire in 30 minutes.'
        .'</p>'
        .'</body></html>';
      error_log('Replacement password assigned for '.$mymail);
      require 'gmailer.php';
      if ($_SESSION['success'] === false) {
          $_SESSION['emailMessage'] = 'Problem sending e-mail. ';
          header( 'Location: forgotpass.php' );
          return;
      }
      $_SESSION['success'] = 'A temporary password has been sent to the '
          .$mymail.' address. ';
          //.$pass;
      header('Location: login.php');
      return;
    } else {
      $_SESSION['error'] = 'Email is required.';
    } // non-zero length for email
}
?>
<!DOCTYPE html>
<!--  VIEW or HTML code for model-view-controller  -->
<html>
<head>
<?php
    require_once 'header.php';
?>
<title>Replace Password</title>
</head>
<body>
<div class="center" id="main">
<?php
    flashMessages();
?>
<form method="POST">
    <h2 class="center">Replace Password</h2>
  <div id="centerlist">
    <ul class="center">
      <li class="left">Forgot Password</li>
      <li class="left">Get Temporary Password</li>
    </ul>
  </div>
    <p class="justify">If you forget your password, you can get a new
       temporary one. You need to
       login with it and reset it to a permanent one within 30-minutes.
       If a temporary password has expired, you can get a
       new temporary one here too. This applies to temporary passwords
       for new accounts too.
    </p>
    <p class="justify">You should receive an email containing a temporary
       password within a few minutes. Reset the password when you log-in
       as the temporary password will only be valid for approximately
       30 minutes.
    </p>
    <div class="center-entry">
      <p class="center less-bottom-margin"><label for="email">Email</label></p>
      <p class="center less-bottom-margin less-top-margin"><input class="text-box" type="text" name="emale" id="email"></p>
    </div>
    <p class="center big double-space">
            <input class="button-submit" type="submit" onclick="return true;" value="Assign new password">
            <input class="button-submit" type="submit" name="cancel" value="Cancel">
    </p>
</form>

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
        addr = document.getElementById('email').value;
        if (addr == null || addr == "" ) {
            alert("Email is required.");
            return false;
        }
        if ( addr.indexOf('@') == -1 ) {
            alert("Invalid email address");
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
