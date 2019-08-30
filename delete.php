<?php
require_once "pdo.php";
require_once "util.php";
session_start();
//require 'timeout.php';

if ( ! isset($_SESSION['user_id']))  {
      die('ACCESS DENIED');
}
if ( isset($_POST['delete']) && isset($_POST['profile_id']) ) {
    $sql = "DELETE FROM Profile WHERE profile_id = :zip";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(':zip' => $_POST['profile_id']));
    $_SESSION['success'] = 'Record deleted';
    header( 'Location: index.php' ) ;
    return;
}

if ( ! isset($_GET['profile_id']) ) {
  $_SESSION['error'] = "Missing profile_id";
  header('Location: index.php');
  return;
}

$stmt = $pdo->prepare("SELECT profile_id, first_name, last_name FROM Profile where profile_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = $_GET['profile_id'].'Bad choice for first and last names'.$row['profile_id'];
    header( 'Location: index.php' ) ;
    return;
}

?>
<!--  VIEW or HTML code for model-view-controller  -->
<!DOCTYPE html>
<html>
<head>
<title>Delete Profile</title>
<?php
   require_once 'header.php';
?>
</head>
<body>
<div class="center" id="main">
<br />
<h3 class="center">Confirm Deletion</h3>
<br />
<form method="post">

<div class="container-form-entry"> <!-- column container of one column -->
  <div class="less-bottom-margin less-top-margin box-input-label">First Name:
  </div><div class="less-top-margin less-bottom-margin box-input-label">
     <?= htmlentities($row['first_name']) ?>
  </div>
</div><div class="container-form-entry more-top-margin-3x">
  <div class="less-bottom-margin less-top-margin box-input-label">Last Name:
  </div><div class="less-top-margin less-bottom-margin box-input-label">
     <?= htmlentities($row['last_name']) ?>
  </div>
</div>
<br />
<h4 class="center">
<input type="hidden" name="profile_id" value="<?= $row['profile_id'] ?>">
<input class="button-submit spacer" type="submit" value="Delete" name="delete">
</h4>
</br>
</br>
<h2 class="center spacer"><a href="index.php">Cancel</h2>
</form>
</div>
<script>
$(document).ready(function() {
  window.console && console.log('Document ready called ');
  isMobileDevice = Boolean("<?php echo isMobile() ?>" == 1);
  isLargeDevice = !isMobileDevice;
  window.console && console.log('Mobile device = ' + isMobileDevice);
  var w = $( window ).width();
  window.console && console.log('The window width is = ' + w);
  adjustDataEntryWindow();
});
</script>
</body>
</html>
