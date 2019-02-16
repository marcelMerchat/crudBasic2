<?php
// 'edit.php'
require_once "pdo.php";
require_once "util.php";
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
if ( ! isset($_REQUEST['profile_id']) ) {
    $_SESSION['error'] = "Missing profile_id";
    header('Location: index.php');
    return;
}
$_SESSION['error'] = '';
if ( isset($_GET['profile_id']) && strlen($_GET['profile_id'] > 0)) {
    $_SESSION['profile_id'] = $_GET['profile_id'];
}
$uid = $_SESSION['user_id'];
$profileid = $_SESSION['profile_id'];
//echo " ready 1a to add positions at header . . . ".$_POST['first_name'].$_POST['last_name'].$_POST['email'].$_POST['profession'].$_POST['goal'];
//echo " work info: ".$_POST['wrkStartYr1'].$_POST['org1'].$_POST['wrkFinalYr1'].$_POST['desc1'];
$posCount = get_position_count($pdo);
$_SESSION['posCount'] =  $posCount;
// Get profile from database
$profile = get_profile_information($pdo, $profileid, $uid);
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
// Check for initial GET request without (or without) post information
if (isset($_POST['first_name']) && isset($_POST['last_name']) &&
    isset($_POST['email']) && isset($_POST['profession']) &&
    isset($_POST['goal'])) {
      $_SESSION['success'] = $_SESSION['success'].' post was made . . . ';
    $msg = validateProfile();
    $_SESSION['success'] = $_SESSION['success'].' . . . profile checked  . . . '.$mdg;
    if (is_string($msg) ) {
      $_SESSION['error'] = $msg;
      echo $_SESSION['error'] ;
      header('Location: edit.php?profile_id='.$_REQUEST['profile_id']);
      return;
    }
    $_SESSION['success'] = $_SESSION['success'].' profile passed . . . ';
    $msg = validateSkill();
    if (is_string($msg) ) {
       $_SESSION['error'] = $msg;
       echo $_SESSION['error'] ;
       header('Location: edit.php?profile_id='.$_REQUEST['profile_id']);
       return;
    }
    $_SESSION['success'] = $_SESSION['success'].' skill passed . . . ';
    $msg = validateEducation();
    if (is_string($msg) ) {
       $_SESSION['error'] = $msg.' the value of edu found';
       echo $_SESSION['error'] ;
       header('Location: edit.php?profile_id='.$_REQUEST['profile_id']);
       return;
    }
    $_SESSION['success'] = $_SESSION['success'].' edu passed . . . ';
    echo 'ready to check positions';
    $msg = validatePos();
    echo 'check positions '.$msg;
    //echo $msg;
    if (is_string($msg) ) {
          $_SESSION['error'] = $_SESSION['error'].$msg;
          echo $_SESSION['error'] ;
          header('Location: edit.php?profile_id='.$_REQUEST['profile_id']);
          return;
    }
    $_SESSION['success'] = $_SESSION['success'].' database check . . . ';
    // Get revised posted profile information
    $f_n = $_POST['first_name'];
    $l_n = $_POST['last_name'];
    $e_m = $_POST['email'];
    $p_rof = $_POST['profession'];
    $g_l = $_POST['goal'];
    unset($_SESSION['error']);
    $f_n = trim(filterWord($pdo, $f_n));
    $l_n = trim(filterWord($pdo, $l_n));
    $e_m = trim(filterWord($pdo, $e_m));
    $p_rof = trim(filterWord($pdo, $p_rof));
    $g_l = trim(filterWord($pdo, $g_l));
    if(isset($_SESSION['error']) && $_SESSION['error'] == "offensive"){
        $_SESSION['error'] = $_SESSION['message'].' Word not recognized, please try again.';
        header("Location: edit.php");
        return;
    }
 // Update profiles
    $_SESSION['success'] = $_SESSION['success'].' update basic profile in database . . . ';
    $sql = "UPDATE Profile SET first_name = :fn, last_name = :lnm, email = :em, profession = :prof, goal = :goal WHERE profile_id = :pid ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(':fn' => $_POST['first_name'], ':lnm' => $_POST['last_name'], ':em' => $_POST['email'],
                  ':prof' => $_POST['profession'], ':goal' => $_POST['goal'],  ':pid' => $_GET['profile_id']) );

 // Delete old skill entries; recreate new list
    $stmt = $pdo->prepare('DELETE FROM SkillSet WHERE profile_id = :pid');
    $stmt->execute(array(':pid' => $_REQUEST['profile_id']));
 // Insert new position entries; create replacement list
   //echo "inserting skill set ".$_POST['skill1'].' length is '. strlen($_POST['skill1']);

    insertSkillSet($pdo, $_REQUEST['profile_id']);
    $skillCount = get_skill_count($pdo);
    $_SESSION['skillCount'] =  $skillCount;
    //$_SESSION["success"] = 'Record edited: there are now '.$skillCount.' skills.';

 // Delete old education entries; recreate new list
    $stmt = $pdo->prepare('DELETE FROM Education WHERE profile_id = :pid');
    $stmt->execute(array(':pid' => $_REQUEST['profile_id']));
    insertEducations($pdo, $_REQUEST['profile_id']);
    //$_SESSION['success'] = $_SESSION['success']." Education added";
 // Clear old position entries; recreate new list
    $stmt = $pdo->prepare('DELETE FROM Position WHERE profile_id = :pid');
    $stmt->execute(array(':pid' => $_REQUEST['profile_id']));
 // Insert new position entries; create replacement list

    //$_SESSION['success'] = $_SESSION['success']." ready to add positions . . . ";
    insertPositions($pdo, $_REQUEST['profile_id']);
    //$_SESSION['success'] = $_SESSION['success']." finished adding positions . . . ";
    //echo 'finished with insertions';
    $_SESSION['countPosition'] = get_position_count($pdo);
    //$_SESSION['success'] = $_SESSION['success'].' Record edited: there are now '.$_SESSION['countPosition'].' positions.';
    if(isset($_SESSION['message'])) {
        $_SESSION['error'] = 'Language error: '.$_SESSION['message'];
    }
    header("Location: index.php");
    return;
} else {
  //echo '<p>Initial Get Request: nothing posted yet</p>';
}
?>

<!--            VIEW                -->

<!DOCTYPE html>
<html>
<head>
<title>Edit Profile</title>
<?php
   require_once 'header.php';
   // Get educations and positions from database
   $educations = loadEdu($pdo, $_SESSION['profile_id']);
   $positions = loadPos($pdo, $_SESSION['profile_id']);
   $skills = loadSkill($pdo, $_SESSION['profile_id']);
?>
</head>
<body>
    <div class="content" id="main">
    <h4 class="less-bottom-margin center">Editing profile</h4>
<?php
          flashMessages();
?>
<form method="post">
          <!-- hidden unchangeable information -->
          <input type='hidden' name='profile_id' value='<?= $profileid ?>' >
          <p><input type="hidden" name="user_id" value='<?= $uid ?>' id="userid"></p>
          <!-- Modifiable Information -->
          <div class="container-form-entry">
            <div class="less-bottom-margin less-top-margin box-input-label">First Name
            </div><div class="less-top-margin less-bottom-margin profile-input-form">
              <input class="text-box" type="text" name="first_name" value="<?= $fn ?>" id="fn"/>
            </div>
          </div>
          <div class="container-form-entry">
            <div class="less-bottom-margin less-top-margin box-input-label">Last Name
            </div><div class="less-top-margin less-bottom-margin profile-input-form">
              <input class="text-box" type="text" name="last_name" value="<?= $ln ?>" id="ln"/>
            </div>
          </div>
          <div class="container-form-entry more-top-margin-3x">
            <div class="less-bottom-margin less-top-margin box-input-label">E-mail
            </div><div class="less-top-margin less-bottom-margin profile-input-form">
              <input class="text-box" type="text" name="email" value="<?= $em ?>" id="em">
            </div></div>
          <div class="container-form-entry more-top-margin-3x">
            <div class="less-bottom-margin less-top-margin box-input-label">Profession
            </div><div class="less-top-margin less-bottom-margin profile-input-form">
              <input class="text-box" type="text" name="profession" value="<?= $prof ?>" id="pf">
            </div></div>
          <div class="goal-box-layout less-top-margin">
                <h4 class="small-bottom-pad center"> Goals</h4>
                <textarea class="goal-box" rows="5" name="goal" id="gl" ><?= $gl ?></textarea>
          </div>
          <!-- End of profile information -->
          <!-- Skills -->
<!-- End of goals -->
<!-- Skills -->
<h4 class="more-top-margin-3x less-bottom-margin center">Skills</h4>
<!-- the id addSkill point to a JavaScript function -->
<div class="less-top-margin less-bottom-margin" id="skill_fields">
<?php
  $countSkill = 1;
  foreach($skills as $skill){
           $_SESSION['skill_count'] = $countSkill;
           echo '<div id="skill'.$countSkill.'" >';
           echo '<div class="less-top-margin less-bottom-margin input-form center">
                   <input class="skill ui-autocomplete-custom text-box"
                   name="skill_name'.$countSkill.
                   '" value="'.htmlentities(trim($skill['name'])).'"
                   id="jobskill'.$countSkill.'" >
                  </div>
                   <p class="less-top-margin box-input-label more-bottom-margin center">Delete preceding skill:
                   <input class="click-plus" type="button" value="-"
                          onclick =
                   "$(\'#skill'.$countSkill.'\').remove();
                       removeSkill(countSkill, skillRemoved); return false;" >
                   </p></div>';
          $countSkill++;
    }
?>
<!-- Added html for skills -->
<script id="skill-template" type="text">
    <div id="skill@COUNT@">
    <div class="less-top-margin less-bottom-margin input-form center">
        <input class="skill ui-autocomplete-custom text-box center"
           name="skill_name@COUNT@" value='<?= htmlentities("") ?>' id="jobskill@COUNT@"/>
    </div>
        <p class="less-top-margin box-input-label less-bottom-margin center"> Delete preceeding skill:
        <input type="button" class="click-plus" value="-"
               onclick="$('#skill@COUNT@').remove(); skillRemoved = skillRemoved + 1; alert('Skill removed, skill count decreases to ' + (countSkill - skillRemoved)); return false;" />
        </p>
    </div>
</script>
</div>
<h5 class="less-top-margin center">Add Job Skill: <button class="click-plus less-bottom-margin" id="addSkill">+</button></h5>
<!-- 'addSkill' is an argument of a JavaScript
 function object [ $('#addSkill') ] described below. -->
<!-- end of skills -->
<h4 class="less-bottom-margin center">Education</h4>
<div class="less-top-margin less-bottom-margin centered-row-layout" id="edu_fields">
<?php
$countEdu = 1;
    foreach($educations as $education){
        $_SESSION['education_count'] = $countEdu;
        echo '<div class="form-background div-form-group border-top-bottom more-bottom-margin" id="edu'.$countEdu.'">
                <div class="container-form-entry">
                    <div class="less-bottom-margin short-input-label">Year
                    </div><div class="less-top-margin less-bottom-margin short-input-form">
                        <input class="text-box year-entry-box" type="text" name="edu_year'.$countEdu.'"
                            value="'.$education['year'].'"  id="eduYear'.$countEdu.'">
                    </div>
                </div><div class="container-form-entry">
                    <div class="less-bottom-margin less-top-margin short-input-label">School
                    </div><div class="less-top-margin less-bottom-margin input-form">
                        <input class="school text-box" type="text" \
                            name="edu_school'.$countEdu.'" \
                            value="'.htmlentities(trim($education['name'])).'" id="school'.$countEdu.'">
                    </div>
                </div><div class="container-form-entry">
                    <div class="less-bottom-margin less-top-margin short-input-label">Major
                    </div><div class="less-top-margin less-bottom-margin input-form">
                        <input class="text-box" type="text" name="edu_major'.$countEdu.'" value="'.htmlentities(trim($education['major'])).'" id="edu_major'.$countEdu.'" >
                    </div>
                </div><div>
                    <p>Delete this education <input
                       class="click-plus" type="button" value="-"
                       onclick="$(\'#edu'.$countEdu.'\').remove(); return false;">
                    </p>
                </div>
            </div>';
              $countEdu++;
    }
?>
<script type="text" id="edu-template">
    <div class="form-background div-form-group border-top-bottom more-bottom-margin left" id="edu@COUNT@">
    <div class="container-form-entry">
        <div class="less-bottom-margin short-input-label left">Year</div>
        <div class="less-bottom-margin less-top-margin short-input-form left">
             <input class="year-entry-box" type="text" name="edu_year@COUNT@" id="eduYear@COUNT@" />
        </div>
    </div>
    <div class="container-form-entry less-bottom-margin">
        <div class="less-bottom-margin box input-label less-top-margin short-input-label">School </div>
        <div class="less-bottom-margin less-top-margin input-form">
          <input class="school ui-autocomplete-custom text-box less-bottom-margin" type="text" name="edu_school@COUNT@" value='' id="school@COUNT@" />
        </div>
    </div>
    <div class="container-form-entry less-bottom-margin">
        <div class="less-bottom-margin less-top-margin left short-input-label">Major </div>
        <div class="less-bottom-margin less-top-margin input-form">
          <input class="text-box" type="text" size="80" name="edu_major@COUNT@" value="" id="major@COUNT@" />
        </div>
    </div>
    <div class="less-bottom-margin">
        <p class="less-top-margin less-bottom-margin"> Delete this educational entry:
            <input type="button" class="click-plus" value="-"
                onclick="$('#edu@COUNT@').remove(); removeEdu(countEdu, eduRemoved); return false;"/>
        </p>
    </div>
    </div>
</script>
</div>
<h5 class="less-top-margin center">Add Education: <button class="click-plus less-bottom-margin" id="addEdu" >+</button></h5>
<!-- End of Eduction         -->
<!-- Beginning of Employment -->
<h4 class="more-top-margin less-bottom-margin center">Work History</h4>
<div class="less-top-margin less-bottom-margin" id="position_fields">

<?php
$pos = 1;
foreach($positions as $position){
    echo '<div class="form-background div-form-group border-top-bottom left more-bottom-margin" id="position'.$pos.'">';
    if ($mobile) {
       // echo '<p class="less-bottom-margin">
       //         Starting Year <input class="year-entry-box less-bottom-margin"
       //         type="text" name="yearStart'.$pos.'"
       //         value="'.$position['yearStart'].'">
       //       </p>';
             echo '<div class="container-form-entry">
                   <div class="inline-block">
                      <div class="less-bottom-margin year-input-label">Start Year</div>
                      <div class="less-top-margin less-bottom-margin short-input-form">
                        <input class="year-entry-box" type="text" name="wrkStartYr'.$pos.'"
                               value="'.$position['yearStart'].'" id="workStartYr'.$pos.'">
                      </div>
                  </div>
                  <div class="inline-block">
                      <div class="less-bottom-margin year-input-label">Final Year</div>
                      <div class="less-top-margin less-bottom-margin short-input-form">
                           <div><input class="year-entry-box" type="text" name="wrkFinalYr'.$pos.'"
                                       value="'.$position['yearLast'].'" id="workFinalYr'.$pos.'">
                           </div>
                      </div>
                  </div>
                  </div>';

    } else {
      // <div class="col-sm-6" style="background-color:lavenderblush;">
        echo '<div class="container-fluid">
              <div class="row">
                 <div class="inline-block">
                    <div class="less-bottom-margin year-input-label"> Start Year</div>
                    <div class="short-input-form"><input class="year-entry-box" type="text" name="wrkStartYr'.$pos.'"
                              value="'.$position['yearStart'].'" id="workStartYr'.$pos.'">
                    </div>
                 </div>
                 <div class="inline-block">
                     <div class="less-bottom-margin year-input-label"> Final Year</div>
                     <div class="short-input-form"><input class="year-entry-box" type="text" name="wrkFinalYr'.$pos.'"
                        value="'.$position['yearLast'].'" id="workStartYr'.$pos.'">
                     </div>
                 </div>
                </div>
              </div> ';
    }
    echo  '<div>   <p class="less-bottom-margin">
                  Organization:
              </p><p class="less-top-margin less-bottom-margin">
              <input class="text-box less-top-margin less-bottom-margin" name="org'.$pos.'"
               value="'.htmlentities(trim($position['organization'])).'" id="company'.$pos.'"/>
               </p><p class="less-bottom-margin">Description: </p>
               <textarea class="position-box margin-close less-top-margin less-bottom-margin"
                    name="desc'.$pos.'" rows = "8" id=\"positionDesc'.$pos.'>'
                    .htmlentities(trim($position['description'])).
              '</textarea><p class="less-top-margin">
                Delete this position:
                <input class="click-plus" type="button" value="-"
                  onclick="$(\'#position'.$pos.'\').remove(); removePosition(countPosition, positionRemoved); return false;">
              </p></div>
     </div>';
     $pos++;
}
?>
</div>
<h4 class="less-top-margin center">Add Position <button class="click-plus" id="addPos" >+</button></h4>
<!-- End of Employment -->
<p class="less-bottom-margin small headline-green center"><span class="link-info">Modify database</span>
  <input class="button-submit" type="submit" onclick="return doValidate();" value="Save"/>
  <input class="button-submit" type="submit" name="cancel" value="Cancel" size="40">
</p>
</form>
</div> <!-- main -->
<script>
$(document).ready(function() {
        window.console && console.log('Document ready called ');
        isMobileDevice = Boolean("<?php echo isMobile() ?>" == 1);
        isLargeDevice = !isMobileDevice;
        window.console && console.log('Mobile device = ' + isMobileDevice);

        countPosition = Number("<?php echo $pos      ?>") - 1;
        countEdu =      Number("<?php echo $countEdu ?>") - 1;
        countSkill =   Number("<?php echo $countSkill ?>") - 1;
        skillRemoved =  0;
        eduRemoved = 0;
        positionRemoved =  0;
        $('#addSkill').click(function(event){
            event.preventDefault();
            window.console && console.log("Adding skill");
            //alert('Adding job skill, now there are '+ (countSkill + 1 - skillRemoved) + ' skills.');
            if(countSkill - skillRemoved + 1 > 9) {
                alert('Maximum of nine skills exceeded');
                return;
            } else {
                countSkill = countSkill + 1;
            }
            window.console && console.log("Adding skill-"+countSkill);
            // Fill out the template block within the html code
            var source = $('#skill-template').html();
            $('#skill_fields').append(source.replace(/@COUNT@/g, countSkill));
            alert('Added skill, skill count increases to '+ (countSkill - skillRemoved));
            $(document).on('click', '.skill', 'input[type="text"]', function(){
                var skillId = $(this).attr("id");
                var termskill = document.getElementById(id=skillId).value;
                $.getJSON('skill.php?ter'+'m='+termskill, function(data) {
                     var ys = data;
                     $('.skill').autocomplete({ source: ys });
                });
            });
        });
        $('#addEdu').click(function(event) {
            event.preventDefault();
            alert('Adding education entry, now there are '+ (countEdu + 1 - eduRemoved) + ' entries.');
            if( countEdu - eduRemoved + 1 > 9){
              alert('Maximum of nine education entries exceeded');
              return;
            } else {
              countEdu = countEdu +   1;
            }
            var source = $('#edu-template').html();
            // Creates Div with id of edu1, edu2, ...
            // These divs inherit class from edu-fields div
            $('#edu_fields').append(source.replace(/@COUNT@/g, countEdu));
            alert('Added education, count increases to '+ (countEdu - eduRemoved));
            window.console && console.log("Appending to education");
            //auto-completion handler for new additions
            $(document).on('click', '.school', 'input[type="text"]', function(){
                var schoolId = $(this).attr("id");
                term = document.getElementById(id=schoolId).value;
                window.console && console.log('preparing json for '+term);
                $.getJSON('school.php?ter'+'m='+term, function(data) {
                    var y = data;
                    $('.school').autocomplete({ source: y });
                });
            });
        });  //end of addedu
       $('#addPos').click(function(event){
            event.preventDefault();
            alert('Adding position, now there are '+ (countPosition + 1 - positionRemoved) + ' positions.');
            if( countPosition - positionRemoved + 1 > 5){
                alert('Maximum of five position entries exceeded');
                return;
            } else {
                countPosition = countPosition + 1;
            }
            var positionYrs = 'positionYears'+countPosition;
            // positionsYrs is the target for direct JavaScript DOM insertion
            $('#position_fields').append(
               '<div class="form-background div-form-group  border-top-bottom  more-bottom-margin" id=\"position'+countPosition+'\"> \
                   <div class="container-fluid"> \
                      <div class="row" id='+positionYrs+'> </div> \
                   </div> \
                   <div> \
                     <p class="less-bottom-margin"> \
                        Organization: \
                     </p> <input \
                           class="text-box less-top-margin less-bottom-margin" type="text" name="org'+countPosition+'" id="company'+countPosition+'"/> \
                     <p class="less-bottom-margin"> Description:</p> \
                     <textarea class="position-box" \
                        name="desc'+ countPosition + '" rows = "8" \
                        id=\"positionDesc'+countPosition+'\"> </textarea> \
                     <p>Delete this position: <input type="button" \
                             class="click-plus" value="-" \
                             onclick="$(\'#position'+countPosition+'\').remove(); \
                                removePosition(countPosition, positionRemoved); \
                                return false;"/> \
                     </p> \
                  </div> \
              </div>'
            );
            alert('Added position: Now there are ' + (countPosition - positionRemoved));
            var position = 'positionYears'+countPosition;
            // 'positionYear# is the insertion target above.'
            var insertionId = "positionYears"+countPosition;
            var insertionTag = document.getElementById(insertionId);
            // Inline Package for both years
            // var multiYearWrapper = document.createElement("div");
            // var multiYearWrapperId = "yearWrapper"+countPosition;
            // multiYearWrapper.id = multiYearWrapperId;
            // multiYearWrapper.className = "container-form-entry";

            //var divInstitutionGroup = document.createElement("div");
            //var institutionGroupId = "positionInstitutionGroup"+countPosition;
            //var divMajorGroup = document.createElement("div");
            //var majorGroupId = "positionMajorGroup"+countPosition;

        //  Starting Year

        //  Define container for an input box and its label
            var workStartYearGroup = document.createElement("div");
            var workStartYearGroupId = "workStartYearGroup"+countPosition;
            workStartYearGroup.id = workStartYearGroupId;
        //  1-line or 2-line option for large or small devices
            if(isLargeDevice){
             // large device (1-line)
                //workStartYearGroup.className = "col-sm-6 container-form-entry";
                workStartYearGroup.className = "inline-block";
            } else {
             // small device (2-lines)
                workStartYearGroup.className = "inline-block";
            }
        //  Define container for label
            var StartYearLabelDiv = document.createElement("div");
            var StartYearLabelDivId = "workStartYearLabel"+countPosition;
            StartYearLabelDiv.id = StartYearLabelDivId;

            //StartYearLabelDiv.className = "less-bottom-margin year-input-label left inline-block";
            StartYearLabelDiv.className = "less-bottom-margin year-input-label";
            // Make Label and attach to container for label
            var node = document.createTextNode("Start Year");
            StartYearLabelDiv.appendChild(node);
            workStartYearGroup.appendChild(StartYearLabelDiv);
        //  Define container for input box
            var workStartYearInputDiv = document.createElement("div");
            var workStartYearInputDivId = "workStartYear"+countPosition;
            workStartYearInputDiv.id = workStartYearInputDivId;
            workStartYearInputDiv.className = "less-bottom-margin less-top-margin short-input-form";
       //   Make input box and attach it to its container
            var workStartYearInput = document.createElement("input");
       //   This is the tag id for the form year
            var workStartYearId = "wrkStartYr"+countPosition;
            var workStartYearName = "wrkStartYr"+countPosition;
            workStartYearInput.className = "year-entry-box";
            workStartYearInput.name = workStartYearName;
            workStartYearInput.id = workStartYearId;
            workStartYearInputDiv.appendChild(workStartYearInput);
            workStartYearGroup.appendChild(workStartYearInputDiv);
        //  Insert group directly into DOM
            insertionTag.appendChild(workStartYearGroup);

        //  Final Year
            var workFinalYearGroup = document.createElement("div");
            var workFinalYearGroupId = "workFinalYearGroup"+countPosition;
            workFinalYearGroup.id = workFinalYearGroupId;
            workFinalYearGroup.name = workFinalYearGroupId;
            if(isLargeDevice){
                //workFinalYearGroup.className = "container-form-entry";
                workFinalYearGroup.className = "short-input-form inline-block";
            } else {
             // small device year-form-input
                workFinalYearGroup.className = "short-input-form inline-block";
            }
            var FinalYearLabelDiv = document.createElement("div");
       //   This is the tag id for the final form year
            var FinalYearLabelDivId = "workFinalYearLabel"+countPosition;
            FinalYearLabelDiv.id = FinalYearLabelDivId;
            FinalYearLabelDiv.className = "less-bottom-margin year-input-label";
            //FinalYearLabelDiv.className = "less-bottom-margin year-input-label";

            var node = document.createTextNode("Final Year");
            FinalYearLabelDiv.appendChild(node);
            workFinalYearGroup.appendChild(FinalYearLabelDiv);

            var workFinalYearInputDiv = document.createElement("div");
            var workFinalYearInputDivId = "workFinalYear"+countPosition;
            workFinalYearInputDiv.id = workFinalYearInputDivId;
            //workFinalYearInputDiv.className = "less-bottom-margin less-top-margin short-input-form inline-block";
            workFinalYearInputDiv.className = "less-bottom-margin less-top-margin short-input-form";

            var workFinalYearInput = document.createElement("input");
            var workFinalYearId = "wrkFinalYr"+countPosition;
            var workFinalYearName = "wrkFinalYr"+countPosition;
            workFinalYearInput.className = "year-entry-box";
            workFinalYearInput.name = workFinalYearName;
            workFinalYearInput.id = workFinalYearId;

            workFinalYearInputDiv.appendChild(workFinalYearInput);
            workFinalYearGroup.appendChild(workFinalYearInputDiv);
            if(isMobileDevice){
               insertionTag.appendChild(workFinalYearGroup);
               //  Insert 1-line group directly into DOM
            }  else {
                insertionTag.appendChild(workFinalYearGroup);
               //multiYearWrapper.appendChild(workStartYearGroup);
               //multiYearWrapper.appendChild(workFinalYearGroup);
               //insertionTag.appendChild(multiYearWrapper);
            }
            // $('#'+positionYrs).append("HERE");
            window.console && console.log("Adding position "+countPosition);
        });
         //  Add entire year group
        //  <div class="container-form-entry"> \
      //   <div class="year-form-entry"> \
     //         <div class="less-bottom-margin less-top-margin year-input-label"> Starting Year</div> \
     //         <div class="less-top-margin less-bottom-margin short-input-form"> \
     //             <input class="year-entry-box" type="text" name="yearStart' + countPosition + '"' + ' id="ys'+ countPosition + '\" /> \
     //         </div> \
     //     </div> \
     //     <div class="year-form-entry"> \
     //         <div class="less-bottom-margin less-top-margin short-input-label ident"> Final Year \
     //         </div><div class="less-top-margin less-bottom-margin year-entry-box"> \
     //             <input class="year-entry-box" type="text" name="yearStart' + countPosition + '"' + ' id="yl'+ countPosition + '\" /> \
     //         </div> \
     //     </div> \
     // </div> \

        $(document).on('click', '.text-box', 'input[type="text"]', function(){
              var p = $(this);
              var tagId = $(this).attr("id");
              var textinfo = document.getElementById(id=tagId).value;
              window.console && console.log(tagId);
              window.console && console.log('info is '+textinfo);
              var len = textinfo.length;
              if( len > 0){
                  window.console && console.log('trying . . .'+ textinfo);
                  $.getJSON('jsonLanguage.php?ter'+'m='+textinfo, function(data) {
                  window.console && console.log('inside getJSON');
                  window.console && console.log(data.first);
                  if(data.first=='bad'){
                      var info = p.val();
                      p.val(info+': Questionable language detected . . . ');
                      p.css("background-color", "bisque");
                      p.css("borderWidth", "2px");
                      p.css("border-color", "#986600");
                  } else {
                    p.css("background-color", 'rgb(249, 255, 185)');
                    p.css("borderWidth", "1px");
                    p.css("border-color", 'rgb(88,66,00)');
                  }
                });
            }      //$('#gl').html.append("data dot first");
        });
        $(document).on('click', '.goal-box', 'input[type="text"]', function(){
              var p = $(this);
              var goalId = $(this).attr("id");
              termgl = document.getElementById(id=goalId).value;
              window.console && console.log(termgl+goalId);
              var len = termgl.length;
              if( len > 0){
              //    $.getJSON('school.php?ter'+'m='+termgl, function(data) {
              $.getJSON('jsonLanguage.php?ter'+'m='+termgl, function(data) {
                  window.console && console.log(data.first);
                  //var p = $('#gl');
                  //var p = $('#goalId');
                  //$('.goal-box').val("json back: " + data.first);
                  if(data.first=='bad'){
                      //$('.goal-box').val(data.first);
                      //var info = $('.goal-box').val();
                      //var info = $('#goalId').val();
                      var info = p.val();
                      //$('#goalId').val(info+': Questionable language detected . . . ');
                      p.val(info+': Questionable language detected . . . ');
                      //$('.goal-box').val(info+': Questionable language detected . . . '); //append(field + " ")
                      //$('#gl').val(info+': Questionable language detected . . . '); //append(field + " ")
                      //p.hide(500).show(500);
                      //p.queue(function() {p.css("background-color", "#EEAAAA");});
                      p.css("background-color", "bisque");
                      p.css("borderWidth", "2px");
                      p.css("border-color", "#00EEDD");
                  } else {
                    //p.css("background-color", 'rgb(225, 250, 250)'); //blue-green
                    p.css("background-color", 'rgb(249, 255, 185)');
                    p.css("borderWidth", "1px");
                    p.css("border-color", 'rgb(88,66,00)');
                    //border: 1px solid #008800;
                    //background-color: #eef;
                  }
                });
            }      //$('#gl').html.append("data dot first");
        });
        $(document).on('click', '.position-box', 'input[type="text"]', function(){
             var p = $(this);
             var posId = $(this).attr("id");
             termPos = document.getElementById(id=posId).value;
             window.console && console.log(termPos+posId);
             var len = termPos.length;
             if( len > 0){
              //    $.getJSON('school.php?ter'+'m='+termgl, function(data) {
             $.getJSON('jsonLanguage.php?ter'+'m='+termPos, function(data) {
                  window.console && console.log(data.first);
                  //var p = $('#gl');
                  //var p = $('#goalId');
                  //$('.goal-box').val("json back: " + data.first);
                  if(data.first=='bad'){
                      //$('.goal-box').val(data.first);
                      //var info = $('.goal-box').val();
                      //var info = $('#goalId').val();
                      var info = p.val();
                      //$('#goalId').val(info+': Questionable language detected . . . ');
                      p.val(info+': Questionable language detected . . . ');
                      //$('.goal-box').val(info+': Questionable language detected . . . '); //append(field + " ")
                      //$('#gl').val(info+': Questionable language detected . . . '); //append(field + " ")
                      //p.hide(500).show(500);
                      //p.queue(function() {p.css("background-color", "#EEAAAA");});
                      p.css("background-color", "bisque");
                      p.css("borderWidth", "2px");
                      p.css("border-color", "#BB8800");
                  } else {
                    //p.css("background-color", 'rgb(225, 250, 250)'); //blue-green
                    p.css("background-color", 'rgb(249, 255, 185)');
                    p.css("borderWidth", "1px");
                    p.css("border-color", 'rgb(88,66,00)');
                    //border: 1px solid #008800;
                    //background-color: #eef;
                  }
                });
            }      //$('#gl').html.append("data dot first");
        });
     });
</script>
</body>
</html>
