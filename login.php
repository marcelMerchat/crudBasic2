<?php
session_start();
require_once 'pdo.php';
require_once "util.php";

if ( isset($_POST['cancel'] ) ) {
    header('Location: index.php');
    return;
}

// 'if' statement fails for GET requests; there is no POST data.
if (   isset($_POST['email'])  && isset($_POST['pass'])) {
  if ( (strlen($_POST['email']) >= 1) && (strlen($_POST['pass']) >= 1 )) {
    unset($_SESSION['name']);  // Logout current user
    unset($_SESSION['user_id']);
    // If user Name and password fields have entries:
    if (strpos($_POST['email'], '@') === FALSE ) {
         $_SESSION['error'] = 'Invalid email address'.$_POST['email'];
         header( 'Location: login.php' ) ;
         return;
    }
    $sql = "SELECT user_id, email, password, block FROM users WHERE email = :em";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array( ':em' => $_POST['email']));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if($row === false) {
            $_SESSION['error'] = 'Incorrect password: The e-mail was not found: Please try again.';
            error_log('Login failure: '.$_POST['email'].' is not in database. Please check spelling');
            header( 'Location: login.php' );
            return;
    }
    if($row['block'] == 1) {
            $_SESSION['error'] = 'Account locked. Contact administrator regarding this login.';
            unset($_SESSION['user_id']);
            error_log('Login blocked: '.$_POST['email']);
    } else {
      $sql = "SELECT user_id, email, password FROM users WHERE email = :em AND password = :pw";
      $salt = 'XyZzy12*_';
      $stmt = $pdo->prepare($sql);
      $posted_pass = hash('md5', $salt.$_POST['pass']);
      $stmt->execute(array(
            ':em' => $_POST['email'],
            ':pw' => $posted_pass));
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      $user_pass = array_values($row)[2];
      if($user_pass === $posted_pass) {
           $_SESSION['success'] = 'Logged in.';
           $_SESSION['user_id'] = array_values($row)[0];
           $_SESSION['name'] = $_POST['email'];
           $_SESSION['email'] = $_POST['email'];
           $_SESSION['countEdu'] = 0;
           $_SESSION['countPosition'] = 0;
           $_SESSION['countSkill'] = 0;

           $_SESSION['full_name'] = get_name($pdo, $_SESSION['user_id']);
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
  }
} else {
    $_SESSION['error'] = 'Both fields must be filled out.';
    header( 'Location: login.php' );
    return;
}
}
?>

<!--   VIEW ------------------------------------>

<!DOCTYPE html>
<html>
<head>
  <?php
     require_once 'header.php';
  ?>
  <title>Marcel Merchat's Login Page</title>
</head>
<body>
</div>
<div class="content form center" id="main">
<form method="POST" action="login.php">
    <h1>Please Log In</h1>
    <p>
      <?php
        flashMessages();
      ?>

      <div class="container-form-entry more-top-margin-3x">
        <div class="more-input-margin less-bottom-margin less-top-margin box-input-label">
          <label class="less-bottom-margin less-top-margin" for="email">Email</label>
        </div><div class="less-top-margin less-bottom-margin box-profile-input">
          <input class="text-box" type="text" name="email" value='<?= htmlentities("") ?>' id="email">
        </div></div>
     <div class="container-form-entry more-top-margin-3x">
       <div class="more-input-margin less-bottom-margin less-top-margin box-input-label"><label for="id_1723">Password</label>
       </div><div class="less-top-margin less-bottom-margin box-profile-input">
          <input class="text-box" type="password" name="pass" value='<?= htmlentities("") ?>' id="id_1723" />
       </div>
     </div>
     <h3 class="more-top-margin-3x">
         <input class="button-submit" type="submit" onclick="return doValidate();" value="Login">
         <input class="button-submit" type="submit" name="cancel" value="Cancel">
         <!-- doValidate() is a JavaScript function (see script.js) for preliminary validation
             which runs before the post to the website. The server program
             at the website (see util.php) performs a final validation check.-->
     </h3>
     <p class="left quad-space"> If you forgot the password but remember the hint,
      you can recover <a href="forgotpass.php">here</a>.</p>
      <!-- Hint:
       <p class="big"> For a password hint, view source and find an account and password hint
                          in the HTML comments.
       Three accounts are:
       email: umsi@umich.edu php123  password: php123

       The password is the three character name of the
       programming language used in this class (all lower case)
       followed by 123.

       Other accounts:
       Elvis: epresley@musicland.edu  password: rock123
       Marilyn: mmonroe@whitehouse.gov  password: holly123  </p>   -->
       <p class="left quad-space">You can get your own login password
           <a href="apply.php"> here</a>.
       </p><p class="left quad-space">Take me back to the
           <a href="index.php"> first page</a>.
       </p>
  </form>

</div></body>
<script>
function doValidate() {
    console.log('Validating...');
    try {
        addr = document.getElementById('email').value;
        pw = document.getElementById('id_1723').value;
        console.log("Validating addr="+addr+" pw="+pw);
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
