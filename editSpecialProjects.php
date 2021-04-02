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
// Check for initial GET request without (or without) post information
// Only required entries are the first and laat names and the email.
if (isset($_POST['profile_id']) ) {
    unset($_SESSION['error']);
    unset($_SESSION['success']);
    unset($_SESSION['message']);
    $_SESSION['message'] = ' ';
    $profileid = $_SESSION['profile_id'];
    //  Special Projects and Demos
    // Delete old projects; insert new list
    $stmt = $pdo->prepare('DELETE FROM Project WHERE profile_id = :pid');
    $stmt->execute(array(':pid' => $profileid));
    insertProjects($profileid,$pdo);
    header('Location: edit.php?profile_id='.$profileid);
    return;
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
   $projects = loadProjects($profileid,$pdo);
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
<p class="more-top-margin-1x less-bottom-margin center">When finished, click the 'Save'
    button at the bottom to save your work.</p>
<form method="post" name="form1">
<!-- hidden unchangeable information -->
    <input type="hidden" name="profile_id" value ="<?= $profileid ?>" />
    <p><input type="hidden" name="user_id" value ="<?= $uid ?>" id="userid"/></p>
<!-- Modifiable Information -->
<!-- Projects and Work Demonstrations -->
<h4 class="more-top-margin-3x more-bottom-margin-1x center">Projects</h4>
<div class="less-top-margin less-bottom-margin centered-row-layout" id="proj_fields">
<?php
$count_proj = 0;
foreach($projects as $project){
        $count_proj++;
        // <div class="container-form-entry">
        //     <div class="left div-year-group">Year
        //      <input class="year-entry-box"
        //              type="text" name="proj_year'.$count_proj.'"
        //             value="'.$project['year'].'"
        //                id="proj_year'.$count_proj.'" />
        //     </div>
        // </div>
        echo '<div class="form-background div-form-group border-top-bottom
                 more-bottom-margin more-top-margin-3x" id="proj_div'.$count_proj.'">
                <div class="container-form-entry">
                    <div class="less-bottom-margin less-top-margin">
                      <p class="margin-bottom-small">Project Name</p>
                      <p class="margin-top-small margin-bottom-small small">
                           Example: Auto Mechanics
                      </p>
                    </div><div class="less-top-margin less-bottom-margin input-form">
                        <input class="award ui-autocomplete-custom text-box-long" type="text" name="proj'.$count_proj.
                        '" value="'.htmlentities(trim($project['name'])).'" id="proj'.$count_proj.'">
                    </div><div class="less-top-margin less-bottom-margin input-form">
                        <p class="margin-bottom-small">Link to report; for example, https://www.myschool.org/project12345</p>
                        <input class="ui-autocomplete-custom award text-box-long" type="text" name="proj_report_lnk'.$count_proj.
                        '" value="'
                        .htmlentities(trim($project['report_link']))
                        .'" id="proj_report_lnk'.$count_proj.'">
                    </div><div class="less-top-margin less-bottom-margin input-form">
                        <p class="margin-bottom-small">Link to Github Repository; for example, https://www.github.com/myName/12345</p>
                        <input class="ui-autocomplete-custom award text-box-long" type="text" name="git'.$count_proj.
                        '" value="'
                        .htmlentities(trim($project['github_link']))
                        .'" id="git'.$count_proj.'">
                    </div>
                </div><div>
                    <p>Delete this project <input
                       class="click-plus" type="button" value="-"
                        onclick =
 "deleteProject(\'#proj_div'.$count_proj.'\',\'proj'.$count_proj.'\',\'proj_report_lnk'.$count_proj.'\',\'git'.$count_proj.'\'); return false;" />
                    </p>
                </div>
            </div>';
    }
?>

<script type="text" id="proj-template">
    <div class="form-background div-form-group border-top-bottom more-bottom-margin more-top-margin-3x left" id="proj_div@COUNT@">
    <div class="container-form-entry less-bottom-margin">
        <div class="less-bottom-margin less-top-margin left">
          <p class="margin-bottom-small">Project Name</p>
          <p class="margin-top-small margin-bottom-small small">
             Example: Auto Mechanics
          </p>
        </div>
        <div class="less-bottom-margin less-top-margin input-form">
          <input class="name-box ui-autocomplete-custom text-box-long"
                 type="text" size="80" name="proj@COUNT@"
                 id="proj@COUNT@" />
        </div>
        <div class="less-top-margin less-bottom-margin input-form">
            <p class="margin-bottom-small">Report link; for example, https://www.myschool.org/project12345</p>
            <input class="ui-autocomplete-custom name-box text-box-long" type="text"
              name="proj_report_lnk@COUNT@" id="proj_report_lnk@COUNT@"/>
        </div>
        <div class="less-top-margin less-bottom-margin input-form">
            <p class="margin-bottom-small">Github link; for example, https://www.github.com/project12345</p>
            <input class="ui-autocomplete-custom award text-box-long" type="text"
              name="git@COUNT@" id="git@COUNT@"/>
        </div>
    </div>
    <div class="less-bottom-margin">
        <p class="less-top-margin less-bottom-margin"> Delete this project:
            <input type="button" class="click-plus" value="-"
                onclick="deleteProject('#proj_div@COUNT@','proj@COUNT@','proj_report_lnk@COUNT@',
                 'git@COUNT@'); return false;"/>
        </p>
    </div>
    </div>
</script>
</div>
<h5 class="more-top-margin-3x center">Add Project:
   <button class="click-plus less-bottom-margin button-small"
           id="addProj" >+</button></h5>

<!--        End of Projects        -->
<h4 class="more-top-margin-3x headline-green center"><span class="link-info">Save changes</span>
      <button class="button-submit" onclick="return doValidateProjects();">Save</button>
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
        count_proj = Number("<?php echo $count_proj ?>");
        proj_array = makeProjectArray(count_proj);
        proj_report_array = makeProjectReportArray(count_proj);
        github_array = makeGithubArray(count_proj);
        window.console && console.log('The project array is ' + proj_array);
        proj_removed = 0;
        last_text_box =  '';
        test_box =  '';
        audit_array = [];
        audit_list = {};
        $('#addProj').click(function(event) {
            event.preventDefault();
            if( count_proj - proj_removed + 1 > 9){
              triggerAlert('Maximum of nine projects exceeded', replace = true);
              return;
            } else {
              count_proj = count_proj + 1;
            }
            var source = $('#proj-template').html();
            triggerAlert("Adding Project", true );
            $('#proj_fields').append(source.replace(/@COUNT@/g, count_proj));
            triggerAlert("Project appended", true );
            $(function(){
              $('.name-box').click(function(e){e.preventDefault();}).click();
            });
            var field = "proj"+count_proj;
            proj_array.push(field);
            var proj_report_field = "proj_report_lnk"+count_proj;
            proj_report_array.push(proj_report_field);
            var github_field = "git"+count_proj;
            github_array.push(github_field);
        });  //end of addProj
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
});
</script>
</body>
</html>
