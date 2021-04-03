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
// If the user requested cancel, go back to index.php
if ( isset($_POST['cancel']) ) {
    header('Location: edit.php?profile_id='.$profileid);
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
    //$stmt = $pdo->prepare('DELETE FROM SkillSet WHERE profile_id = :pid');
    //$stmt->execute(array(':pid' => $profileid));
    //insertSkillSet($profileid,$pdo);
//  Hobbies and Interests
    // Delete old hobbies; insert new list
    //$stmt = $pdo->prepare('DELETE FROM HobbyList WHERE profile_id = :pid');
    //$stmt->execute(array(':pid' => $profileid));
    //insertHobbyList($profileid,$pdo);

    //$stmt = $pdo->prepare('DELETE FROM Personal WHERE profile_id = :pid');
    //$stmt->execute(array(':pid' => $profileid));
    //insertPersonal($profileid,$pdo);

//  Education
    // Delete old education entries; recreate new list
    $stmt = $pdo->prepare('DELETE FROM Education WHERE profile_id = :pid');
    $stmt->execute(array(':pid' => $profileid));
    insertEducations($profileid,$pdo);

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
    header('Location: edit.php?profile_id='.$profileid);
    return;
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
   $positions = loadPos($profileid,$pdo);
   //$hobbies= loadHobbies($profileid,$pdo);
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
    <h3 class="less-bottom-margin center">Editing profile</h3>

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
          //echo $profileid;
          $gradchecked = false;
          $independentchecked = false;
          $employedchecked = false;
          if ($resume_style=='student'){
              $gradchecked = 'checked';
          }
          if ($resume_style=='independent'){
              $independentchecked = 'checked';
          }
          if ($resume_style=='employed'){
              $employedchecked = 'checked';
          }
?>
<p class="more-top-margin-1x less-bottom-margin center">When finished, click the 'Save'
    button at the bottom to save your work.</p>
<form method="post" name="form1">
          <!-- hidden unchangeable information -->
          <input type="hidden" name="profile_id" value ="<?= $profileid ?>" />
          <p><input type="hidden" name="user_id" value ="<?= $uid ?>" id="userid"/></p>
          <!-- Modifiable Information -->
          <!-- Select Resume Style -->
          <h4 class="more-top-margin-2x less-bottom-margin center">Resume Style</h4>
          <p class="more-top-margin-1x less-bottom-margin center">Please select a resume style:</p>
          <div class="radio more-top-margin-2x">

          <label class="container-radio more-top-margin-2x"  for="student">New Grad
             <input type="radio" <?= $gradchecked ?> id="student" name="resume_type" value="student">
             <span class="checkmark"></span>
          </label><br>
          <label class="container-radio more-top-margin-2x" for="experienced">Employed
             <input type="radio"  <?= $employedchecked ?>  id="experienced" name="resume_type" value="employed">
             <span class="checkmark"></span></label><br>
          <label class="container-radio more-top-margin-2x" for="independent">Independent
             <input type="radio" <?= $independentchecked ?> id="independent" name="resume_type" value="independent">
             <span class="checkmark"></span>
          </label>
          </div>
          <p class="more-top-margin-1x less-bottom-margin left" style="padding-left : 7rem;">For the new grad style, education is listed first. For the experienced style, the work history comes first. For independent, skills are first.</p>
          <!-- Basic Information -->
          <h4 class="more-top-margin-2x center">Basic Information</h4>
          <div class="container-form-entry more-top-margin-1x">
            <div class="less-bottom-margin box-input-label">First Name
            </div><div class="less-top-margin less-bottom-margin profile-input-form">
              <input class="text-box" type="text" name="first_name" value="<?= $fn ?>" id="fn"/>
            </div>
          </div>
          <div class="container-form-entry more-top-margin-1x">
            <div class="less-bottom-margin box-input-label">Last Name
            </div><div class="less-bottom-margin less-top-margin profile-input-form">
              <input class="text-box" type="text" name="last_name" value="<?= $ln ?>" id="ln"/>
            </div>
          </div>
          <div class="container-form-entry more-top-margin-1x">
            <div class="less-bottom-margin less-top-margin box-input-label">E-mail
            </div><div class="less-top-margin less-bottom-margin profile-input-form">
              <input class="text-box" type="text" name="email" value="<?= $em ?>" id="em">
            </div></div>
          <div class="container-form-entry more-top-margin-1x">
            <div class="less-bottom-margin less-top-margin box-input-label">Profession
            </div><div class="less-top-margin less-bottom-margin profile-input-form">
              <input class="text-box" type="text" name="profession" value="<?= $prof ?>" id="pf">
            </div></div>
          <div class="goal-box-layout more-top-margin-2x">
                <h5 class="small-bottom-pad center"> Goals</h5>
                <textarea class="paragraph-box" rows="5" name="goal" id="gl" ><?= $gl ?></textarea>
          </div>
          <!-- End of goals -->
<!-- End of profile information -->
<!-- Education -->
<h4 class="more-top-margin-3x center">Education</h4>
<div class="less-top-margin less-bottom-margin centered-row-layout" id="edu_fields">
<?php
$count_edu = 0;
    foreach($educations as $education){
        //print_r($education);
        $count_edu++;
        echo '<div class="form-background div-form-group border-top-bottom
                 more-bottom-margin more-top-margin-3x" id="edu'.$count_edu.'">
                <div class="container-form-entry">
                    <div class="left div-year-group">Year
                     <input class="year-entry-box"
                     type="number" min=1940 max=2100
                     name="edu_year'.$count_edu.'"
                    value="'.$education['year'].'"
                       id="edu_year'.$count_edu.'" />
                    </div>
                </div><div class="container-form-entry">
                    <div class="less-bottom-margin less-top-margin
                                                      short-input-label">School
                    </div><div class="less-top-margin less-bottom-margin
                                                                 input-form">
                        <input class="school ui-autocomplete-custom text-box-long" type="text"
                            name="edu_school'.$count_edu.'" value="'
                            .htmlentities(trim($education['institution']))
                            .'" id="school'.$count_edu.'">
                    </div>
                </div><div class="container-form-entry">
                    <div class="less-bottom-margin less-top-margin">
                      <p class="margin-bottom-small">Degree or Certificate</p>
                      <p class="margin-top-small margin-bottom-small small">
                           Examples: Master\'s Degree in Music,
                                     Certificate in Auto Mechanics
                      </p>
                    </div><div class="less-top-margin less-bottom-margin input-form">
                        <input class="award text-box-long" type="text"
                                name="edu_award'.$count_edu.
                        '" value="'.htmlentities(trim($education['degree']))
                        .'" id="award'.$count_edu.'">
                    </div>
                </div><div>
                    <p>Delete this education <input
                       class="click-plus" type="button" value="-"
                        onclick =
 "deleteEdu(\'#edu'.$count_edu.'\',\'school'.$count_edu.'\',\'award'.$count_edu.'\'); return false;" />
                    </p>
                </div>
            </div>';
    }
?>
<!-- Inject HTML into hot spot and insert in the DOM
     'edu-template' is the target id of the JavaScript function
      function object $('#addEdu') described at the end of the body.

onchange="markVisited('award@COUNT@');"
onchange= "this.className=(this.value=='')?'':'activity ui-autocomplete-custom text-box-long visited-long max-entry-box-width';"
-->
<script type="text" id="edu-template">
<div class="form-background more-top-margin-2x border-top-bottom" id="edu@COUNT@">
    <div class="div-year-group more-bottom-margin-2x left">
                 Year <input class="year-entry-box" type="number"
                 min="1940" max="2100" name="edu_year@COUNT@"
                 id="edu_year@COUNT@" />
    </div>
    <div class="container-form-entry less-bottom-margin">
            <div class="less-bottom-margin box less-top-margin short-input-label center"> School </div>
            <div class="less-bottom-margin input-form zero-top-margin">
              <input class="school ui-autocomplete-custom text-box-long
                                     zero-top-margin"
                    type="text" name="edu_school@COUNT@"
                    id="school@COUNT@" />
            </div>
    </div>
    <div class="container-form-entry less-bottom-margin">
         <div class="less-bottom-margin less-top-margin left">
           <p class="margin-bottom-small center">Degree or Certificate</p>
           <p class="margin-top-small margin-bottom-small small center">
           Examples: Master&#8217;s Degree in Music,
                     Certificate in Auto Mechanics
            </p>
          </div>
          <div class="less-bottom-margin less-top-margin input-form zero-top-margin">
              <input class="award ui-autocomplete-custom text-box-long zero-top-margin"
                      type="text" name="edu_award@COUNT@"
                      value=""
                      id="award@COUNT@" />
          </div>
      </div>
      <div class="less-bottom-margin">
            <p class="less-top-margin less-bottom-margin more-left-margin"> Delete this educational entry:
                <input type="button" class="click-plus" value="-"
                    onclick="deleteEdu('#edu@COUNT@','school@COUNT@','award@COUNT@'); return false;"/>
            </p>
      </div>
</div>
</script>
</div>
<h5 class="more-top-margin-3x center">Add Education: <button class="click-plus less-bottom-margin button-small" id="addEdu" >+</button></h5>
<!-- End of Education         -->
<!-- Beginning of Employment -->
<h4 class="more-top-margin-3x more-bottom-margin-1x center">Work History</h4>
<div class="less-top-margin less-bottom-margin" id="position_fields">
<script id="activity-template1" type="text">
    <div class="less-top-margin less-bottom-margin input-form more-top-margin-3x center" id="activity_div@COUNT@">
      <input type="text"
       class="activity ui-autocomplete-custom
              text-box-long max-entry-box-width"
       name="task@COUNT@"
       id="activity@COUNT@"/>
</script>
<script id="activity-template2" type="text">
       <p class="less-top-margin box-input-label
                 less-bottom-margin">
          Delete preceeding activity: <input
          type="button" class="click-plus" value="-"
          onclick="deleteActivity(
            '#activity_div@COUNT@',
            'activity@COUNT@');
            return false;"/></p>
        </div>
</script>
<?php
$pos = 0;
$count_task = 0;
foreach($positions as $position){
  //var_dump($position);
    $pos++;
    $positionid = $position['position_id'];
    $finalyr =$position['yearLast'];
    if($finalyr == 9999){
       $finalyr = '';
    }
    $activity = loadActivity($profileid,  $positionid, $pdo);
    echo '<div class="form-background div-form-group border-top-bottom left more-bottom-margin more-top-margin-3x" id="position'.$pos.'">';
    if ($mobile) {
       echo '<div class="container-form-entry">
                   <div class="inline-block">
                      <div class="less-bottom-margin year-input-label">Start Year</div>
                      <div class="less-top-margin less-bottom-margin">
                        <input class="year-entry-box short-input-form" type="number"
                                 min=1940 max=2100 name="wrk_start_yr'.$pos.'"
                               value="'.$position['yearStart'].'" id="wrk_start_yr'.$pos.'">
                      </div>
            </div>
            <div class="inline-block">
                  <div class="less-bottom-margin year-input-label">Final Year</div>
                      <div class="less-top-margin less-bottom-margin short-input-form">
                           <div><input class="year-entry-box" type="number" min=1940 max=2100 name="wrk_final_yr'.$pos.'"
                                       value="'.$position['yearLast'].'" id="wrk_final_yr'.$pos.'">
                           </div>
                      </div>
                  </div>
            </div>';
    } else {
        echo '<div class="container-fluid">
              <div class="row">
                 <div class="inline-block">
                    <div class="less-bottom-margin year-input-label"> Start Year</div>
                    <div class=""><input class="year-entry-box short-input-form" type="number"
                             min=1940 max=2100 name="wrk_start_yr'.$pos.'"
                              value="'.$position['yearStart'].'" id="wrk_start_yr'.$pos.'">
                    </div>
                 </div>
                 <div class="inline-block">
                     <div class="less-bottom-margin year-input-label"> Final Year</div>
                     <div class=""><input class="year-entry-box short-input-form" type="number"
                              min=1940 max=2100 name="wrk_final_yr'.$pos.'"
                        value="'.$position['yearLast'].'" id="wrk_final_yr'.$pos.'">
                     </div>
                 </div>
              </div>
            </div> ';
    }
    // onchange="this.className=(this.value==\'\')?\'\':\'visited-long\';"
    // onchange="this.className=(this.value==\'\')?\'\':\'visited-long\';"
    // onchange="this.className=(this.value==\'\')?\'\':\'paragraph-box visited\';"
    echo  '<div class="div-form-group no-border"><p class="less-bottom-margin left">
                  Organization Name:
              </p></p><p class="left"> <input type="text"
                  class="text-box-long less-top-margin less-bottom-margin
                         left" name="org'.$pos.'"
                  value="'.htmlentities(trim($position['organization']))
               .'"   id="org'.$pos.'"/>
              </p>
              <p class="less-bottom-margin left">
                        Job Title:
              </p><p class="left">
                 <input type="text"
                 class="text-box-long less-top-margin less-bottom-margin left"
                 name="title'.$pos.'"
                 value="'.htmlentities(trim($position['title']))
                         .'" id="title'.$pos.'"/>
              </p><p class="less-bottom-margin left">Job Summary: </p>
          <div><div><textarea class= "paragraph-box margin-close
                                less-top-margin less-bottom-margin"
                             style="min-width:300px;"
                             name="job_summary'.$pos.'" rows = "8"
                               id="summary'.$pos.'">'
                               .htmlentities(trim($position['summary'])).
              '</textarea></div>
              <p class="less-bottom-margin more-top-margin-2x">
                             Activities:</p>';


    foreach($activity as $task){
       $count_task++;
       $activity_position_tag = 'position'.$pos;
       //$activity_rank = htmlentities(trim($task['activity_rank']));
       //$activity_job_number = htmlentities(trim($task['activity_rank']));
       // <input type="text" name = '.$position
       //    .'value="'.$pos.'"
      //    id="add_activity'.$count_task.'" />

      // <input type="text" name="activity_rank"'.$count_task.'
      //       value="'.$pos.'"
      //       id="activityrank'.$count_task.'" />
      // onchange= "this.className=(this.value==\'\') ? \'\' : \'activity ui-autocomplete-custom text-box-long visited-long max-entry-box-width \';"
      echo '<div id="activity_div'.$count_task.'" >
              <div class="less-top-margin less-bottom-margin
                                                            input-form center">
                <input class="activity ui-autocomplete-custom text-box-long"
                         name="task'.$count_task.'" type="text"
                        value="'.htmlentities(trim($task['description'])).'"
                           id="activity'.$count_task.'" >
                <input type="hidden"
                        name="activity_position_tag'.$count_task. '"
                       value='.$pos.' id="activity_parent'.$count_task.'" />
                <p class=
                   "less-top-margin box-input-label less-bottom-margin center">
                    Delete preceding activity:
                    <input class="click-plus" type="button" value="-"
                        onclick =
                  "deleteActivity(\'#activity_div'.$count_task.'\',\'activity'
                                            .$count_task.'\'); return false"/>
                  </p>
               </div>
            </div>';
    }
echo '<div class="less-bottom-margin" id="activity_fields'.$pos.'"> </div>';
    // activity field section has following ending
       echo  '<h4 class="small-bottom-pad more-top-margin-2x center">
               Add Activity <button class="click-plus-activities button-small"
                                        id="add_activity'.$pos.'">+</button>
              </h4>
              <p class="less-top-margin">Delete this position:
                     <input class="click-plus" type="button" value="-"
                        onclick= "deleteJob(\'#position'.$pos
                          .'\',\'wrk_start_yr' .$pos
                          .'\',\'wrk_final_yr' .$pos
                          .'\',\'org'.$pos
                          .'\',\'title'.$pos
                          .'\',\'summary'.$pos
                          .'\'); return false;"/>
              </p>
        </div>
    </div>
</div>'; // this position
}
// <input class="button-submit" type="submit" onclick="return doValidate();" value="Save"/>
// <input class="button-submit" type="submit" name="cancel" value="Cancel" size="40">
?>
</div>  <!-- position fields -->
<h5 class="more-top-margin-3x center">Add Position <button class="click-plus button-small" id="addPos" >+</button></h5>
<!-- End of Employment -->
<h5 class="more-top-margin-3x headline-green center"><span class="link-info">Save changes</span>
      <button class="button-submit" onclick="return doValidate();">Save</button>
      &nbsp;
      <input class="button-submit spacer wide-10char" type="submit" value="Cancel" name="cancel"/>
      <!-- <button class="button-submit">Cancel</button> -->
      <!-- doValidate() is a JavaScript function (see script.js) for preliminary validation
          which runs before the post to the website. The server program
          at the website (see util.php) performs a final validation check.-->
</h5>
</form>
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
        count_position =  Number("<?php echo $pos      ?>");
        count_edu =       Number("<?php echo $count_edu ?>");
        count_activity =  Number("<?php echo $count_task ?>");
        school_array = makeSchoolArray(count_edu);
        award_array = makeAwardArray(count_edu);
        activity_array = makeActivityArray(count_activity);
        org_year_start_array = makeJobYearStartArray(count_position);
        org_year_final_array = makeJobYearFinalArray(count_position);
        org_array = makeOrgArray(count_position);
        position_desc_array = makePositionDescArray(count_position);
        position_title_array = makePositionTitleArray(count_position);
        activity_removed =  0;
        edu_removed = 0;
        position_removed =  0;
        last_text_box =  'fn';
        test_box =  'fn';
        audit_array = ["fn","ln","em","pf","gl"];
        audit_list = {"fn": -1,"ln": -1 ,"em" : -1};
        // When education is added, the immediate previous education
        // is checked for offensive language.
        $('#addEdu').click(function(event) {
            event.preventDefault();
            if( count_edu - edu_removed + 1 > 9){
              triggerAlert('Maximum of nine education entries exceeded', replace = true);
              return;
            } else {
              count_edu = count_edu + 1;
            }
            var source = $('#edu-template').html();
            $('#edu_fields').append(source.replace(/@COUNT@/g, count_edu));
            $(function(){
              $('.school').click(function(e){e.preventDefault();}).click();
            });
            $(document).on('input', '.school', 'input[type="text"]', function(){
               var school_id = $(this).attr("id");
               var term = document.getElementById(id=school_id).value;
               window.console && console.log('preparing json for '+term);
               if(term.length > 1) {
                 $.getJSON('school.php?ter'+'m='+term, function(data) {
                    window.console && console.log('data returned'+data);
                    var y = data;
                    $('.school').autocomplete({ source: y });
                  });
               }
               });
               $(function(){
                  $('.award').click(function(e){e.preventDefault();}).click();
               });
               $(document).on('input', '.award', 'input[type="text"]', function(){
                 var award_id = $(this).attr("id");
                 var term = document.getElementById(id=award_id).value;
                 window.console && console.log('preparing award json for '+term);
                 if(term.length > 1) {
                   $.getJSON('edu_award.php?ter'+'m='+term, function(data) {
                        window.console && console.log('data returned'+data);
                        var y = data;
                        $('.award').autocomplete({ source: y });
                   });
                 }
               });
            var field = "school"+count_edu;
            school_array.push(field);
            var award_field = "award"+count_edu;
            award_array.push(award_field);
        });  //end of addedu
        $('#addPos').click(function(event){
            event.preventDefault();
            if( count_position - position_removed + 1 > 5){
                triggerAlert('Maximum of five position entries exceeded', replace = true);
                return;
            } else {
                count_position = count_position + 1;
            }
            var position_id = 'position_years'+count_position;
            // onchange=\"this.className=(this.value==\'\')?\'\':\'visited-long\';\"
            // onchange=\"this.className=(this.value==\'\')?\'\':\'visited-long\';\"
            // onchange="this.className=(this.value==\'\')?\'\':\'paragraph-box visited\';"
            $('#position_fields').append(
                  '<div class="form-background div-form-group  border-top-bottom more-bottom-margin more-top-margin-3x" \
                              id="position' + count_position + '"> \
                    <div class="container-fluid"> \
                        <div class="row" id=' + position_id + '> </div> \
                    </div> \
                    <div class="div-form-group no-border"> \
                      <p class="less-bottom-margin left"> \
                          Organization: \
                      </p><p class="left"><input \
                          class="text-box-long less-top-margin less-bottom-margin left" \
                          type="text" name="org' + count_position +
                          '"id="org' + count_position +
                  '"/> </p><p class="less-bottom-margin left">Job Title</p>  \
                       <p class="left"><input \
                          class="text-box-long less-top-margin less-bottom-margin left" \
                          type="text" name="title'  + count_position + '" \
                            id="title' + count_position + '"/></p> \
                        <p class="less-bottom-margin left">Job Summary: \
                      </p><div><textarea class= "paragraph-box margin-close \
                              less-top-margin less-bottom-margin" \
                              style="min-width:300px;" \
                          name="job_summary'+ count_position + '" \
                          rows = "8" \
                             id="summary' + count_position + '"> \
                    </textarea> </div> \
                    <p class="less-bottom-margin more-top-margin-2x"> \
                                   Activities:</p> \
                    <div class="less-bottom-margin" id="activity_fields' +
                       count_position + '"> \
                    </div> \
                    <h4 class="small-bottom-pad more-top-margin-2x center"> \
                         Add Activity \
                    <input type="button" \
                           class="click-plus-activities-input-button button-small" value="+" \
                           id = "add_activity' + count_position + '");\
                            /> </h4>\
                    <p class="left"> Delete this position: \
                       <input type="button" \
                                class="click-plus" value="-" \
                                onclick = \
                                "deleteJob(\'#position' + count_position +
                                 '\',\'wrk_start_yr'    + count_position +
                                 '\',\'wrk_final_yr'    + count_position +
                                 '\',\'org'         + count_position +
                                 '\',\'title'         + count_position +
                                 '\',\'summary'   + count_position +
                                 '\'); return false;" /> \
                    </p> \
                 </div>'
);
            var org_start_year_field = "wrk_start_yr"+count_position;
            org_year_start_array.push(org_start_year_field);
            var org_final_year_field = "wrk_final_yr"+count_position;
            org_year_final_array.push(org_final_year_field);
            var orgfield = "org"+count_position;
            org_array.push(orgfield);
            var position_desc = "summary"+count_position;
            position_desc_array.push(position_desc);
            var position_title = "title"+count_position;
            position_title_array.push(position_title);
            //var position = 'position_years'+count_position;
            var insertion_id = "position_years"+count_position;
            var insertionTag = document.getElementById(insertion_id);
        //  Starting Year
        //  Define container for an input box and its label
            var work_start_year_group = document.createElement("div");
            var work_start_year_group_id = "work_start_year_group"+count_position;
            work_start_year_group.id = work_start_year_group_id;
        //  1-line or 2-line option for large or small devices
            if(isLargeDevice){
             // large device (1-line)
                work_start_year_group.className = "inline-block";
            } else {
             // small device (2-lines)
                //work_start_year_group.className = "";
            }
        //  Define container for label
            var start_year_label_div = document.createElement("div");
            var start_year_label_div_id = "work_start_year_label"+count_position;
            start_year_label_div.id = start_year_label_div_id;
            start_year_label_div.className = "less-bottom-margin year-input-label";
        //  Make Label and attach to container for label
            var node = document.createTextNode("Start Year");
            start_year_label_div.appendChild(node);
            work_start_year_group.appendChild(start_year_label_div);
        //  Define container for input box
            var work_start_year_input_div = document.createElement("div");
            var work_start_year_input_div_id = "work_start_year"+count_position;
            work_start_year_input_div.id = work_start_year_input_div_id;
            work_start_year_input_div.className = "less-bottom-margin less-top-margin";
       //   Make input box and attach it to its container
            var work_start_year_input = document.createElement("input");
       //   This is the tag id for the form year
            var work_start_year_id = "wrk_start_yr"+count_position;
            var work_start_year_name = "wrk_start_yr"+count_position;
            work_start_year_input.className = "year-entry-box short-input-form";
            work_start_year_input.name = work_start_year_name;
            work_start_year_input.id = work_start_year_id;
            work_start_year_input.type = "number";
            work_start_year_input.min = 1940;
            work_start_year_input.max = 2100;
            work_start_year_input_div.appendChild(work_start_year_input);
            work_start_year_group.appendChild(work_start_year_input_div);
        //  Insert group directly into DOM
            insertionTag.appendChild(work_start_year_group);

        //  Final year_
            var work_final_year_group = document.createElement("div");
            var work_final_year_group_id = "work_final_year_group"+count_position;
            work_final_year_group.id = work_final_year_group_id;
            work_final_year_group.name = work_final_year_group_id;
            if(isLargeDevice){
                work_final_year_group.className = "inline-block";
            }
            var final_year_label_div = document.createElement("div");
       //   This is the tag id for the final form year
            var final_year_label_div_id = "work_final_year_label"+count_position;
            final_year_label_div.id = final_year_label_div_id;
            final_year_label_div.className = "less-bottom-margin year-input-label";

            var node = document.createTextNode("Final Year");
            final_year_label_div.appendChild(node);
            work_final_year_group.appendChild(final_year_label_div);

            var work_final_year_input_div = document.createElement("div");
            var work_final_year_input_div_id = "work_final_year"+count_position;
            work_final_year_input_div.id = work_final_year_input_div_id;
            work_final_year_input_div.className = "less-bottom-margin less-top-margin";

            var work_final_year_input = document.createElement("input");
            var work_final_year_id = "wrk_final_yr"+count_position;
            var work_final_year_name = "wrk_final_yr"+count_position;
            work_final_year_input.className = "year-entry-box short-input-form";
            work_final_year_input.name = work_final_year_name;
            work_final_year_input.id = work_final_year_id;
            work_final_year_input.type = "number";
            work_final_year_input.min = 1940;
            work_final_year_input.max = 2100;
            work_final_year_input_div.appendChild(work_final_year_input);
            work_final_year_group.appendChild(work_final_year_input_div);
            if(isMobileDevice){
               insertionTag.appendChild(work_final_year_group);
               //  Insert 1-line group directly into DOM
            }  else {
                insertionTag.appendChild(work_final_year_group);
            }
            window.console && console.log("Adding position "+count_position);
});
    $(document).on('click', '.text-box', 'input[type="text"]', function(){
            var p = $(this);
            var tagId = $(this).attr("id");
            last_text_box = tagId;
            window.console && console.log("Text box was clicked: "+last_text_box);
            checkLanguage(tagId);
    });
    $(document).on('click', '.text-box-long', 'input[type="text"]', function(){
            var p = $(this);
            var tagId = $(this).attr("id");
            last_text_box = tagId;
            window.console && console.log("Text box was clicked: "+last_text_box);
            checkLanguage(tagId);
    });
    $(document).on('click', '.paragraph-box', 'input[type="text"]', function(){
            var p = $(this);
            var tagId = $(this).attr("id");
            window.console && console.log(last_text_box);
            last_text_box = tagId;
            window.console && console.log("Paragraph box was clicked: "+last_text_box);
            checkLanguage(tagId);
     });
     $(document).on('click', '.year-entry-box', 'input[type="text"]', function(){
             var p = $(this);
             var tagId = $(this).attr("id");
             if(submitted===true){
                  reformatDataEntryBox(tagId);
             }
     });
    // load old activities
    $(document).on('click', '.click-plus-activities','button', function(){
             event.preventDefault();
             var p = $(this);
             var tagId = $(this).attr("id");
             window.console && console.log("At start of click-plus-activities: Button was clicked: " + tagId);
             var base = "add_activity"; // 14
             var start = base.length;
             //var str = tagId;
             var len = tagId.length;
             // Job number is the button number
             var job_num = tagId.substr(start, len);
             window.console && console.log('Preparing to add activity for Job-' + job_num);
             if(count_activity - activity_removed + 1 > 930) {
                  // triggerAlert('Maximum of nine hundred and thirty activities exceeded',
                  //     replace=true);
                 return;
               } else {
                   count_activity = count_activity + 1;
              }
              window.console && console.log("Adding activity-" +
                                                              count_activity);
              // Fill out the template block within the html code
              activity_num = count_activity;
              var location =  "#" + "activity_fields" + job_num;
              //onchange= \
              //"this.className=(this.value==\'\')?\'\': \'activity ui-autocomplete-custom \
              //                     text-box-long visited-long max-entry-box-width\';" \
              $(location).append('<div \
                   class="less-top-margin less-bottom-margin \
                          input-form center" \
                      id="activity_div' + activity_num + '"> \
               <input type="text" \
                               class="activity ui-autocomplete-custom \
                                text-box-long max-entry-box-width" \
                               name="task' + activity_num + '" \
                                 id="activity' + activity_num + '"/> \
               <input type="hidden" \
                       name="activity_position_tag' + activity_num + '" \
                       value=' + job_num +
                       ' id="activity_parent' + activity_num + '" /> \
               <p class="less-top-margin box-input-label \
                         less-bottom-margin"> \
                  Delete preceeding activity: <input \
                  type="button" class="click-plus" value="-" \
                  onclick="deleteActivity( \
                    \'#activity_div' + activity_num + '\', \
                    \'activity' + activity_num + '\'); return false;"/></p> \
                </div>'
              );
              window.console && console.log(
                 "Added Activity-"+ activity_num + " for Job-" + location);
              var field = "activity"+activity_num;
              activity_array.push(field);
      });
      // load new actvities
      $(document).on('click', '.click-plus-activities-input-button','input[type="button"]', function(){
               event.preventDefault();
               var p = $(this);
               var tagId = $(this).attr("id");
               window.console && console.log("Button for click-plus-activities-input-button: " + tagId);
               //checkLanguage(tagId);
               var base = "add_activity"; // 14
               var start = base.length;
               var len = tagId.length;
//               // Job number is the button number
               var job_num = tagId.substr(start, len);
               window.console && console.log('Adding activity for Job-' + job_num);
               if(count_activity - activity_removed + 1 > 930) {
                   triggerAlert('Maximum of nine hundred and thirty activities exceeded',
                   replace=true);
                    return;
                } else {
                     count_activity = count_activity + 1;
                }
                window.console && console.log('Adding activity for Job-' + job_num);
                window.console && console.log("Adding activity-" +
                    count_activity);
                // Fill out the template block within the html code
                activity_num = count_activity;
                //var source1 = $('#activity-template1').html();
                var location =  "#" + "activity_fields" + job_num;
                // onchange= "this.className=(this.value==\'\')?\'\': \'activity ui-autocomplete-custom \
                //       text-box-long visited-long max-entry-box-width\';" \
                $(location).append('<div \
                     class="less-top-margin less-bottom-margin \
                            input-form center" \
                        id="activity_div' + activity_num + '"> \
                  <input type="text" \
                                 class="activity ui-autocomplete-custom \
                                  text-box-long max-entry-box-width" \
                                 name="task' + activity_num + '" \
                                   id="activity' + activity_num + '"/> \
                 <input type="hidden" \
                         name="activity_position_tag' + activity_num + '" \
                         value=' + job_num +
                         ' id="activity_parent' + activity_num + '" /> \
                 <p class="less-top-margin box-input-label \
                           less-bottom-margin"> \
                    Delete preceeding activity: <input \
                    type="button" class="click-plus" value="-" \
                    onclick="deleteActivity( \
                      \'#activity_div' + activity_num + '\', \
                      \'activity' + activity_num + '\'); return false;"/></p> \
                  </div>'
                );
                window.console && console.log(
                   "Added Activity-"+ activity_num + " for Job-" + location);
                var field = "activity"+activity_num;
                activity_array.push(field);
       });
});
</script>
</body>
</html>
