<?php
session_start();
require_once 'pdo.php';
require_once "util.php";

if ( isset($_POST['cancel'] ) ) {
    header('Location: index.php');
    return;
}

// 'if' statement fails for GET requests; there is no POST data.
if (   isset($_POST['email']) && isset($_POST['pass']) ) {
  $email = trim($_POST['email']);
  if ( strlen($email) >= 1 && strlen($_POST['pass']) >= 1 ) {
    unset($_SESSION['user_id']);  // Logout current user
    // If user Name and password fields have entries:
    if (strpos($_POST['email'], '@') === FALSE ) {
         $_SESSION['error'] = 'Invalid email address'.$_POST['email'];
         header( 'Location: login.php' ) ;
         return;
    }
    $sql = "SELECT random, timeout, block FROM users WHERE email = :em";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array( ':em' => $_POST['email']));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if($row == false) {
            $_SESSION['error'] = 'Incorrect email or password: Please check spelling and try again.';
            error_log('Login failure: '.$_POST['email'].' is not in database.');
            header( 'Location: login.php' );
            return;
    }
    if($row['block'] == 1) {
            $_SESSION['error'] = 'Something went wrong. Contact administrator.';
            unset($_SESSION['user_id']);
            error_log('Login blocked: '.$_POST['email']);
            header( 'Location: login.php' );
            return;
    }
    $totalseconds = -1;
    $time_limit = 3000;
    // Login blocked after time limit
    if($row['timeout'] == 1){
        $mysqlfield = 'password_time';
        $totalsecs = getElapsedSeconds($_POST['email'],$mysqlfield,$pdo);
        //print_r('. The elapsed time was '.$totalsecs);
        if($totalsecs > $time_limit) {
          $_SESSION['error'] = 'Temporary password expired after 30 minutes. '
          . 'To get a new password, enter your email address.';
          //print_r('. The elapsed time exceeded 30 minutes. ');
          unset($_SESSION['user_id']);
          error_log('Temporary password expired for '.$_POST['email']);
        } else if ( !($totalsecs > 0) ){
          unset($_SESSION['user_id']);
          $_SESSION['error'] = 'Something went wrong. To get a new password, '
          .'contact the administrator at merchatDataTools@gmail.com or '
          .'call 773-852-1689. ';
          error_log('Time difference was not greater than zero for '.$_POST['email']);
        }
        if( !($totalsecs > 0) || $totalsecs > $time_limit) {
          print_r('. Something wrong here. The elapsed time was '.$totalsecs);
        }
     }
    //$salt = 'XyZzy12*_';
    $sql = "SELECT user_id, password, random FROM users WHERE email = :em";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
        ':em' => $_POST['email']));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    var_dump($row);
    $user_pass = $row['password'];
    $salt = $row['random'];
    $posted_pass = hash('md5',$salt.$_POST['pass']);
          //print_r('The posted password is '.$posted_pass);
          //print_r('The database pass '.$row['password']);
    $hashed_hint2 = hash('md5', $salt.'admin1r23');
    echo("<br />");
          //print_r('. The salt is '.$salt);
          //print_r('. The hash for "admin1r23" is '.$hashed_hint2);
    if($user_pass === $posted_pass) {
              $_SESSION['success'] = 'Logged in.';
              $_SESSION['user_id'] = $row['user_id'];
              $_SESSION['email'] = $_POST['email'];
              $_SESSION['countEdu'] = 0;
              $_SESSION['countPosition'] = 0;
              $_SESSION['countSkill'] = 0;
              $_SESSION['userName'] = get_name($_SESSION['user_id'],$pdo);
              error_log('Login success: '.$_POST['email']);
              $_SESSION['LAST_ACTIVITY'] = time();
              header( 'Location: index.php');
              return;
    } else {
			        $_SESSION['error'] = 'Incorrect password';
              error_log('Login failure: '.$_POST['email'].' Password is incorrect.');
              header( 'Location: login.php' );
              return;
    }
  } else {
      $_SESSION['error'] = ' Incorrect email or password. ';
      header( 'Location: login.php' );
      return;
  }
} else {
      // This is the fall through
}
?>

<!--  VIEW or HTML code for model-view-controller  -->
<!DOCTYPE html>
<html>
<head>
  <?php
     require_once 'header.php';
  ?>
  <title>Login</title>
</head>
<body>
<div class="form center" id="main">
<form method="POST" action="login.php">
    <h1>Please Log In</h1>
    <p>
      <?php
        flashMessages();
      ?>

      <div class="container-form-entry more-top-margin-3x">
        <div class="more-input-margin less-bottom-margin less-top-margin box-input-label">
                <label class="less-bottom-margin less-top-margin center" for="email">Email</label>
        </div><div class="less-top-margin less-bottom-margin box-profile-input">
          <input class="text-box" type="text" name="email" value='<?= htmlentities("") ?>' id="email">
        </div></div>
     <div class="container-form-entry more-top-margin-3x">
       <div class="more-input-margin less-bottom-margin less-top-margin box-input-label center">
               <label class="center" for="id_1723">Password</label>
       </div><div class="less-top-margin less-bottom-margin box-profile-input">
          <input class="text-box" type="password" name="pass" value='<?= htmlentities("") ?>' id="id_1723" />
       </div>
     </div>
     <h3 class="more-top-margin-3x center">
         <input class="button-submit" type="submit" onclick="return doValidate();" value="Login">
         &nbsp;
         <input class="button-submit" type="submit" name="cancel" value="Cancel">
         <!-- doValidate() is a JavaScript function (see script.js) for preliminary validation
             which runs before the post to the website. The server program
             at the website (see util.php) performs a final validation check.-->
     </h3>
     <!-- Hint: -->
       <p class="justify quad-space">You can get your own password
           <a href="apply.php"> here</a> or login as
           guest@mycompany.com using password 'guest123' for a fast preview.
       </p><p class="justify">If you forgot the password,
                you can get a new one <a href="forgotpass.php">here</a>.
       </p><p class="justify left">Take me back to the
           <a href="index.php"> first page</a>.
       </p>
  </form>
</div>
<script>
function doValidate() {
    //console.log('Validating...');
    try {
        addr = document.getElementById('email').value;
        pw = document.getElementById('id_1723').value;
        //console.log("Validating addr="+addr+" pw="+pw);
        if (addr == null || addr == "" || pw == null || pw == "") {
            alert("Both fields must be filled out");
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
</body></html>
