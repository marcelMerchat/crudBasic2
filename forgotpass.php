<?php
session_start();
require_once 'pdo.php';
require_once "util.php";

if ( isset($_POST['cancel'] ) ) {
    header('Location: index.php');
    return;
}

// 'if' statement fails for GET requests; there is no POST data.
if (   isset($_POST['email'])  || isset($_POST['hint'])) {
   if ( (strlen($_POST['email']) >= 1) && (strlen($_POST['hint']) >= 1 )) {
       unset($_SESSION['name']);  // Logout current user
       unset($_SESSION['user_id']);
   // If user Name and password fields have entries:
    if (strpos($_POST['email'], '@') === FALSE ) {
         $_SESSION['error'] = 'Invalid email address'.$_POST['email'];
         //header( 'Location: apply.php' ) ;
         //return;
    }
    if (strlen($_POST['hint']) < 5) {
         $_SESSION['error'] = 'hint is too short'.$_POST['hint'];
         //header( 'Location: apply.php' ) ;
         //return;
    }
//     $rememberHint = false;
    $hint = $_POST['hint'];
    $sql = 'SELECT user_id, email, hint FROM users WHERE email = :em AND hint = :hnt';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(':em' => $_POST['email'], ':hnt' => $_POST['hint']));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    print_r($row);
    print_r("\n".$_POST['email']);
    print_r("\n".$_POST['hint']);

    if($row === false) {
            $_SESSION['error'] = 'Hint not found: Please try again.';
            error_log('Login failure: '.$_POST['email'].' hint not in database. Please check spelling');
            //header( 'Location: forgotpass.php' );
            //return;
    } else if ( $row['email'] == $_POST['email'] AND $row['hint'] == $_POST['hint']) {
          //  hint found in database; does it match email address?
          $rememberHint = true;
          $_SESSION['user_id'] = array_values($row)[0];
          $_SESSION['email'] = $_POST['email'];
          echo 'hint matches '.$row;
          $_SESSION['success'] = 'You entered the hint. You may change the password.';
          error_log('new password application success for User-'.$_POST['user_id']);
          header( 'Location: recoverPass.php' );
          return;
   } else {
	     $_SESSION['error'] = 'Incorrect email or hint';
       error_log('password application failure: '.$_POST['email'].'Email does not match hint.');
   }
} else {
  $_SESSION['error'] = 'Incorrect email or hint';
  error_log('password application failure: '.$_POST['email'].'Email does not match hint.');
}
} else {
  $_SESSION['success'] = 'Ready to recover password. Enter email and hint.';
}
?>

<!-- VIEW ------------------------------------>

<!DOCTYPE html>
<html>
<head>
<?php
require_once 'header.php';
?>
<title>New User</title>
</head>
<body>
<div class="content center" id="main">
<?php
  flashMessages();
?>
<form method="POST">
  <h2>Change Password</h2>
  <p class="big">
      <label for="email">Email</label>
      <input class="email" type="text" name="email" value="<?= htmlentities('') ?>" id="email">
      </p><p class="big">
      <label for="id_1723h">Hint</label>
      <input class="password"  type="password" name="hint" value="<?= htmlentities('') ?>" id="id_1723h">
      </p>

    <p class="big double-space">
                <input class="button-submit" type="submit" onclick="return doValidateHint();" value="Submit">
                <input class="button-submit" type="submit" name="cancel" value="Cancel">
    </p>


</form>
</body>
<script>
function doValidateHint() {
    console.log('Validating...');
    try {
        addr = document.getElementById('email').value;
        hnt = document.getElementById('id_1723h').value;
        console.log("Validating addr="+addr+" hnt="+hnt);
        if (addr == null || addr == "" || hnt == null || hnt == "") {
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
