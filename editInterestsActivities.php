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
if (isset($_POST['profile_id'])) {

    unset($_SESSION['error']);
    unset($_SESSION['success']);
    unset($_SESSION['message']);
    $_SESSION['message'] = ' ';
    $profileid = $_SESSION['profile_id'];

//  Hobbies and Interests
    // Delete old hobbies; insert new list
    $stmt = $pdo->prepare('DELETE FROM HobbyList WHERE profile_id = :pid');
    $stmt->execute(array(':pid' => $profileid));
    insertHobbyList($profileid,$pdo);

    $stmt = $pdo->prepare('DELETE FROM Personal WHERE profile_id = :pid');
    $stmt->execute(array(':pid' => $profileid));
    insertPersonal($profileid,$pdo);
    header("Location: edit.php?profile_id=".$profileid);
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
    <h4 class="less-bottom-margin center">Editing Interests and Activities</h4>
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

<!-- Hobbies and Interests -->
<h3 class="more-top-margin-3x center">Hobbies and Interests</h3>
<!-- the id addactivitypoint to a JavaScript function -->
<div class="less-top-margin less-bottom-margin" id="hobby_fields">
<?php
  $count_hobby = 0;
  foreach($hobbies as $hobby){
           $count_hobby++;
echo '<div id="hobby_div'.$count_hobby.'" >
                 <div class="less-top-margin less-bottom-margin input-form center">
                   <input class="hobby ui-autocomplete-custom text-box-long"
                           type="text"
                        name="hobby_name'.$count_hobby.
                   '" value="'.htmlentities(trim($hobby['name'])).'"
                   id="hobby'.$count_hobby.'" >
                 </div>
                   <p class="less-top-margin box-input-label less-bottom-margin center">Delete preceeding interest or hobby:
                   <input class="click-plus" type="button" value="-"
                          onclick = "deleteHobby(\'#hobby_div'.$count_hobby.'\',\'hobby'.$count_hobby.'\'); return false">
                   </p>
      </div>';
  }
?>
</div>

<!-- Added html for hobbies and interests -->
<script id="hobby-template" type="text">
    <div id="hobby_div@COUNT@">
    <div class="less-top-margin less-bottom-margin input-form center">
        <input class="hobby ui-autocomplete-custom text-box-long center"
                type="text"
           name="hobby_name@COUNT@" id="hobby@COUNT@"/>
    </div>
        <p class="less-top-margin box-input-label less-bottom-margin center"> Delete preceeding hobby or interest:
        <input type="button" class="click-plus" value="-"
               onclick="deleteHobby('#hobby_div@COUNT@','hobby@COUNT@'); return false;"/></p>
    </div>
</script>
<h5 class="more-top-margin-3x center">Add Hobby or Interest:
    <button class="click-plus less-bottom-margin button-small"
              id="addHobby">+</button></h5>

<!-- end of hobbies and interests -->
<!-- Start of personal -->

<!-- Other Optional Information: -->
<h3 class="more-top-margin-3x center">Other Optional Information</h3>
<?php
$hobbies = $interests['interest'];
$language = $interests['languages'];
$software = $interests['computer_skill'];
$pub = $interests['publication'];
$license = $interests['licenses'];
echo '<div id="interests_div more-top-margin-3x" >';
//                <div class="less-top-margin less-bottom-margin input-form center">
//                  <p class=
//                   "less-top-margin box-input-label less-bottom-margin center">
//                    Activities and Interests: For example, Yoga, Visiting restaurants</p>
//                  <input class="hobby ui-autocomplete-custom text-box-long"
//                        name="interest"
//                       value="'.htmlentities(trim($interests['interest'])).'"
//                          id="interest" >
//                </div>';
echo '         <div class="input-form center more-top-margin-3x">
                 <p class=
                  "box-input-label less-bottom-margin center">
                   Foreign Languages: For example, Spanish, Manadrin</p>
                 <input class="hobby ui-autocomplete-custom text-box-long"
                         type="text"
                      name="languages"
                      value="'.htmlentities(trim($interests['languages'])).'"
                      id="languages" >
               </div>';
echo '                <div class="input-form center more-top-margin-3x">
                 <p class=
                  "box-input-label less-bottom-margin center">
                   Computer Skills: For example, Microsoft Word, Excel</p>
                 <input class="hobby ui-autocomplete-custom text-box-long"
                      type="text"
                      name="computer_skill"
                      value="'.htmlentities(trim($interests['computer_skill'])).'"
                      id="computer" >
               </div>';
echo '         <div class="input-form center more-top-margin-3x">
                 <p class=
                  "box-input-label less-bottom-margin center">
                   Publication</p>
                 <input class="hobby ui-autocomplete-custom text-box-long"
                      type="text"
                      name="publication"
                      value="'.htmlentities(trim($interests['publication'])).'"
                      id="publication" >
               </div>';
echo '         <div class="input-form center more-top-margin-3x">
                 <p class=
                  "box-input-label less-bottom-margin center">
                   Licenses</p>
                 <input class="hobby ui-autocomplete-custom text-box-long"
                      type="text"
                      name="licenses"
                      value="'.htmlentities(trim($interests['licenses'])).'"
                      id="license">
               </div>
    </div>';
?>
<h4 class="more-top-margin-3x headline-green center"><span class="link-info">Save changes</span>
      <button class="button-submit" onclick="return doValidateActivities();">Save</button>
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
        count_hobby =      Number("<?php echo $count_hobby ?>");
        hobby_array = makeHobbyArray(count_hobby);
        hobby_removed =  0;
        last_text_box =  '';
        test_box =  '';
        audit_array = [];
        audit_list = {};
        //When a new skill is added, the immediate previous skill is checked
        //for offensive language.
        $('#addHobby').click(function(event){
            event.preventDefault();
            window.console && console.log("Adding hobby");
            if(count_hobby - hobby_removed + 1 > 12) {
                 //triggerAlert('Maximum of twelve hobbies exceeded', replace=true);
                 return;
            } else {
                 count_hobby = count_hobby + 1;
            }
            window.console && console.log("Adding Hobby-"+count_hobby);
        //  Fill out the template block within the html code
            var source = $('#hobby-template').html();
            $('#hobby_fields').append(source.replace(/@COUNT@/g, count_hobby));
            $(function(){
              $('.hobby').click(function(e){e.preventDefault();}).click();
            });
            $(document).on('click', '.hobby', 'input[type="text"]', function(){
                var hobby_id = $(this).attr("id");
                var term_interest = document.getElementById(id=hobby_id).value;
                if(term_interest.length > 1) {
                  $.getJSON('hobby.php?ter'+'m='+term_interest, function(data) {
                     window.console && console.log(' Data returned: '+data);
                     var ys = data;
                     $('.hobby').autocomplete({ source: ys });
                  });
                }
            });
            var field = "hobby"+count_hobby;
            hobby_array.push(field);
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
});
</script>
</body>
</html>
