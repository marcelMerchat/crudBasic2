?php
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
$myhint = "";
// 'if' statement fails for GET requests; there is no POST data.
if ( isset($_POST['name'])  || isset($_POST['email'])  || isset($_POST['hint'])) {
  $myname = trim(htmlentities($_POST['name']));
  $mymail = trim(htmlentities($_POST['email']));
  $myhint = trim(htmlentities($_POST['hint']));
  if ( strlen($_POST['name']) >= 1 && strlen($_POST['email']) >= 1 && (strlen($_POST['hint']) >= 1 )) {
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
 //  Validate Hint
     $hint = trim($_POST['hint']);
     if (strlen(trim($_POST['hint'])) < 5) {
          $_SESSION['error'] = $_SESSION['error']
             .' Hint is too short: '.$_POST['hint'].'. ';
          $numErrors = $numErrors + 1;
     }
     $validHint = validateName($hint,$pdo);
     if(!$validHint) {
          $_SESSION['error'] = $_SESSION['error']
            .' Please check the hint entry. Use letters and numbers. ';
          $numErrors = $numErrors + 1;
      }
  //  Error handling
      if ($numErrors > 0){
        $_SESSION['success'] = false;
        // if ($numErrors === 1){
        //     $topicSentence = ' There is one thing to check. ';
        // } else if ($numErrors > 1){
        //     $topicSentence = ' There are '.$numErrors.' things to check. ';
        // }
        //$_SESSION['error'] = $topicSentence.$_SESSION['error'];
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
     $_SESSION['hint'] = $hint;
     $salt = generateRandomString(10);
     $pass = generateRandomString(8);
     $hashed_pass = hash('md5', $salt.$pass);
     $hashed_hint = hash('md5', $salt.$hint);
//   Create time string
     $current_time = date("Y-m-d H:i:s");
//   Generate date object with string
//   $date = new DateTime($current_time)
//   Change database with protection from user input.
     $sql = "INSERT INTO users (name,email,password,random, hint, password_time, block)
               VALUES (:nm, :em, :hpw, :rnd, :hnt, :pwt, :blck)";
     $stmt = $pdo->prepare($sql);
     $stmt->execute(array(
         ':nm' => $_POST['name'],
         ':em' => $email,
         ':hpw' => $hashed_pass,
         ':rnd' => $salt,
         ':hnt' => $hashed_hint,
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
       // .' delete this <a href="localhost/crudBasic/login.php">local address</a>'
       //.' at this <a href="localhost/crudbasic/assignPassword.php">address</a> </p></body></html>';
       //.' at this <a href="http://www.marcel-merchat.com/crudbasic/assignPassword.php">address</a> </p></body></html>';
        error_log('New password application for '.$email);
        //  Attempt email notification before entering new password into database.
      require 'gmailer.php';
      //$_SESSION['success'] = true;
      if ($_SESSION['success'] === false) {
          $_SESSION['emailMessage'] =
                         'Something went wrong trying to send an email. ';
          header( 'Location: apply.php' );
          return;
      }
      $sql = 'SELECT name, email, hint FROM users WHERE user_id = :uid';
      $stmt = $pdo->prepare($sql);
      $stmt->execute(array(':uid' => $user_id));
      $row =  $stmt->fetch(PDO::FETCH_ASSOC);
        //$_SESSION['email'] = 'merchat77@gmail.com';
        //$_SESSION['emailSubject'] = 'New Login Account with www.marcel-merchat.com';
        //$_SESSION['emailMessage'] = 'New user for '.$mymail.' added to database. Block column must be reset to 0 for activation.';
        //$_SESSION['userName'] = "Administrator";
        //require 'gmailer.php';
        //$_SESSION['success'] = 'To activate this new login account, send email to database administrator (merchatDataTools@gmail.com) or call 773-852-1689.';
        //$_SESSION['success'] = 'You may now login. If there are any questions, send email to database administrator (merchatDataTools@gmail.com).';
      $_SESSION['success'] =
            'A temporary password has been sent to your '.$mymail.' address.';
      header('Location: login.php');
      return;
    } else {
      $_SESSION['error'] = 'Name, Email, and hint are required.';
    } // non-zero length for email and password
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
<form method="POST" name="apply.php">
    <h2 class="center">Get New User Login</h2>
    <p class="justify">You should receive an email containing
           a temporary password to login within a few minutes.
    </p>
    <div class="big center">
      <p class="center less-bottom-margin less-top-margin">
             <label class="less-bottom-margin" for="username">Name</label>
      </p><p class="center less-bottom-margin less-top-margin">
             <input class="text-box" type="text" name="name"
                    value='<?= $myname ?>' id="username">
      </p>
    </div><div class="center-entry">
      <p class="center less-bottom-margin"><label for="email">Email</label>
      </p><p class="center less-bottom-margin less-top-margin">
           <input class="text-box" type="text" name="email"
                  value='<?= $mymail ?>' id="email"></p>
    </div>
      <!-- <div>
      <p class="center less-bottom-margin"><label for="id_1723"> Password</label></p>
      <p class="center less-bottom-margin less-top-margin"><input class="text-box"  type="password" name="pass" id="id_1723"></p>
    </div> -->
    <div>
      <p class="center less-bottom-margin"><label for="id_1723h"> Hint</label>
      </p><p class="center less-bottom-margin less-top-margin">
          <input class="text-box"  type="password" name="hint"
                 value='<?= $myhint ?>' id="hint">
      </p>
    </div>
      <p class="center big double-space">
            <input class="button-submit" type="submit"
                   onclick="return validateApplication();"
                   value="Assign My Login">
            <input class="button-submit" type="submit" name="cancel"
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
function doValidate() {
    console.log('Validating...');
    try {
        uName = document.getElementById('username').value;
        addr = document.getElementById('email').value;
        //pw = document.getElementById('id_1723').value;
        hnt = document.getElementById('hint').value;
        //console.log("Validating addr="+addr+" pw="+pw);
        if (addr == null || addr == "" || hnt == null ||
             hnt == "" || uName == null || uName == "" ) {
            alert("Name, email, and hint are required.");
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
