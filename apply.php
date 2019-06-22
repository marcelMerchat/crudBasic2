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
if (   isset($_POST['email']) &&  isset($_POST['hint']) ) {
  if ( strlen($_POST['email']) >= 1) {
    unset($_SESSION['user_id']);
    // If user Name and password fields have entries:
    if (strpos($_POST['email'], '@') === FALSE ) {
         $_SESSION['error'] = 'Invalid email address'.$_POST['email'];
         header( 'Location: apply.php' ) ;
         return;
    } else if (strlen($_POST['hint']) < 5){
        $_SESSION['error'] = 'Hint '.$_POST['hint'].' is too short.';
        header( 'Location: apply.php' ) ;
        return;
    } else {
        $email = $_POST['email'];
        $userName = $_POST['name'];
        $_SESSION['userName'] = $_POST['name'];
        $query = 'SELECT email FROM users WHERE email = :em';
        $stmt = $pdo->prepare($query);
        $stmt->execute(array(':em' => $_POST['email']));
        $matches =  $stmt->fetch(PDO::FETCH_ASSOC);
        if(!$matches) {
           // Empty arrays are 'falsey' in php.
           // Continue to assign login id when email is still unassigned.
        } else {
          $_SESSION['error'] = 'That email address already exists. Please choose a different one.';
          $_SESSION['success'] = false;
          header( 'Location: apply.php' ) ;
          return;
        }
    }

    //$salt = 'XyZzy12*_';
    $salt = generateRandomString(10);
    $pass = generateRandomString(8);
    $hashed_pass = hash('md5', $salt.$pass);
//  Change database with protection from user input.
    $sql = "INSERT INTO users (name,email,password,random, hint, block)
               VALUES (:nm, :em, :hpw, :rnd, :hnt, :blck)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
         ':nm' => $_POST['name'],
         ':em' => $_POST['email'],
         ':hpw' => $hashed_pass,
         ':rnd' => $salt,
         ':hnt' => $_POST['hint'],
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
    .' with hint '.$_POST['hint']
    .'. You may login using the temporary password '.$pass
    .' at this <a href="http://www.marcel-merchat.com/crudBasic/login.php">'
    .'address</a>'
    .'</p>'
    .'</body></html>';
       //.' delete this <a href="localhost/crudBasic/login.php">local address</a>'
    error_log('New password application for User-'.$user_id);
//  Attempt email notification before entering new password into database.
    require 'gmailer.php';
    if ($_SESSION['success'] === false) {
        $_SESSION['emailMessage'] = 'Success is false after executing gmailer. '
                        .'The email address was rejected. You may try again.';
        header( 'Location: apply.php' );
        return;
    }
    $sql = 'SELECT name, email, hint FROM users WHERE user_id = :uid';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(':uid' => $user_id));
    $row =  $stmt->fetch(PDO::FETCH_ASSOC);
    $myname = htmlentities($row['name']);
    $mymail = htmlentities($row['email']);
    $myhint = htmlentities($row['hint']);
    // There is some difficulty sending a second email, here is the code:
    //$_SESSION['email'] = 'merchat77@gmail.com';
    //$_SESSION['emailSubject'] = 'New Login Account with www.marcel-merchat.com';
    //$_SESSION['emailMessage'] = 'New user for '.$mymail.' added to database. Block column must be reset to 0 for activation.';
    //$_SESSION['userName'] = "Administrator";
    //require 'gmailer.php';
    //$_SESSION['success'] = 'To activate this new login account, send email to database administrator (merchatDataTools@gmail.com) or call 773-852-1689.';
    //$_SESSION['success'] = 'You may now login. If there are any questions, send email to database administrator (merchatDataTools@gmail.com).';
    $_SESSION['success'] = 'A temporary password has been sent to your '.$mymail.' address.';
    header('Location: login.php');
    return;
  }
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
</head>
<body>
<div class="content center" id="main">
<?php
    flashMessages();
?>
<form method="POST" name="form1">
    <h2 class="center">Get New User Login</h2>
    <p>You should receive an email containing a temporary password to login
       within a few minutes. You can reset the password using the hint.
       Please do not use security information for the hint.
    </p>
    <div class="big center">
      <p class="center less-bottom-margin less-top-margin"><label class="less-bottom-margin" for="username">User Nickname</label></p>
      <p class="center less-bottom-margin less-top-margin"><input class="text-box" type="text" name="name" value='<?= htmlentities("") ?>' id="username"></p>
    </div><div class="center-entry">
      <p class="center less-bottom-margin"><label for="email">Email</label></p>
      <p class="center less-bottom-margin less-top-margin"><input class="text-box" type="text" name="email" value='<?= htmlentities("") ?>' id="email"></p>
    </div>
      <!-- <div>
      <p class="center less-bottom-margin"><label for="id_1723"> Password</label></p>
      <p class="center less-bottom-margin less-top-margin"><input class="text-box"  type="password" name="pass" id="id_1723"></p>
    </div> -->
    <div>
      <p class="center less-bottom-margin"><label for="id_1723h"> Hint</label></p>
      <p class="center less-bottom-margin less-top-margin"><input class="text-box"  type="password" name="hint" id="id_1723h"></p>
    </div>
      <p class="center big double-space">
            <input class="button-submit" type="submit" onclick="return doValidate();" value="Assign My Login">
            <input class="button-submit" type="submit" name="cancel" value="Cancel">
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
function doValidate() {
    console.log('Validating...');
    try {
        addr = document.getElementById('email').value;
        //pw = document.getElementById('id_1723').value;
        hnt = document.getElementById('id_1723h').value;
        //console.log("Validating addr="+addr+" pw="+pw);
        if (addr == null || addr == "" || hnt == null || hnt == "") {
            alert("An email address is required.");
            return false;
        }
        if ( addr.indexOf('@') == -1 ) {
            alert("Invalid email address");
            return false;
        }
        if (hnt == null || hnt == "") {
            alert("A hint is required.");
            return false;
        } else if (hnt.length < 5){
            alert("The hint must have at least 5 characters.");
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
