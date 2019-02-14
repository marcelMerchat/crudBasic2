<?php
session_start();
require 'timeout.php';
require_once 'pdo.php';
require_once "util.php";

if ( ! isset($_SESSION['user_id']))  {
      die('ACCESS DENIED');       //return;
}

if ( isset($_POST['cancel'] ) ) {
    header('Location: index.php');
    return;
}

// 'if' statement fails for GET requests; there is no POST data.
if ( isset($_POST['pass'])) {
  if ( strlen($_POST['pass']) >= 5 ) {
    // If user Name and password fields have entries:

    $salt = 'XyZzy12*_';
        $hashed_pass = hash('md5', $salt.$_POST['pass']);
        $u = (int) $_SESSION['user_id'];
    //  Change database without user control or hacking issues
        $sql = "UPDATE users SET password = ? WHERE user_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array($hashed_pass,$u));
        //$stmt->execute();
        echo 'changed password';
    //  Retrieve information from database while protecting users from
    //  scripts or html code such as redirection to another website
        $sql = 'SELECT name, email, hint FROM `users` WHERE user_id = ?';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array($u));
        $row =  $stmt->fetch(PDO::FETCH_ASSOC);
        $myname = htmlentities($row['name']);
        $mymail = htmlentities($row['email']);
        $myhint = htmlentities($row['hint']);
        unset($_SESSION['error']);
        $_SESSION['email'] = $mymail;
        $_SESSION['emailSubject'] = 'Password Change';
        $_SESSION['emailMessage'] = 'The password for '.$mymail.' was changed. If you did not did not authorize this, please contact database administrator (merchatDataTools@gmail.com) or call 773-852-1689.';
        require 'gmailer.php';
        $_SESSION['success'] = 'The password has been changed. A confirmation has been forwarded to your '.$mymail.' address.';
        error_log('new password application success for User-'.$_POST['user_id']);
        header( 'Location: login.php' );
        return;
    } else {
		    $_SESSION['error'] = 'Incorrect password, please try again.';

    }
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
</div>
<div class="content center" id="main">
<br/>
<form method="POST">
    <h1 class="center">Get New Password</h1>
<?php
  flashMessages();
?>
  <br/>
  <p class="center big">
        <label for="id_1723">Password</label>
        <input class="password"  type="password" name="pass" value="<?= htmlentities('') ?>" id="id_1723">
  </p>
  <br/>
  <p class="center big double-space">
        <input class="button-submit" type="submit" onclick="return doValidatePass();" value="Assign Password">
        <input class="button-submit" type="submit" name="cancel" value="Cancel">
  </p>
</form>
</body>
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
        return true;
    } catch(e) {
        return false;
    }
    return false;
  }
</script>
