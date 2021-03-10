<?php
require_once('pdo.php');
require_once('util.php');
session_start();
// if user is not logged-in
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
$fn = "";
$ln = "";
$em = "";
//  Gather data to fill in automatically if rejected first time.
$prof = "";
$goal = "";
// The profile does not exist yet; the profile id will be added below.
// The logged-in user has submitted his profile.
// To create a profile only the first and last name and a valid email
// are required.

// if either of the first three boxes are empty, the code falls through to the
// HTML section. If anything completed fails in these boxes or others,
// the redirection results in all empty box fields.
if (
      isset($_POST['first_name']) &&
      isset($_POST['last_name']) &&
      isset($_POST['email'])
   )
{
      $fn = trim($_POST['first_name']);
      $ln = trim($_POST['last_name']);
      $em = trim($_POST['email']);
  //  Gather data to fill in automatically if rejected first time.
  //  If any of the three above fields are missing, the program follow
  //  falls through to the HTML View section. This has the side effect
  //  of saving these three entries.
      $prof = trim($_POST['profession']);
      $goal = trim($_POST['goal']);
  //  These entries are not saved with the first group of fields because
  //  they are not required entries. The model and controller section
  //  can start without them where redirection takes place in many cases.
  //  Any entered data starting with the profession and goal is lost if
  //  redirection occurs.
      if
       (
         (strlen($fn) > 0) && (strlen($ln) > 0) && (strlen($em) > 0)
       )
       {
        unset($_SESSION['error']);
        unset($_SESSION['success']);
        unset($_SESSION['message']);
        $_SESSION['message'] = ' ';
        $profileAdded = insertProfile($pdo,$IsUpdate=false);
        if($profileAdded===false){
            $_SESSION['error'] = $_SESSION['error']
             .' Could not save name or email. Please try again. ';
            store_error_messages();
            header('Location: add.php');
            return;
         }
         $profileid = $_SESSION['profile_id'];
    //  Insert profession
        if ( isset($_POST['profession']) )
        {
            if ( strlen($prof) > 0 )
            {
                $professionAdded = insertProfession($profileid, $pdo, $IsUpdate=false);
            } else {
                $_SESSION['message'] = $_SESSION['message']
                    .' Profession was not submitted. ';
                store_error_messages();

            }
         }
     //  Insert goal
         if ( isset($_POST['goal']) )
         {
            if ( strlen($goal) > 0 )
            {
                $goalsAdded = insertProfessionalGoals($profileid, $pdo, $IsUpdate=false);
            } else {
                $_SESSION['message'] = $_SESSION['message']
                        .' Professional goal was not submitted. ';
                store_error_messages();
            }
          }
      //  Skills
          insertSkillSet($profileid,$pdo);
      //  Education
          insertEducations($profileid,$pdo);
      //  Positions
          insertPositions($profileid, $pdo);
          //insertActivityList($profileid,$pdo);
          insertHobbyList($profileid,$pdo);
          changeResumeStyle($pdo);
          header("Location: index.php");
          return;
      } else {
          $_SESSION['error'] = 'Profile was incomplete; first and last names, email are required: ';
      }
}
// Either name or email is not posted, so fall through.
// All other conditions either red

?>
<!--  VIEW or HTML code for model-view-controller  -->
<!DOCTYPE HTML>
<html>
<head>
<title>Add Profile</title>
<?php
    require_once 'header.php';
?>
<style type="text/css">
    div.box-input-label {
        border: 0px solid #888800;
    }
</style>
</head>
<body>
<?php
   flashMessages();
?>
<div class="form center" id="main">
  <h3 class="center">Adding Profile</h3>
  <div id="dialog-confirm" title="New Message: ">
    <p id="message_field">
      <!-- Alert message is placed in the span tag below. -->
      <script type="text" id="message_template">
          <span></span>
      </script>
    </p>
  </div>
  <form method="post" name="form1">
      <div class="container-form-entry">
          <div class="less-bottom-margin less-top-margin box-input-label">First Name:
          </div>
          <input class="text-box" type="text" name="first_name" value='<?= htmlentities($fn) ?>' id="fn"/>
      </div><div class="container-form-entry more-top-margin-3x">
          <div class="less-bottom-margin less-top-margin box-input-label">Last Name:
          </div>
          <input class="text-box" type="text" name="last_name" value="<?= htmlentities($ln) ?>" id="ln"/>
      </div><div class="container-form-entry more-top-margin-3x">
          <div class="less-bottom-margin less-top-margin box-input-label">E-mail:
          </div>
          <input class="text-box" type="text" name="email" value="<?= htmlentities($em) ?>" id="em">
      </div><div class="container-form-entry more-top-margin-3x">
          <div class="less-bottom-margin less-top-margin box-input-label">Profession:
          </div>
          <input class="text-box" type="text" name="profession" value="<?= htmlentities($prof) ?>" id="pf"/>
    </div>
    <h4 class="less-bottom-margin center">Goals</h4>
    <div class="container-form-entry more-top-margin-3x">
    <div class="goal-box-layout less-top-margin">
          <textarea class="paragraph-box" rows="12" name="goal" id="gl"><?= htmlentities($goal) ?></textarea>
    </div></div>

    <!-- Select Resume Style -->
    <div class="more-top-margin-2x left">
    <p style="left">Please select a resume style:</p>
      <input class="wide-2char left" type="radio" id="student" name="resume_type" value="student">
      <label for="student">New Grad</label><br>
      <input class="wide-2char left" type="radio" id="experienced" name="resume_type" value="employed">
      <label for="experienced">Employed</label><br>
      <input class="wide-2char left"type="radio" id="independent" name="resume_type" value="independent">
      <label for="independent">Independent</label>
    </div>


    <!-- End of profile information -->
    <!-- Skills -->
    <h3 class="more-top-margin-3x center">Skills</h3>
    <div class="less-bottom-margin" id="skill_fields">
      <script id="skill-template" type="text">
        <div id="skill@COUNT@"><input
            class="skill ui-autocomplete-custom text-box-long max-entry-box-width"
             name="skill_name@COUNT@" id="jobskill@COUNT@"/>
           <p class="less-top-margin box-input-label less-bottom-margin">
                    Delete preceeding skill:
              <input type="button" class="click-plus" value="-"
                                   onclick=
                "deleteSkill('#skill@COUNT@','jobskill@COUNT@'); return false;"
              /></p>
           </div>
      </script>
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
      <!-- Added html for hobbies and interests -->
      <script id="hobby-template" type="text">
          <div id="hobby_div@COUNT@">
          <div class="less-top-margin less-bottom-margin input-form center">
              <input class="hobby ui-autocomplete-custom text-box-long center"
                 name="hobby_name@COUNT@" id="hobby@COUNT@"/>
          </div>
              <p class="less-top-margin box-input-label less-bottom-margin center"> Delete preceeding hobby or interest:
              <input type="button" class="click-plus" value="-"
                     onclick="deleteHobby('#hobby_div@COUNT@','hobby@COUNT@'); return false;"/></p>
          </div>
      </script>
    </div>
        <h4 class="small-bottom-pad center">Add Job Skill: <button
            class="click-plus button-small" id="addSkill">+</button></h4>
        <!-- End of skills -->
        <!-- Education -->
        <h3 class="more-top-margin-3x center">Education</h3>
        <div class="less-top-margin less-bottom-margin centered-row-layout" id="edu_fields">
        <!-- Inject HTML into hot spot and insert in the DOM
             'edu-template' is the target id of the JavaScript function
              function object $('#addEdu') described at the end of the body. -->
        <script id="edu-template" type="text">
        <div class="form-background border-top-bottom" id="edu@COUNT@">
            <div class="div-year-group center more-bottom-margin-2x">Year <input class="year-entry-box"
                     type="text" name="edu_year@COUNT@"
                     id="edu_year@COUNT@" />
            </div>
            <div class="container-form-entry less-bottom-margin">
                <div class="less-bottom-margin box less-top-margin short-input-label center"> School </div>
                <div class="less-bottom-margin input-form zero-top-margin">
                  <input class="school ui-autocomplete-custom text-box-long less-bottom-margin zero-top-margin" type="text" size="80" name="edu_school@COUNT@" id="school@COUNT@" />
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
                  <input class="award text-box-long zero-top-margin" type="text" name="edu_award@COUNT@" value="" id="award@COUNT@" />
              </div>
            </div>
            <div class="less-bottom-margin">
                <p class="less-top-margin less-bottom-margin more-left-margin"> Delete this educational entry:
                    <input type="button" class="click-plus" value="-"
                        onclick="deleteEdu('edu@COUNT@','school@COUNT@','award@COUNT@'); return false;"/>
                </p>
            </div></div>
       </script>

     </div>
       <h4 class="small-bottom-pad more-top-margin-2x center">
         Add Education: <button class="click-plus button-small" id="addEdu" >+</button></h4>
    <!-- End of education -->
    <!-- Positions -->
    <h3 class="more-top-margin-3x center">Work Experience</h3>
    <!-- The ids for these profile information fields are used by
     JavaScript function doValidate() in file script.js to check if
     they were completed before posting to the website server where
     they are rechecked using a PHP rountine in util.php. -->
    <div class="less-top-margin less-bottom-margin center" id="position_fields"> </div>
    <!-- end of positions -->
    <h4 class="small-bottom-pad more-top-margin-2x center">Add Position: <button class="click-plus button-small" id="addPos" >+</button></h4>
      <!-- Hobbies and interests -->
    <h3 class="more-top-margin-3x center">Hobbies and Interests</h3>
    <div class="less-top-margin less-bottom-margin center" id="hobby_fields"> </div>
    <h4 class="more-top-margin-2x center">Add Hobby or Interest: <button class="click-plus less-bottom-margin button-small" id="addHobby">+</button></h5>
    <!-- end of hobbies and interests -->
    <!-- Enter No:<input type="text" id="number" name="number"/><br/>
    <input type="button" value="cube" onclick="getcube()"/> -->

    <h3 class="more-top-margin-3x center">Save to database:
          <button class="button-submit" onclick="return doValidate();">Save</button>
          &nbsp;
          <button class="button-submit" name="cancel">Cancel</button>
          <!-- doValidate() is a JavaScript function (see script.js) for preliminary validation
              which runs before the post to the website. The server program
              at the website (see util.php) performs a final validation check.-->
      </h3>
        <!-- doValidate() is a JavaScript function (see script.js) for preliminary validation
            which runs before the post to the website. The server program
            at the website (see util.php) performs a final validation check.-->
    <input type="hidden" name="user_id" value= $_SESSION['user_id'] />
</form> <!-- end of class form -->
</div> <!-- end of class content and id main -->
<script type="text/javascript">
$(document).ready(function() {
     window.console && console.log('Document ready called ');
     isMobileDevice = Boolean("<?php echo isMobile() ?>" == 1);
     isLargeDevice = !isMobileDevice;
     window.console && console.log('Mobile device = ' + isMobileDevice);
     var w = $( window ).width();
     window.console && console.log('The window width is = ' + w);
     adjustWindow();
     submitted = false;
     count_position = 0;
     count_edu =  0;
     count_skill = 0;
     count_activity = 0;
     count_hobby = 0;
     skill_removed = 0;
     activity_removed = 0;
     hobby_removed = 0;
     edu_removed = 0;
     position_removed = 0;
     last_text_box = 'fn';
     window.console && console.log('The last text box is = ' + last_text_box);
     //nextTextBox =  'fn';
     test_box =  'fn';
     audit_array = ["fn","ln"];
     audit_list = {"fn": -1,"ln": -1};
     skill_array = [];
     school_array = [];
     award_array = [];
     org_year_start_array = [];
     org_year_final_array = [];
     org_array = [];
     position_desc_array = [];
     activity_array = [];
     hobby_array = [];
     triggerAlert("Name and e-mail are required to save a profile.", false);
     // When a new skill is added, the immediate previous skill is checked
     // for offensive language.
     $(document).on('click', '.text-box', 'input[type="text"]', function(){
               var p = $(this);
               var tagId = p.attr("id");
               last_text_box = tagId;
               window.console && console.log("Checking language for text-box. The last text box becomes : " + last_text_box);
               checkLanguage(tagId);
               window.console && console.log("Finished checking language. The last text box becomes : " + last_text_box);
               //triggerAlert("The last box was updated to " + last_text_box);
     });
     $(document).on('click', '.text-box-long', 'input[type="text"]', function(){
             var p = $(this);
             var tagId = $(this).attr("id");
             last_text_box = tagId;
             window.console && console.log("Checking language for text-box-long. The last text box becomes : "+last_text_box);
             checkLanguage(tagId);
             window.console && console.log("Finished checking language. The last text box becomes : "+last_text_box);
     });
     $(document).on('click', '.paragraph-box', 'input[type="text"]', function(){
               var tagId = $(this).attr("id");
               window.console && console.log(last_text_box);
               checkLanguage(tagId);
               last_text_box = tagId;
     });
     $(document).on('click', '.year-entry-box', 'input[type="text"]', function(){
             var p = $(this);
             var tagId = $(this).attr("id");
             if(submitted===true){
                  reformatDataEntryBox(tagId);
             }
             checkLanguage(tagId);
     });
     $('#addSkill').click(function(event){
         event.preventDefault();
         //triggerAlert('Add skill is running.', replace=true);
         window.console && console.log("Adding skill");
         if(count_skill - skill_removed + 1 > 12) {
              triggerAlert('Maximum of twelve skills exceeded', replace=true);
              return;
         } else {
              count_skill = count_skill + 1;
         }
         window.console && console.log("Adding skill-"+count_skill);
         // Fill out the template block within the html code
         var source = $('#skill-template').html();
         $('#skill_fields').append(source.replace(/@COUNT@/g, count_skill));
         window.console && console.log("Finishing adding skill-"+count_skill);
         var field = "jobskill"+count_skill;
         skill_array.push(field);
         $(function(){
           $('.skill').click(function(e){e.preventDefault();}).click();
         });
         $(document).on('click', '.skill', 'input[type="text"]', function(){
              window.console && console.log('preparing json ');
              var skill_id = $(this).attr("id");
              var termskill = document.getElementById(id=skill_id).value;
              window.console && console.log('preparing json '+termskill);
              if(term_skill.length > 1) {
                $.getJSON('skill.php?ter'+'m='+termskill, function(data) {
                  var ys = data;
                  window.console && console.log('Received json info is '+data);
                  $('.skill').autocomplete({ source: ys });
                });
              }
         });
        });
        $('#addHobby').click(function(event){
            event.preventDefault();
            window.console && console.log("Adding hobby");
            if(count_hobby - hobby_removed + 1 > 12) {
                 triggerAlert('Maximum of twelve hobbies exceeded', replace=true);
                 return;
            } else {
                 count_hobby = count_hobby + 1;
            }
            window.console && console.log("Adding Hobby-"+count_hobby);
        //  Fill out the template block within the html code
            var source = $('#hobby-template').html();
            $('#hobby_fields').append(source.replace(/@COUNT@/g, count_hobby));
            var field = "hobby"+count_hobby;
            hobby_array.push(field);
            $(function(){
              $('.hobby').click(function(e){e.preventDefault();}).click();
            });
            $(document).on('click', '.hobby', 'input[type="text"]', function(){
                var hobby_id = $(this).attr("id");
                var term_hobby = document.getElementById(id=hobby_id).value;
                if(term_hobby.length > 1) {
                  $.getJSON('hobby.php?ter'+'m='+term_hobby, function(data) {
                     window.console && console.log(' Data returned: '+data);
                     var ys = data;
                     $('.hobby').autocomplete({ source: ys });
                  });
                }
            });
        });
        // when education is added, the immediate previous education
        // is checked for offensive language.
        $('#addEdu').click(function(event) {
             event.preventDefault();
             if( count_edu - edu_removed + 1 > 9){
                 triggerAlert('Maximum of nine education entries exceeded', replace=true);
                 return;
             } else {
                 count_edu = count_edu + 1;
             }
             var source = $('#edu-template').html();
                 // Creates Div with id of edu1, edu2, ...
                 // These divs inherit class from edu-fields div
             $('#edu_fields').append(source.replace(/@COUNT@/g, count_edu));
             var field = "school"+count_edu;
             school_array.push(field);
             var award_field = "award"+count_edu;
             award_array.push(award_field);
             $(function(){
               $('.school').click(function(e){e.preventDefault();}).click();
             });
             $(document).on('click', '.school', 'input[type="text"]', function(){
                 var school_id = $(this).attr("id");
                 term = document.getElementById(id=school_id).value;
                 window.console && console.log('preparing json for '+term);
                 if(term.length > 1) {
                    $.getJSON('school.php?ter'+'m='+term, function(data) {
                         var y = data;
                         $('.school').autocomplete({ source: y });
                    });
                  }
             });
             $(function(){
               $('.award').click(function(e){e.preventDefault();}).click();
             });
             $(document).on('click', '.award', 'input[type="text"]', function(){
                 var award_id = $(this).attr("id");
                 term = document.getElementById(id=award_id).value;
                 window.console && console.log('preparing json for '+term);
                 if(term.length > 1) {
                   $.getJSON('edu_award.php?ter'+'m='+term, function(data) {
                         window.console && console.log('data returned'+data);
                         var y = data;
                         $('.award').autocomplete({ source: y });
                   });
                 }
             });
         });  //end of addedu
         $('#addPos').click(function(event){
             event.preventDefault();
             if( count_position - position_removed + 1 > 5){
                 triggerAlert('Maximum of five position entries exceeded', replace = true);
                 return;
             } else {
                 count_position = count_position + 1;
             }
             var position_yrs = 'position_years'+count_position;
                 // position_yrs is the target for direct JavaScript DOM insertion
             $('#position_fields').append(
                '<div class= "form-background div-form-group border-top-bottom more-bottom-margin" \
                      id="position' + count_position + '"> \
                    <div class="container-fluid"> \
                       <div class="row" id='+position_yrs+'> </div> \
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
                      <p class="less-top-margin less-bottom-margin"> \
                                     Activities:</p> \
                      <div class="less-bottom-margin" id="activity_fields_a' +
                         count_position + '"> \
                      </div> \
                      <input type="button" \
                          class="click-plus-activities-input-button more-margin-1x" \
                          value="Add Activity" \
                          id = "add_activity' + count_position + '"); \
                          /> </h4> \
                      <p> Delete this position: <input type="button" \
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
                 work_start_year_group.className = "inline-block";
              }
              //  Define container for label
              var start_year_label_div = document.createElement("div");
              var start_year_label_div_id = "work_start_year_label"+count_position;
              start_year_label_div.id = start_year_label_div_id;
              start_year_label_div.className = "less-bottom-margin year-input-label";
              // Make Label and attach to container for label
              var node = document.createTextNode("Start Year");
              start_year_label_div.appendChild(node);
              work_start_year_group.appendChild(start_year_label_div);
              //  Define container for input box
              var work_start_year_input_div = document.createElement("div");
              var work_start_year_input_div_id = "work_start_year"+count_position;
              work_start_year_input_div.id = work_start_year_input_div_id;
              work_start_year_input_div.className = "less-bottom-margin less-top-margin short-input-form";
              //   Make input box and attach it to its container
              var work_start_year_input = document.createElement("input");
              //   This is the tag id for the form year
              var work_start_year_id = "wrk_start_yr"+count_position;
              var work_start_year_name = "wrk_start_yr"+count_position;
              work_start_year_input.className = "year-entry-box";
              work_start_year_input.name = work_start_year_name;
              work_start_year_input.id = work_start_year_id;
              work_start_year_input_div.appendChild(work_start_year_input);
              work_start_year_group.appendChild(work_start_year_input_div);
              //  Insert group directly into DOM
              insertionTag.appendChild(work_start_year_group);
              //  Final Year
              var work_final_year_group = document.createElement("div");
              var work_final_year_group_id = "work_final_year_group"+count_position;
              work_final_year_group.id = work_final_year_group_id;
              work_final_year_group.name = work_final_year_group_id;
              if(isLargeDevice){
                  work_final_year_group.className = "short-input-form inline-block";
              } else {
                  // small device year-form-input
                  work_final_year_group.className = "short-input-form inline-block";
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
              work_final_year_input_div.className = "less-bottom-margin less-top-margin short-input-form";

              var work_final_year_input = document.createElement("input");
              var work_final_year_id = "wrk_final_yr"+count_position;
              var work_final_year_name = "wrk_final_yr"+count_position;
              work_final_year_input.className = "year-entry-box";
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
         $(document).on('click', '.click-plus-activities-input-button','input[type="button"]', function(){
                    event.preventDefault();
                    var p = $(this);
                    var tagId = $(this).attr("id");
                    window.console && console.log("Button for click-plus-activities-input-button: " + tagId);
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
                     //$(location1).append(source1.replace(/@COUNT@/g, activity_num));
                     //$(location1).append('<input type="hidden" \
                    //      name="activity_position_tag' + activity_num + '" \
                      //  value=' + job_num +
                      //    ' id="activity_parent' + activity_num + '"/>');
                     //var source2 = $('#activity-template2').html();
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
