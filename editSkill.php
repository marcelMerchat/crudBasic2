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
// Check for initial GET request without (or without) post information
// Only required entries are the first and laat names and the email.
if (isset($_POST['profile_id']) ) {

    unset($_SESSION['error']);
    unset($_SESSION['success']);
    unset($_SESSION['message']);
    $_SESSION['message'] = ' ';
    $profileid = $_SESSION['profile_id'];
    //  Skills: delete old skill entries; insert new list
    $stmt = $pdo->prepare('DELETE FROM SkillSet WHERE profile_id = :pid');
    $stmt->execute(array(':pid' => $profileid));
    insertSkillSet($profileid,$pdo);
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
   $skills = loadSkill($profileid,$pdo);
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
    <h4 class="less-bottom-margin center">Editing Skills</h4>

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
<p class="more-top-margin-1x less-bottom-margin center">When finished, click the 'Save'
    button at the bottom to save your work.</p>
<form method="post" name="form1">
          <!-- hidden unchangeable information -->
          <input type="hidden" name="profile_id" value ="<?= $profileid ?>" />
          <p><input type="hidden" name="user_id" value ="<?= $uid ?>" id="userid"/></p>
<!-- Modifiable Information -->
<!-- Skills -->
<h4 class="more-top-margin-1x less-bottom-margin center">Skills</h4>
<!-- the id addSkill point to a JavaScript function -->
<div class="less-top-margin less-bottom-margin" id="skill_fields">
<?php
  $count_skill = 0;
  foreach($skills as $skill){
           $count_skill++;
echo '<div id="skill'.$count_skill.'" >
                 <div class="less-top-margin less-bottom-margin input-form center">
                   <input class="skill ui-autocomplete-custom text-box-long"
                        type="text" name="skill_name'.$count_skill.
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
           type="text" name="skill_name@COUNT@"
           value='<?= htmlentities("") ?>' id="jobskill@COUNT@"/>
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
<h4 class="more-top-margin-3x headline-green center"><span class="link-info">Save changes</span>
      <button class="button-submit" onclick="return validateSkill();">Save</button>
      &nbsp;
      <input class="button-submit spacer wide-10char" type="submit" value="Cancel" name="cancel"/>
      <!-- <button class="button-submit">Cancel</button> -->
      <!-- doValidate() is a JavaScript function (see script.js) for preliminary validation
          which runs before the post to the website. The server program
          at the website (see util.php) performs a final validation check.-->
</h4>
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
        count_skill =     Number("<?php echo $count_skill ?>");
        skill_array = makeSkillArray(count_skill);
        skill_removed =  0;
        last_text_box =  '';
        test_box =  '';
        audit_array = [];
        audit_list = {};
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
            $(function(){
              $('.skill').click(function(e){e.preventDefault();}).click();
            });
            $(document).on('input', '.skill', 'input[type="text"]', function(){
            //$(document).on('click', '.skill', 'input[type="text"]', function(){
                var skill_id = $(this).attr("id");
                window.console && console.log(' click json for : '+ skill_id);
                var term_skill = document.getElementById(id=skill_id).value;
                window.console && console.log(' content is '+ term_skill);
                if(term_skill.length > 1) {
                  $.getJSON('skill.php?ter'+'m='+term_skill, function(data) {
                     window.console && console.log(' Data returned: '+data);
                     var ys = data;
                     $('.skill').autocomplete({ source: ys });
                  });
                }
            });
            var field = "jobskill"+count_skill;
            skill_array.push(field);
        });
        //When a new skill is added, the immediate previous skill is checked
        //for offensive language.
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
});
</script>
</body>
</html>
