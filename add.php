<?php
require_once('pdo.php');
require_once('util.php');
session_start();
// user is not logged-in
if ( ! isset($_SESSION['user_id']))  {
      header('Location: index.php');
      die('ACCESS DENIED');
}
// If the user requested cancel, go back to view.php
if ( isset($_POST['cancel']) ) {
    $msg = 'taking shortcut';
    header('Location: index.php');
    return;
}
// The profile does not exist yet; the profile id will be added below.
// The logged-in user has submitted his profile.
// To create a profile only the first and last name and a valid email
// are required.
if (isset($_POST['first_name']) || isset($_POST['last_name']) ||
    isset($_POST['email']) )
    {
      if
       (
         (strlen($_POST['first_name']) >= 1) &&
         (strlen($_POST['last_name']) >= 1) &&
         (strlen($_POST['email']) >= 1)
       )
       {
         unset($_SESSION['error']);
         unset($_SESSION['success']);
         unset($_SESSION['message']);
  //     Save entered data
         $fn = $_POST['first_name'];
         $ln = $_POST['last_name'];
         $em = $_POST['email'];
         $prof = $_POST['profession'];
         $goal = $_POST['goal'];
         $fn = filterPhrase($fn,$pdo);
         $ln = filterPhrase($ln,$pdo);
      // check name
         $valid_name = validateName($pdo);
      // check email
         $valid_email = validateEmail($pdo);
         if (!$valid_email || !$valid_name)
          {
            $_SESSION['error'] = "Name and email are required to begin.";
            header('Location: index.php');
            return;
          } else {
            $profileAdded = insertProfile($pdo,$IsUpdate=false);
            if($profileadded===false){
                $_SESSION['error'] = 'Could not save name or email. Please try again. ';
                header('Location: add.php');
                return;
            }
          //$profileid = $pdo->lastInsertId();
          //$_SESSION['profile_id'] = $profileid;
          //$emailAdded = insertEmail($profileid,$pdo);
          }
          // When IsUpdate is false, Session['profile_id'] is assigned.
          $profileid = $_SESSION['profile_id'];
  //  Check Professional Goals
  //  Profession might be vaild but have a deleted word.
  //$em = filterPhrase($em,$pdo);
          $prof = filterPhrase($prof,$pdo);
          $goal = filterPhrase($goal,$pdo);
          $professionAdded = insertProfession($profileid, $pdo, $IsUpdate=false);
          $goalsAdded = insertProfessionalGoals($profileid, $pdo, $IsUpdate=false);
  //  Skills
          $skillCount = 0;
          $skillAdded = insertSkillSet($profileid,$pdo);
          $skillCount = get_skill_count($profileid,$pdo);
          $_SESSION['skillCount'] =  $skillCount;
  //  Education
          insertEducations($profileid,$pdo);
  //  Positions
          insertPositions($profileid, $pdo);
          $_SESSION['countPosition'] = get_position_count($profileid,$pdo);
//    $sql = "INSERT INTO Profile (user_id, first_name, last_name, email, profession, goal) VALUES ( :uid, :fn, :ln, :em, :prof, :goal )";
//    $stmt = $pdo->prepare($sql);
//    $stmt->execute(array(
//          ':uid' => $_SESSION['user_id'], ':fn' => $fn,
//          ':ln'  => $ln,  ':em' => $_POST['email'],
//          ':prof'  => $_POST['profession'], ':goal' => $_POST['goal']) );
          header("Location: index.php");
          return;
          //printf("falling through");
      } else {
          $_SESSION['error'] = 'Profile was incomplete, first and last names, and email are required:';
      }
} else {
  // Either name or email is not posted, so fall through
}
?>
<!--  VIEW or HTML code for model-view-controller  -->
<!DOCTYPE HTML>
<html>
<head>
<title>Add Profile</title>
<?php
    require_once 'header.php';
?>
</head>
<body>
<div class="content" id="main">
<?php
   flashMessages();
?>
<h3 class="center">Adding Profile</h3>
<form method="post" name="form1">
        <div class="container-form-entry"> <!-- column container of one column -->
          <div class="less-bottom-margin less-top-margin box-input-label">First Name:
          </div><div class="less-top-margin less-bottom-margin profile-input-form">
            <input class="text-box" type="text" name="first_name" value='<?= htmlentities("") ?>' id="fn"/>
          </div>
        </div><div class="container-form-entry more-top-margin-3x">
          <div class="less-bottom-margin less-top-margin box-input-label">Last Name:
          </div><div class="less-top-margin less-bottom-margin profile-input-form">
             <input class="text-box" type="text" name="last_name" value='<?= htmlentities("") ?>' id="ln"/>
          </div></div>
        <div class="container-form-entry more-top-margin-3x">
          <div class="less-bottom-margin less-top-margin box-input-label">E-mail:
          </div><div class="less-top-margin less-bottom-margin profile-input-form">
            <input class="text-box" type="text" name="email" value='<?= htmlentities("") ?>' id="em">
          </div></div>
       <div class="container-form-entry more-top-margin-3x">
         <div class="less-bottom-margin less-top-margin box-input-label">Profession:
         </div><div class="less-top-margin less-bottom-margin profile-input-form">
           <input class="text-box" type="text" name="profession" value='<?= htmlentities("") ?>' id="pf"/>
         </div></div>
    <div class="goal-box-layout less-top-margin">
          <div class="center">
               <h4 class="less-bottom-margin center">Goals</h4>
          </div>
          <textarea class="paragraph-box" rows="5" name="goal" id="gl"><?= htmlentities("") ?></textarea>
    </div>
    <!-- End of profile information -->
    <!-- Skills -->
    <h3 class="more-top-margin-3x center">Skills</h3>
    <div class="less-bottom-margin" id="skill_fields">
      <script id="skill-template" type="text">
              <div id="skill@COUNT@">
                  <input class="text-box max-entry-box-width" name="skill_name@COUNT@" id="jobskill@COUNT@"/>
                  <p class="less-top-margin box-input-label less-bottom-margin"> Delete preceeding skill:
                  <input type="button" class="click-plus" value="-" onclick="$('#skill@COUNT@').remove(); countSkill--;return false;"/></p>
              </div>
      </script>
    </div>
        <h4 class="small-bottom-pad center">Add skill <button class="click-plus"
                          id="addSkill">+</button></h4>
        <!-- End of skills -->
        <!-- Education -->
        <h3 class="more-top-margin-2x center">Education</h3>
        <div class="less-top-margin less-bottom-margin centered-row-layout" id="edu_fields">
        <!-- Inject HTML into hot spot and insert in the DOM
             'edu-template' is the target id of the JavaScript function
              function object $('#addEdu') described at the end of the body. -->
        <script id="edu-template" type="text">
        <div class="form-background div-form-group border-top-bottom more-bottom-margin" id="edu@COUNT@">
            <div class="container-form-entry">
                <div class="less-bottom-margin short-input-label"> Year </div>
                <div class="less-bottom-margin less-top-margin short-input-form">
                     <input class="year-entry-box" type="text" name="edu_year@COUNT@" id="eduYearID@COUNT@" />
                </div>
            </div>
            <div class="container-form-entry less-bottom-margin">
                <div class="less-bottom-margin box less-top-margin short-input-label"> School </div>
                <div class="less-bottom-margin less-top-margin input-form">
                  <input class="school text-box less-bottom-margin" type="text" size="80" name="edu_school@COUNT@" id="school@COUNT@" />
                </div>
            </div>
            <div class="container-form-entry less-bottom-margin">
                <div class="less-bottom-margin less-top-margin left short-input-label"> Major </div>
                <div class="less-bottom-margin less-top-margin input-form">
                  <input class="text-box" type="text" name="edu_major@COUNT@" value="" id="major@COUNT@" />
                </div>
            </div>
            <div class="less-bottom-margin">
                <p class="less-top-margin less-bottom-margin more-left-margin"> Delete this educational entry:
                    <input type="button" class="click-plus" value="-"
                        onclick="$('#edu@COUNT@').remove(); removeEdu(countEdu, eduRemoved); return false;"/>
                </p>
            </div>
       </script>
       </div>
       <h4 class="small-bottom-pad more-top-margin-3x center">Add Education: <button class="click-plus" id="addEdu" >+</button></h4>
    <!-- End of education -->
    <!-- Positions -->
    <h3 class="more-top-margin-3x center">Work Experience</h3>
    <!-- The ids for these profile information fields are used by
     JavaScript function doValidate() in file script.js to check if
     they were completed before posting to the website server where
     they are rechecked using a PHP rountine in util.php. -->
    <div class="less-top-margin less-bottom-margin center" id="position_fields"> </div>
    <h4 class="small-bottom-pad more-top-margin-3x center">Add Position: <button class="click-plus" id="addPos" >+</button></h4>
    <h4 class="more-top-margin-3x center">Save to database:
        <input class="button-submit big" type="submit" onclick="return doValidate();" value="Add"/> <input
               class="button-submit big" type="submit" name="cancel" value="Cancel">
        <!-- doValidate() is a JavaScript function (see script.js) for preliminary validation
            which runs before the post to the website. The server program
            at the website (see util.php) performs a final validation check.-->
    </h4>
    <input type="hidden" name="user_id" value=$_SESSION['user_id']>
</form> <!-- end of class form -->
</div> <!-- end of class content and id main -->
<script type="text/javascript">
$(document).ready(function() {
     window.console && console.log('Document ready called ');
     isMobileDevice = Boolean("<?php echo isMobile() ?>" == 1);
     isLargeDevice = !isMobileDevice;
     window.console && console.log('Mobile device = ' + isMobileDevice);
     countPosition = 0;
     countEdu =  0;
     countSkill = 0;
     skillRemoved =  0;
     eduRemoved = 0;
     positionRemoved =  0;
     lastTextBox =  'fn';
     // When a new skill is added, the immediate previous skill is checked
     // for offensive language.
     $(document).on('click', '.text-box', 'input[type="text"]', function(){
               var p = $(this);
               var tagId = p.attr("id");
               checkLanguage(tagId);
     });
     $(document).on('click', '.paragraph-box', 'input[type="text"]', function(){
               //var p = $(this);
               var tagId = $(this).attr("id");
               window.console && console.log(lastTextBox);
               checkLanguage(tagId);
     });
     $('#addSkill').click(function(event){
         event.preventDefault();
         window.console && console.log("Adding skill");
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
             //alert('Added education, count increases to '+ (countEdu - eduRemoved));
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
                 workStartYearGroup.className = "inline-block";
              } else {
                  // small device (2-lines)
                 workStartYearGroup.className = "inline-block";
              }
             //  Define container for label
              var StartYearLabelDiv = document.createElement("div");
              var StartYearLabelDivId = "workStartYearLabel"+countPosition;
              StartYearLabelDiv.id = StartYearLabelDivId;
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
     });
     </script>
     </body>
     </html>
