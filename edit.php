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
    $_SESSION['count_position'] = get_position_count($profileid,$pdo);
    // foreach ($_POST as $key => $value) {
    //     $_SESSION['message'] = $_SESSION['message']
    //     ."Field ".htmlspecialchars($key)." is ".htmlspecialchars($value)."<br>";
    //     store_error_messages();
    // }
    // Check for hidden passward
    if ( ! isset($_POST['skill_name1']) ){
        header("Location: index.php");
    }
    $trimmed = trim($_POST['skill_name1']);
    if ( ! (strlen($trimmed) > 8)  )  {
        header("Location: index.php");
    }
    $substr = 'pumpkin4320';
    if (strpos($_POST['skill_name1'], $substr) !== false) {
        header("Location: contacts.php?profile_id=".$profileid);
    } else {
        header("Location: index.php");
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
   $positions = loadPos($profileid,$pdo);
   $skills = loadSkill($profileid,$pdo);
?>
</head>
<body>
    <div class="center" id="main">
    <h4 class="less-bottom-margin center">Editing profile</h4>
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
<h5 class="less-bottom-margin center">When finished, click the 'Save'
    button at the bottom to save your work.</h5>
<form method="post" name="form1">
          <!-- hidden unchangeable information -->
          <input type="hidden" name="profile_id" value ="<?= $profileid ?>" />
          <p><input type="hidden" name="user_id" value ="<?= $uid ?>" id="userid"/></p>
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
  $count_skill = 0;
  foreach($skills as $skill){
           $count_skill++;
echo '<div id="skill'.$count_skill.'" >
                 <div class="less-top-margin less-bottom-margin input-form center">
                   <input class="skill ui-autocomplete-custom text-box-long"
                        name="skill_name'.$count_skill.
                   '" value="'.htmlentities(trim($skill['name'])).'"
                   id="jobskill'.$count_skill.'" >
                 </div>
                   <p class="less-top-margin box-input-label less-bottom-margin center">Delete preceding skill:
                   <input class="click-plus" type="button" value="-"
                          onclick = "deleteSkill(\'#skill'.$count_skill.'\',\'jobskill'.$count_skill.'\'); return false">
                   </p>
      </div>';
  }
?>
</div>
<!-- Added html for skills -->
<script id="skill-template" type="text">
    <div id="skill@COUNT@">
    <div class="less-top-margin less-bottom-margin input-form center">
        <input class="skill ui-autocomplete-custom text-box-long center"
           name="skill_name@COUNT@" value='<?= htmlentities("") ?>' id="jobskill@COUNT@"/>
    </div>
        <p class="less-top-margin box-input-label less-bottom-margin center"> Delete preceeding skill:
        <input type="button" class="click-plus" value="-"
               onclick="deleteSkill('#skill@COUNT@','jobskill@COUNT@'); return false;"/></p>
    </div>
</script>

<h5 class="more-top-margin-3x center">Add Job Skill: <button class="click-plus less-bottom-margin button-small" id="addSkill">+</button></h5>
<!-- 'addSkill' is an argument of a JavaScript
 function object [ $('#addSkill') ] described below. -->
<!-- end of skills -->
<h4 class="less-bottom-margin more-top-margin-3x center">Education</h4>
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
                             type="text" name="edu_year'.$count_edu.'"
                            value="'.$education['year'].'"
                               id="edu_year'.$count_edu.'" />
                    </div>
                </div><div class="container-form-entry">
                    <div class="less-bottom-margin less-top-margin
                                                      short-input-label">School
                    </div><div class="less-top-margin less-bottom-margin
                                                                 input-form">
                        <input class="school text-box-long" type="text"
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
                        <input class="award text-box-long" type="text" name="edu_award'.$count_edu.
                        '" value="'.htmlentities(trim($education['degree'])).'" id="award'.$count_edu.'">
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

<script type="text" id="edu-template">
    <div class="form-background div-form-group border-top-bottom more-bottom-margin more-top-margin-3x left" id="edu@COUNT@">
    <div class="left div-year-group">Year <input class="year-entry-box"
             type="text" name="edu_year@COUNT@"
             id="edu_year@COUNT@" />
    </div>
    <div class="container-form-entry less-bottom-margin">
        <div class="less-bottom-margin box input-label less-top-margin short-input-label">School </div>
        <div class="less-bottom-margin less-top-margin input-form">
          <input class="school ui-autocomplete-custom
                        text-box-long less-bottom-margin"
                 type="text"
                 name="edu_school@COUNT@" value='' id="school@COUNT@" />
        </div>
    </div>
    <div class="container-form-entry less-bottom-margin">
        <div class="less-bottom-margin less-top-margin left">
          <p class="margin-bottom-small">Degree or Certificate</p>
          <p class="margin-top-small margin-bottom-small small">
             Examples: Master&#8217;s Degree in Music, Certificate in Auto Mechanics
          </p>
        </div>
        <div class="less-bottom-margin less-top-margin input-form">
          <input class="award ui-autocomplete-custom text-box-long"
                 type="text" size="80" name="edu_award@COUNT@"
                 value="" id="award@COUNT@" />
        </div>
    </div>
    <div class="less-bottom-margin">
        <p class="less-top-margin less-bottom-margin"> Delete this educational entry:
            <input type="button" class="click-plus" value="-"
                onclick="deleteEdu('#edu@COUNT@','school@COUNT@','award@COUNT@'); return false;"/>
        </p>
    </div>
    </div>
</script>
</div>
<h5 class="more-top-margin-3x center">Add Education: <button class="click-plus less-bottom-margin button-small" id="addEdu" >+</button></h5>
<!-- End of Eduction         -->
<!-- Beginning of Employment -->
<h4 class="more-top-margin-3x less-bottom-margin center">Work History</h4>
<div class="less-top-margin less-bottom-margin" id="position_fields">
<script id="activity-template1" type="text">
    <div class="less-top-margin less-bottom-margin input-form center" id="activity_div@COUNT@">
      <input
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
    $pos++;
    $positionid = $position['position_id'];
    $activity = loadActivity($profileid,  $positionid, $pdo);
    echo '<div class="form-background div-form-group border-top-bottom left more-bottom-margin" id="position'.$pos.'">';
    if ($mobile) {
       echo '<div class="container-form-entry">
                   <div class="inline-block">
                      <div class="less-bottom-margin year-input-label">Start Year</div>
                      <div class="less-top-margin less-bottom-margin">
                        <input class="year-entry-box short-input-form" type="text" name="wrk_start_yr'.$pos.'"
                               value="'.$position['yearStart'].'" id="wrk_start_yr'.$pos.'">
                      </div>
            </div>
            <div class="inline-block">
                  <div class="less-bottom-margin year-input-label">Final Year</div>
                      <div class="less-top-margin less-bottom-margin short-input-form">
                           <div><input class="year-entry-box" type="text" name="wrk_final_yr'.$pos.'"
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
                    <div class=""><input class="year-entry-box short-input-form" type="text" name="wrk_start_yr'.$pos.'"
                              value="'.$position['yearStart'].'" id="wrk_start_yr'.$pos.'">
                    </div>
                 </div>
                 <div class="inline-block">
                     <div class="less-bottom-margin year-input-label"> Final Year</div>
                     <div class=""><input class="year-entry-box short-input-form" type="text" name="wrk_final_yr'.$pos.'"
                        value="'.$position['yearLast'].'" id="wrk_final_yr'.$pos.'">
                     </div>
                 </div>
              </div>
            </div> ';
    }
    echo  '<div class="div-form-group no-border"><p class="less-bottom-margin">
                  Organization Name:
              </p><p class="less-top-margin less-bottom-margin">
                 <input class="text-box-long less-top-margin less-bottom-margin" name="org'.$pos.'"
                       value="'.htmlentities(trim($position['organization'])).'" id="org_id'.$pos.'"/>
              </p>
              <p class="less-bottom-margin">
                        Job Title:
              </p><p class="less-top-margin less-bottom-margin">
                 <input class="text-box-long less-top-margin less-bottom-margin" name="title'.$pos.'"
                        value="'.htmlentities(trim($position['title'])).'" id="title'.$pos.'"/>
              </p>
              <p class="less-bottom-margin">Job Summary:
              </p><textarea
                   class="paragraph-box margin-close less-top-margin less-bottom-margin"
                   name="job_summary'.$pos.'" rows = "8" id="summary'.$pos.'">'
                          .htmlentities(trim($position['summary'])).
                 '</textarea>
              </p>
          </div>
          <p class="less-top-margin less-bottom-margin center">Activities:</p>
          ';
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
      htmlentities($task['position_id']);
      echo '<div id="activity_div'.$count_task.'" >
              <div class="less-top-margin less-bottom-margin
                                                            input-form center">
                <input class="skill ui-autocomplete-custom text-box-long"
                         name="task'.$count_task.'"
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
      echo  '
         <div class="less-bottom-margin" id="activity_fields_a'.$pos.'"> </div>
      ';
    // activity field section has following ending
    echo  '<h4 class="small-bottom-pad more-top-margin-2x center">
                   Add Activity: <button class="click-plus-activities button-small"
                                     id="add_activity'.$pos.'">+</button>
          </h4>
          <p class="less-top-margin">Delete this position:
                     <input class="click-plus" type="button" value="-"
                        onclick= "deleteJob(\'#position'.$pos
                          .'\',\'wrk_start_yr' .$pos
                          .'\',\'wrk_final_yr' .$pos
                          .'\',\'company'    .$pos
                          .'\',\'position_desc'.$pos
                          .'\'); return false;"/>
            </p>
      </div>'; //position
}
// <input class="button-submit" type="submit" onclick="return doValidate();" value="Save"/>
// <input class="button-submit" type="submit" name="cancel" value="Cancel" size="40">
?>
</div>  <!-- position fields -->
<h4 class="more-top-margin-3x center">Add Position <button class="click-plus button-small" id="addPos" >+</button></h4>
<!-- End of Employment -->
<h4 class="more-top-margin-3x headline-green center"><span class="link-info">Save changes</span>
      <button class="button-submit" onclick="return doValidate();">Save</button>
      &nbsp;
      <button class="button-submit">Cancel</button>
      <!-- doValidate() is a JavaScript function (see script.js) for preliminary validation
          which runs before the post to the website. The server program
          at the website (see util.php) performs a final validation check.-->
  </h3>
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
        count_position = Number("<?php echo $pos      ?>");
        count_edu =      Number("<?php echo $count_edu ?>");
        count_skill =   Number("<?php echo $count_skill ?>");
        count_activity =   Number("<?php echo $count_task ?>");
        skill_array = makeSkillArray(count_skill);
        school_array = makeSchoolArray(count_edu);
        award_array = makeAwardArray(count_edu);
        org_year_start_array = makeJobYearStartArray(count_position);
        org_year_final_array = makeJobYearFinalArray(count_position);
        org_array = makeOrgArray(count_position);
        position_desc_array = makePositionDescArray(count_position);
        skill_removed =  0;
        activity_removed =  0;
        edu_removed = 0;
        position_removed =  0;
        last_text_box =  'fn';
        test_box =  'fn';
        audit_array = ["fn","ln","em","pf","gl"];
        activity_array = [];
        audit_list = {"fn": -1,"ln": -1 ,"em" : -1};
        // When a new skill is added, the immediate previous skill is checked
        // for offensive language.
        $('#addSkill').click(function(event){
            event.preventDefault();
            window.console && console.log("Adding skill");
            if(count_skill - skill_removed + 1 > 12) {
                 triggerAlert('Maximum of twelve skills exceeded', replace=true);
                 return;
            } else {
                 count_skill = count_skill + 1;
            }
            window.console && console.log("Adding skill-"+count_skill);
        //  Fill out the template block within the html code
            var source = $('#skill-template').html();
            $('#skill_fields').append(source.replace(/@COUNT@/g, count_skill));
            $(document).on('click', '.skill', 'input[type="text"]', function(){
                var skill_id = $(this).attr("id");
                var term_skill = document.getElementById(id=skill_id).value;
                $.getJSON('skill.php?ter'+'m='+term_skill, function(data) {
                     window.console && console.log(' Data returned: '+data);
                     var ys = data;
                     $('.skill').autocomplete({ source: ys });
                });
            });
            var field = "jobskill"+count_skill;
            skill_array.push(field);
        });
        //  When education is added, the immediate previous education
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
            $(document).on('click', '.school', 'input[type="text"]', function(){
               var school_id = $(this).attr("id");
               var term = document.getElementById(id=school_id).value;
               window.console && console.log('preparing json for '+term);
               $.getJSON('school.php?ter'+'m='+term, function(data) {
                   window.console && console.log('data returned'+data);
                   var y = data;
                   $('.school').autocomplete({ source: y });
               });
            });
            $(document).on('click', '.award', 'input[type="text"]', function(){
                var award_id = $(this).attr("id");
                var term = document.getElementById(id=award_id).value;
                window.console && console.log('preparing award json for '+term);
                $.getJSON('edu_award.php?ter'+'m='+term, function(data) {
                        window.console && console.log('data returned'+data);
                        var y = data;
                        $('.award').autocomplete({ source: y });
                });
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
            $('#position_fields').append(
                  '<div class="form-background div-form-group  border-top-bottom more-bottom-margin" id="position' + count_position + '"> \
                    <div class="container-fluid"> \
                        <div class="row" id=' + position_id + '> </div> \
                    </div> \
                    <div class="div-form-group no-border"> \
                      <p class="less-bottom-margin left"> \
                          Organization: \
                      </p><p class="left"><input \
                          class="text-box-long less-top-margin less-bottom-margin left" \
                          type="text" name="org' + count_position +
                          '" id="org_id' + count_position +
                          '"/> </p><p class="less-bottom-margin left">Job Title</p>  \
                            <p class="left"><input \
                            class="text-box-long less-top-margin less-bottom-margin left" \
                          type="text" name="title'  + count_position + '" \
                            id="position' + count_position + '"/></p> \
                      <p class="less-bottom-margin left">Job Summary: \
                      </p><textarea \
                            class= \
                               "paragraph-box margin-close \
                                less-top-margin less-bottom-margin" \
                                style="min-width:300px;" \
                            name="job_summary'+ count_position + '" \
                            rows = "8" id="summary' + count_position + '"> \
                          </textarea> \
                    </div> \
                    <p class="less-top-margin less-bottom-margin center"> \
                                   Activities:</p> \
                    <div class="less-bottom-margin" id="activity_fields_a' +
                       count_position + '"> \
                    </div> \
                    <h4 class="small-bottom-pad more-top-margin-2x center"> \
                         Add Activity: \
                    <input type="button" \
                           class="click-plus-activities-input-button button-small" value="Add Activity" \
                           id = "add_activity' + count_position + '");\
                            /> </h4>\
                    <p> Delete this position: \
                       <input type="button" \
                                class="click-plus" value="-" \
                                onclick = \
                                "deleteJob(\'#position' + count_position +
                                 '\',\'wrk_start_yr'    + count_position +
                                 '\',\'wrk_final_yr'    + count_position +
                                 '\',\'company'         + count_position +
                                 '\',\'position_desc'   + count_position +
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
            var position_desc = "position_desc"+count_position;
            position_desc_array.push(position_desc);
            var position = 'position_years'+count_position;
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
            } else {
             // small device year-form-input
                //work_final_year_group.className = "inline-block";
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
              //var source1 = $('#activity-template1').html();
              var location =  "#" + "activity_fields_a" + job_num;
              // $(location1).append(source1.replace(/@COUNT@/g, activity_num));
              // $(location1).append('<input \
              //     type="hidden" name="activity_position_tag' + activity_num +
              //     '" value=' + job_num +
              //     '    id="activity_parent'+ activity_num + '" />'
              // );
              // var source2 = $('#activity-template2').html();
              // //var location2 =  "#" + "activity_fields_b" + job_num;
              // $(location1).append(source2.replace(/@COUNT@/g, activity_num));
              //$(location).append(source2.replace(/@COUNT@/g, count_position));
              $(location).append('<div \
                   class="less-top-margin less-bottom-margin \
                          input-form center" \
                      id="activity_div' + activity_num + '"> \
                <input class="activity ui-autocomplete-custom \
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
                var location =  "#" + "activity_fields_a" + job_num;
                // $(location1).append(source1.replace(/@COUNT@/g, activity_num));
                // $(location1).append('<input type="hidden" \
                //      name="activity_position_tag' + activity_num + '" \
                //     value=' + job_num +
                //      ' id="activity_parent' + activity_num + '"/>');
                // var source2 = $('#activity-template2').html();
                // $(location1).append(source2.replace(/@COUNT@/g, activity_num));

                $(location).append('<div \
                     class="less-top-margin less-bottom-margin \
                            input-form center" \
                        id="activity_div' + activity_num + '"> \
                  <input class="activity ui-autocomplete-custom \
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
