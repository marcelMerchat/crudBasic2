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
// if ( ! isset($_GET['profile_id']) ) {
//
// }
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
//$profileid = $_SESSION['profile_id'];
//$posCount = get_position_count($profileid,$pdo);
//$_SESSION['posCount'] =  $posCount;
// Get profile from database
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
// Check for initial GET request without (or without) post information
// Only required entries are the first and laat names and the email.
if (isset($_POST['first_name']) && isset($_POST['last_name']) &&
    isset($_POST['email']) ) {

    unset($_SESSION['error']);
    unset($_SESSION['success']);
    unset($_SESSION['message']);
    $profileid = $_SESSION['profile_id'];
 // check name
    $valid_name = validateName($pdo);
 // check email
    $valid_email = validateEmail($pdo);
    if (!$valid_email || !$valid_name) {
      $_SESSION['error'] = 'Could not change name or email. Please try again. ';
      header('Location: edit.php?profile_id='.$_POST['profile_id']);
      return;
    } else {
      $profileInserted = insertProfile($pdo,$IsUpdate=true);
      if($profileInserted===false){
          $_SESSION['error'] = 'Could not change name or email. Please try again. ';
          header('Location: edit.php?profile_id='.$_POST['profile_id']);
          return;
      }
    }
    // Check Professional Goals
    // Profession might be vaild but have a deleted word.
    // This will always be an UPDATE procedure because profession and goals
    // are fields in the Profile table.
    $professionAdded = insertProfession($profileid, $pdo, $IsUpdate=true);
    $goalsAdded = insertProfessionalGoals($profileid, $pdo, $IsUpdate=true);
//  Skills
    // Delete old skill entries; insert new list
    $stmt = $pdo->prepare('DELETE FROM SkillSet WHERE profile_id = :pid');
    $stmt->execute(array(':pid' => $profileid));
    $skillCount = 0;
    $skillAdded = insertSkillSet($profileid,$pdo);
    $skillCount = get_skill_count($profileid,$pdo);
    $_SESSION['skillCount'] =  $skillCount;
 // Education
 // Delete old education entries; recreate new list
    $stmt = $pdo->prepare('DELETE FROM Education WHERE profile_id = :pid');
    $stmt->execute(array(':pid' => $profileid));
    insertEducations($profileid,$pdo);
 // Positions
    // Clear old position entries; recreate new list
    $stmt = $pdo->prepare('DELETE FROM Position WHERE profile_id = :pid');
    $stmt->execute(array(':pid' => $profileid));
 // Insert new position entries; create replacement list
    insertPositions($profileid, $pdo);
    $_SESSION['countPosition'] = get_position_count($profileid,$pdo);
    //$_SESSION['success'] = $_SESSION['success'].' Record edited: there are now '.$_SESSION['countPosition'].' positions.';
    header("Location: index.php");
    return;
} else {
  //$_SESSION['message'] = ' Initial Get Request: nothing posted yet. ';
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
   $educations = loadEdu($profileid,$pdo);
   $positions = loadPos($profileid,$pdo);
   $skills = loadSkill($profileid,$pdo);
?>
</head>
<body>
    <div class="content" id="main">
    <h4 class="less-bottom-margin center">Editing profile</h4>
<?php
          flashMessages();
?>
<form method="post" name="form1">
          <!-- hidden unchangeable information -->
          <input type="hidden" name="profile_id" value="<?= $profileid ?>" >
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
                <textarea class="paragraph-box" rows="5" name="goal" id="gl" ><?= $gl ?></textarea>
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
               <textarea class="paragraph-box margin-close less-top-margin less-bottom-margin"
                    name="desc'.$pos.'" rows = "8" id="positionDesc'.$pos.'">'
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
        lastTextBox =  'fn';
        // When a new skill is added, the immediate previous skill is checked
        // for offensive language.
        $('#addSkill').click(function(event){
            event.preventDefault();
            window.console && console.log("Adding skill");
            //alert('Adding job skill, now there are '+ (countSkill + 1 - skillRemoved) + ' skills.');
            if(countSkill - skillRemoved + 1 > 20) {
                alert('Maximum of twenty skills exceeded');
                return;
            } else {
                countSkill = countSkill + 1;
            }
            window.console && console.log("Adding skill-"+countSkill);
            // Fill out the template block within the html code
            var source = $('#skill-template').html();
            $('#skill_fields').append(source.replace(/@COUNT@/g, countSkill));
            //alert('Added skill, skill count increases to '+ (countSkill - skillRemoved));
            $(document).on('click', '.skill', 'input[type="text"]', function(){
                var skillId = $(this).attr("id");
                var termskill = document.getElementById(id=skillId).value;
                $.getJSON('skill.php?ter'+'m='+termskill, function(data) {
                     var ys = data;
                     $('.skill').autocomplete({ source: ys });
                });
            });
        });
        // when education is added, the immediate previous education
        // is checked for offensive language.
        $('#addEdu').click(function(event) {
            event.preventDefault();
            //alert('Adding education entry, now there are '+ (countEdu + 1 - eduRemoved) + ' entries.');
            if( countEdu - eduRemoved + 1 > 9){
              alert('Maximum of nine education entries exceeded');
              return;
            } else {
              countEdu = countEdu + 1;
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
            //alert('Adding position, now there are '+ (countPosition + 1 - positionRemoved) + ' positions.');
            if( countPosition - positionRemoved + 1 > 5){
                alert('Maximum of five position entries exceeded');
                return;
            } else {
                countPosition = countPosition + 1;
            }
            var positionYrs = 'positionYears'+countPosition;
            // positionsYrs is the target for direct JavaScript DOM insertion
            $('#position_fields').append(
               '<div class="form-background div-form-group  border-top-bottom  more-bottom-margin" id="position'+countPosition+'\"> \
                   <div class="container-fluid"> \
                      <div class="row" id='+positionYrs+'> </div> \
                   </div> \
                   <div> \
                     <p class="less-bottom-margin"> \
                        Organization: \
                     </p> <input \
                           class="text-box less-top-margin less-bottom-margin" type="text" name="org'+countPosition+'" id="company'+countPosition+'"/> \
                     <p class="less-bottom-margin"> Description:</p> \
                     <textarea class="paragraph-box" \
                        name="desc'+ countPosition + '" rows = "8" \
                        id="positionDesc'+countPosition+'"> </textarea> \
                     <p>Delete this position: <input type="button" \
                             class="click-plus" value="-" \
                             onclick="$(\'#position'+countPosition+'\').remove(); \
                                removePosition(countPosition, positionRemoved); \
                                return false;"/> \
                     </p> \
                  </div> \
              </div>'
            );
            //alert('Added position: Now there are ' + (countPosition - positionRemoved));
            var position = 'positionYears'+countPosition;
            // 'positionYear# is the insertion target above.'
            var insertionId = "positionYears"+countPosition;
            var insertionTag = document.getElementById(insertionId);
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

            var node = document.createTextNode("Final Year");
            FinalYearLabelDiv.appendChild(node);
            workFinalYearGroup.appendChild(FinalYearLabelDiv);

            var workFinalYearInputDiv = document.createElement("div");
            var workFinalYearInputDivId = "workFinalYear"+countPosition;
            workFinalYearInputDiv.id = workFinalYearInputDivId;
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
            }
            window.console && console.log("Adding position "+countPosition);
    });
        $(document).on('click', '.text-box', 'input[type="text"]', function(){
              var p = $(this);
              var tagId = $(this).attr("id");
            checkLanguage(tagId);
            //lastTextBox = tagId;
        });
        $(document).on('click', '.paragraph-box', 'input[type="text"]', function(){
              var p = $(this);
              var tagId = $(this).attr("id");
              window.console && console.log(lastTextBox);
              checkLanguage(tagId);
    });
});
</script>
</body>
</html>
