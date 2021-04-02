<?php
// 'edit.php'
require_once "pdo.php";
require "util.php";
session_start();
// If the user is not logged-in
if ( ! isset($_SESSION['user_id']))  {
      header('Location: index.php');
      return; // or die('ACCESS DENIED');
}
// If the user requested cancel, go back to index.php
if ( isset($_POST['cancel']) ) {
    header('Location: index.php');
    return;
}
if ( isset($_GET['profile_id']) && strlen($_GET['profile_id'] > 0)) {
      $profileid = $_GET['profile_id'];
     // For edit screens, profile is provided with GET global variables.
     // By setting the session variable, tha add and edit screens can use the
     // same method.
      $_SESSION['profile_id'] = $_GET['profile_id'];
} else {
      $_SESSION['error'] = "Missing profile_id";
      header('Location: index.php');
      return;

}
$uid = $_SESSION['user_id'];
$profile = get_profile_information($profileid,$uid,$pdo);
if($profile===false){
    $_SESSION['error'] = 'Could not load profile';
    header('Location: index.php');
    return;
}
$fn = htmlentities(trim($profile['first_name']));
$ln = htmlentities(trim($profile['last_name']));
$em = htmlentities(trim($profile['email']));
$prof = htmlentities(trim($profile['profession']));
$gl = htmlentities(trim($profile['goal']));
$resume_style = htmlentities(trim($profile['resume_style']));
// Check for initial GET request without (or without) post information
// Only required entries are the first and laat names and the email.
if (isset($_POST['first_name']) && isset($_POST['last_name']) &&
    isset($_POST['email']) ) {

    unset($_SESSION['error']);
    unset($_SESSION['success']);
    unset($_SESSION['message']);
    $_SESSION['message'] = ' ';
    $profileid = $_SESSION['profile_id'];
    $profileInserted = insertProfile($pdo,$IsUpdate=true);
    if($profileInserted===false){
           $_SESSION['error'] = $_SESSION['error']
              . ' Could not change name or email. Please try again.';
           header('Location: edit.php?profile_id='.$_POST['profile_id']);
           return;
    }
//  Check Professional Goals
    // Profession might be vaild but have a deleted word.
    // This will always be an UPDATE procedure because profession and goals
    // are fields in the Profile table.
    $professionAdded = insertProfession($profileid, $pdo, $IsUpdate=true);
    $goalsAdded = insertProfessionalGoals($profileid, $pdo, $IsUpdate=true);
//  Skills
    // Delete old skill entries; insert new list
    $stmt = $pdo->prepare('DELETE FROM SkillSet WHERE profile_id = :pid');
    $stmt->execute(array(':pid' => $profileid));
    insertSkillSet($profileid,$pdo);
//  Hobbies and Interests
    // Delete old hobbies; insert new list
    $stmt = $pdo->prepare('DELETE FROM HobbyList WHERE profile_id = :pid');
    $stmt->execute(array(':pid' => $profileid));
    insertHobbyList($profileid,$pdo);

    $stmt = $pdo->prepare('DELETE FROM Personal WHERE profile_id = :pid');
    $stmt->execute(array(':pid' => $profileid));
    insertPersonal($profileid,$pdo);
//  Special Projects and Demos
    // Delete old projects; insert new list
    $stmt = $pdo->prepare('DELETE FROM Project WHERE profile_id = :pid');
    $stmt->execute(array(':pid' => $profileid));
    insertProjects($profileid,$pdo);

//  Education
    // Delete old education entries; recreate new list
    $stmt = $pdo->prepare('DELETE FROM Education WHERE profile_id = :pid');
    $stmt->execute(array(':pid' => $profileid));
    insertEducations($profileid,$pdo);
//  Certificates
    // Delete old certificate entries; recreate new list
    $stmt = $pdo->prepare('DELETE FROM Certificates WHERE profile_id = :pid');
    $stmt->execute(array(':pid' => $profileid));
    insertCertificates($profileid,$pdo);
//  Positions
    // Clear old position entries; recreate new list
    $stmt = $pdo->prepare('DELETE FROM Position WHERE profile_id = :pid');
    $stmt->execute(array(':pid' => $profileid));
    // Clear old activitites; recreate new list
    $stmt = $pdo->prepare('DELETE FROM Activity WHERE profile_id = :pid');
    $stmt->execute(array(':pid' => $profileid));
    // Insert new position entries; create replacement list
    insertPositions($profileid, $pdo);
    changeResumeStyle($pdo);
    //$_SESSION['count_position'] = get_position_count($profileid,$pdo);
    // foreach ($_POST as $key => $value) {
    //    $_SESSION['message'] = $_SESSION['message']
    //      ."Field ".htmlspecialchars($key)." is ".htmlspecialchars($value)."<br>";
    //      store_error_messages();
    // }
    // Only approved users may enter contact information.
    if ($_SESSION['contact_info'] == 0) {
        header('Location: index.php');
        return;
    }  else {
        header("Location: contacts.php?profile_id=".$profileid);
        return;
    }
} else {
  //$_SESSION['message'] = ' Initial Get Request: nothing posted yet. ';
}
?>

<!--            VIEW                -->

<!DOCTYPE html>
<html>
<head>
<title>Change Profile</title>
<?php
   require_once 'header.php';
   // Get educations and positions from database YYz6UJyE
   $educations = loadEdu($profileid,$pdo);
   $Certificates = loadCertif($profileid,$pdo);
   $projects = loadProjects($profileid,$pdo);
   // var_dump($Certificates);
   //
   // $sql = 'SELECT Certificates.year, Certificates.award_link as award, Award.name As degree,
   //      Institution.name As institution, Institution.provider As provider
   //      FROM Institution LEFT JOIN Certificates
   //         ON Certificates.institution_id = Institution.institution_id
   //      LEFT JOIN Award
   //         ON Certificates.award_id = Award.award_id
   //      WHERE Certificates.profile_id = :pid';
   // $stmt = $pdo->prepare($sql);
   // $stmt->execute(array(':pid' => $profileid) );
   // $retrieved = $stmt->fetchALL(PDO::FETCH_ASSOC);
   //   $_SESSION['message'] = 'Loading certifications '
   //     .' Profile-'.$profileid;
   // store_error_messages();
   // $Certifications = array_values($retrieved);
   // var_dump($Certifications);
   // var_dump($retrieved);
   //print_r($Certificates);
   $positions = loadPos($profileid,$pdo);
   $skills = loadSkill($profileid,$pdo);
   $hobbies= loadHobbies($profileid,$pdo);
   $interests = loadPersonal($profileid,$pdo);
?>
<style>
div.radio {
    font-size: 1.2rem;
    box-sizing: border-box;
    text-align: right;
    width: 16em;
    padding: 20px;
    padding-right: 20px;
    border: 0px solid #008800;
    margin: auto;
}
label.container-radio {
  display: block;
  position: relative;
  text-align: left;
  padding-left: 9em;
  margin: 2px;
  cursor: pointer;
  font-size: 18px;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}

/* Hide the browser's default radio button */
.container-radio input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
  height: 0;
  width: 0;
}

/* Create a custom radio button */
.checkmark {
  position: absolute;
  top: 0;
  left: 7em;
  height: 25px;
  width: 25px;
  background-color: #bbc;
  border-radius: 50%;
}

/* On mouse-over, add a grey background color */
.container-radio:hover input ~ .checkmark {
  background-color: #ccc;
}

/* Create the indicator (the dot/circle - hidden when not checked) */
.checkmark:after {
  content: "";
  position: absolute;
  display: none;
}

/* Show the indicator (dot/circle) when checked */
.container-radio input:checked ~ .checkmark:after {
  display: block;
  outline: 0px solid orange;
  color: 5px blue;
}

/* Style the indicator (dot/circle) */
.container-radio .checkmark:after {
 	top: 9px;
	left: 9px;
	width: 8px;
	height: 8px;
	border-radius: 30%;
	background: white;
  border: 0px solid #ff8800;
}

/* When the radio button is checked, add a green background */
.container-radio input:checked ~ .checkmark {
  background-color: #AA8800;
}
</style>
</head>
<body>
    <div class="center" id="main">
    <h4 class="less-bottom-margin center">Edit profile</h4>

    <div id="dialog-confirm" title="New Message: ">
      <p id="message_field">
        <!-- Alert message is placed in the span tag below. -->
        <script type="text" id="message_template">
            <span></span>
        </script>
      </p>
    </div>
    <?php
        flashMessages();
    ?>
    <h5 class="more-top-margin-1x more-bottom-margin center">Click a button to add new information or to edit existing information.</h5>
    <div class="radio-input-form">
      <div class="link-button">
        <p class="right">
        <a class="button-submit anchor-radio-button" href="
        <?php
        echo 'editProfile.php?profile_id='.$profileid;
        ?>
        "><span class="round">&nbsp; &nbsp;</span></a>
      </div><div class="link-button-label">
              <p>Profile, education, employment</p>
      </div>
    </div>

    <div class="radio-input-form">
      <div class="link-button">
        <p class="right">
              <a class="button-submit anchor-radio-button" href="
              <?php echo 'editSkill.php?profile_id='.$profileid; ?>
              "> &nbsp; &nbsp;</a>
              </p>
      </div><div class="link-button-label">
              <p>Skills</p>
      </div>
    </div>

    <div class="radio-input-form">
      <div class="link-button">
        <p class="right">
              <a class="button-submit anchor-radio-button" href="
              <?php echo 'editCertificates.php?profile_id='.$profileid ?>
              "> &nbsp; &nbsp;</a>
              </p>
      </div><div class="link-button-label">
              <p>Certificates</p>
      </div>
    </div>

    <div class="radio-input-form">
      <div class="link-button">
        <p class="right">
              <a class="button-submit anchor-radio-button" href="
              <?php echo 'editSpecialProjects.php?profile_id='.$profileid ?>
              "> &nbsp; &nbsp;</a>
              </p>
      </div><div class="link-button-label">
              <p>Special projects and demos</p>
      </div>
    </div>

    <div class="radio-input-form">
      <div class="link-button">
        <p class="right">
              <a class="button-submit anchor-radio-button" href="
              <?php echo 'editInterestsActivities.php?profile_id='.$profileid ?>"> &nbsp; &nbsp; </a>
        </p>
      </div><div class="link-button-label">
              <p>Interests</p>
      </div>
    </div>
<?php
    if ($_SESSION['contact_info'] == 1) {
      echo ' <div class="radio-input-form">
        <div class="link-button">
          <p class="right">
                <a class="button-submit anchor-radio-button" href="'
                .'contacts.php?profile_id='.$profileid.'"> &nbsp; &nbsp;</a>
          </p>
        </div><div class="link-button-label">
                <p>Contact information.</p>
        </div>
      </div>';
    }

// <input class="button-submit spacer wide-10char" type="submit" value="Cancel" name="cancel"/>
echo '<h5 class="center more-top-margin-2x">
<a class="button-submit anchor-radio-button" href="'
.'index.php?profile_id='.$profileid.'; ?>
"> &nbsp; &nbsp;</a> &nbsp; Finished with editing</h5>';
?>
</div> <!-- main -->
<script>
$(document).ready(function() {
        window.console && console.log('Document ready called ');
        isMobileDevice = Boolean("<?php echo isMobile() ?>" == 1);
        isLargeDevice = !isMobileDevice;
        window.console && console.log('Mobile device = ' + isMobileDevice);
        var w = $( window ).width();
        window.console && console.log('The window width is = ' + w);
        adjustWindow();
        submitted = false;
});
</script>
</body>
</html>
