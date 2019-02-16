<?php
session_start();
require 'timeout.php';
require_once 'pdo.php';
require_once "util.php";

if ( isset($_POST['cancel'] ) ) {
    header('Location: index.php');
    return;
}

// 'if' statement fails for GET requests; there is no POST data.
if (   isset($_POST['email'])  && isset($_POST['pass']) && isset($_POST['hint'])) {
  if ( (strlen($_POST['email']) >= 1) && (strlen($_POST['pass']) >= 1 ) && (strlen($_POST['pass']) >= 1 )) {
    unset($_SESSION['user_id']);
    // If user Name and password fields have entries:
    if (strpos($_POST['email'], '@') === FALSE ) {
         $_SESSION['error'] = 'Invalid email address'.$_POST['email'];
         header( 'Location: apply.php' ) ;
         return;
    } else {
        $email = $_POST['email'];
        $query = 'SELECT email FROM users WHERE email = :em';
        $stmt = $pdo->prepare($query);
        $stmt->execute(array(':em' => $_POST['email']));
        $matches =  $stmt->fetch(PDO::FETCH_ASSOC);
        //$length = count($matches); // count number of elements in an array
        if(!$matches) {
           // Empty arrays are 'falsey' in php.
           // Continue to assign login id when email is still unassigned.
        } else {
          $_SESSION['error'] = 'That email address already exists. ('.$length.') Please choose a different one.'
          .'nothing is '.$matches.' and vars are '.var_dump($matches);
          header( 'Location: apply.php' ) ;
          return;
        }
    }
    if (strlen($_POST['pass']) < 5) {

    }
    if (strlen($_POST['hint']) < 5) {
         $_SESSION['error'] = 'hint is too short'.$_POST['hint'];
         header( 'Location: apply.php' ) ;
         return;
    }
    $salt = 'XyZzy12*_';
    $hashed_pass = hash('md5', $salt.$_POST['pass']);
    //  Change database with protection from user input.
    $sql = "INSERT INTO users (name,email,password, hint)
              VALUES (:nm, :em, :hpw, :hnt)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
                  ':nm' => $_POST['name'],
                  ':em' => $_POST['email'],
                  ':hpw' => $hashed_pass,
                  ':hnt' => $_POST['hint']
    ));
    $user_id = $pdo->lastInsertId() + 0;
    echo 'added user to database';
    $sql = 'SELECT name, email, hint FROM users WHERE user_id = :uid';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(':uid' => $user_id));
    $row =  $stmt->fetch(PDO::FETCH_ASSOC);
    $myname = htmlentities($row['name']);
    $mymail = htmlentities($row['email']);
    $myhint = htmlentities($row['hint']);
    $_SESSION['email'] = 'merchat77@gmail.com';
    $_SESSION['emailSubject'] = 'New Login Account with ';
    $_SESSION['emailMessage'] = 'New user for '.$mymail.' added to database. Block column must be reset to 0 for activation.';
    require 'gmailer.php';
    $_SESSION['success'] = 'To activate this new login account, send email to database administrator (merchatDataTools@gmail.com) or call 773-852-1689.';
    error_log('new password application success for User-'.$_POST['user_id']);
    header( 'Location: login.php' );
    return;
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
<title>New Account</title>
</head>
<body>
<div class="content center" id="main">
<form method="POST">
    <h2 class="center">Get New User Login</h2>
    <p>
      <?php
        flashMessages();
      ?>
    </p>
    <div class="big center">
      <p class="center less-bottom-margin less-top-margin"><label class="less-bottom-margin"for="username">User Nickname</label></p>
      <p class="center less-bottom-margin less-top-margin"><input class="text-box" type="text" name="name" value='<?= htmlentities("") ?>' id="username"></p>
    </div><div class="center-entry">
      <p class="center less-bottom-margin"><label for="email">Email</label></p>
      <p class="center less-bottom-margin less-top-margin"><input class="text-box" type="text" name="email" value='<?= htmlentities("") ?>' id="email"></p>
    </div><div>
      <p class="center less-bottom-margin"><label for="id_1723"> Password</label></p>
      <p class="center less-bottom-margin less-top-margin"><input class="text-box"  type="password" name="pass" value='<?= htmlentities("") ?>' id="id_1723"></p>
    </div><div>
      <p class="center less-bottom-margin"><label for="id_1723h"> Hint</label></p>
      <p class="center less-bottom-margin less-top-margin"><input class="text-box"  type="password" name="hint" value='<?= htmlentities("") ?>' id="id_1723h"></p>
    </div>
      <p class="center big double-space">
            <input class="button-submit" type="submit" onclick="return doValidate();" value="Assign My Login">
            <input class="button-submit" type="submit" name="cancel" value="Cancel">
     </p>
</form>
      <p class="justify big">All submitted information is added to the
      database.
      </p>
      <p class="justify big">As this site is public and only a demonstration, do
          not enter any real identification or credit information here
           for yourself or anyone else
            including but not limited to phone numbers, email,
           addresses, account numbers, or any similar information
          in any of the form fields.
      </p>
      <p class="justify big">This is an example of a simple website and database
            that is suitable for a relatively small organization or business
            with less than 50,000 members, but is it modeled for future growth
            into a large organization.
      </p>
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
</body>
</html>
