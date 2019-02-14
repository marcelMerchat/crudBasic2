<?php
require_once('pdo.php');
require_once('util.php');
session_start();
// user is not logged-in
if ( ! isset($_SESSION['user_id']))  {
      header('Location: index.php');
      return; // or die('ACCESS DENIED');
}
// If the user requested cancel, go back to view.php
if ( isset($_POST['cancel']) ) {
    $msg = 'taking shortcut';
    header('Location: index.php');
    return;
}
// The profile does not exist yet; the profile id will be added below.
// The logged-in user has submitted his profile.
if (isset($_POST['first_name']) || (isset($_POST['last_name']) &&
    isset($_POST['email'])) || isset($_POST['profession']) ||
    isset($_POST['goal']) ) {

  if (isset($_POST['first_name']) && isset($_POST['last_name']) &&
    isset($_POST['email']) && isset($_POST['profession']) &&
    isset($_POST['goal']) ) {
    //echo 'starting post validation';
    $msg = validateProfile();
    if (is_string($msg) ) {
      $_SESSION['error'] = $msg;
      header( 'Location: add.php' );
      return;
    }
    $msg = validateSkill();
    if (is_string($msg) ) {
       $_SESSION['error'] = $msg;
       header('Location: add.php');
       return;
    }
    $msg = validateEducation();
    if (is_string($msg) ) {
       $_SESSION['error'] = $_SESSION['error'].$msg;
       header('Location: add.php');
       return;
    }
    $msg = validatePos();
    if (is_string($msg) ) {
          $_SESSION['error'] = $_SESSION['error'].$msg;
          header('Location: add.php');
          return;
    }
    $fn = $_POST['first_name'];
    $ln = $_POST['last_name'];
    $em = $_POST['email'];
    $prof = $_POST['profession'];
    $goal = $_POST['goal'];
    unset($_SESSION['error']);
    $fn = filterWord($pdo, $fn);
    $ln = filterWord($pdo, $ln);
    $em = filterWord($pdo, $em);
    $prof = filterWord($pdo, $prof);
    $goal = filterWord($pdo, $goal);
    if(isset($_SESSION['error']) && $_SESSION['error'] == "offensive"){
        $_SESSION['error'] = $_SESSION['message'].' Word not recognized, please try again.';
        header("Location: add.php");
        return;
    }
    $sql = "INSERT INTO Profile (user_id, first_name, last_name, email, profession, goal) VALUES ( :uid, :fn, :ln, :em, :prof, :goal )";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
          ':uid' => $_SESSION['user_id'], ':fn' => $fn,
          ':ln'  => $ln,  ':em' => $_POST['email'],
          ':prof'  => $_POST['profession'], ':goal' => $_POST['goal']) );
    $profile_id = $pdo->lastInsertId();
    unset($_SESSION['message']);
    insertSkillSet($pdo, $profile_id);
    insertEducations($pdo, $profile_id);
    insertPositions($pdo, $profile_id);
    if(isset($_SESSION['message'])) {
        $_SESSION['error'] = 'Language error: '.$_SESSION['message'];
    }
    header("Location: index.php");
    return;
  } else {
    echo 'profile incomplete';
  }
} else {
    //echo 'Nothing posted yet';
}
?>

<!-- model-view-controller -->
<!--         VIEW             -->

<!DOCTYPE html>
<html>
<head>
<title>Add Profile</title>
<?php
    require_once 'header.php';
    //require_once 'jquery.php';
    //loadMobilityStyles();
?>
<!-- <script src="script.js"></script> -->
</head>
<body>
<div class="content" id="main">
<?php
   flashMessages();
?>
<h3 class="center">Adding Profile</h3>
<form method="post">
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
          <textarea class="goal-box" rows="5" name="goal" id="gl"><?= htmlentities("") ?></textarea>
    </div>
    <!-- End of profile information -->
    <!-- Skills -->
                         <!-- 'addSkill' is an argument of a JavaScript
                        function object [ $('#addSkill') ] described below. -->
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
        <!--  Grab some HTML with hot spots and insert in the DOM
              'edu-template' is the target id of the JavaScript function
               function object $('#addEdu') described below. -->
        <h3 class="more-top-margin-2x center">Education</h3>
        <div class="less-top-margin less-bottom-margin centered-row-layout" id="edu_fields">
        <script id="edu-template" type="text">
        <div class="form-background div-form-group border-top-bottom more-bottom-margin" id="edu@COUNT@">
            <div class="container-form-entry">
                <div class="less-bottom-margin short-input-label"> Year </div>
                <div class="less-bottom-margin less-top-margin short-input-form">
                     <input class="year-entry-box" type="text" name="edu_year@COUNT@" value="" />
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
       <!-- The ids for these profile information fields are used by
        JavaScript function doValidate() in file script.js to check if
        they were completed before posting to the website server where
        they are rechecked using a PHP rountine in util.php. -->
</form> <!-- end of class form -->
</div> <!-- end of class content and id main -->
<script type="text/javascript">
$(document).ready( function () {
     window.console && console.log('Document ready called');
      // fetch variables from the php function environment. However they are //
      // always set equal equal to one before adding a new profile to the
      // database.
      var countPosition = 0;
      countEdu =  0;
      // The J prefix identifies JavaScript variables when variable names
      // appear in the PHP code above.
      countSkill = 0;
      skillRemoved =  0;
      eduRemoved =  0;
      positionRemoved =  0;
      window.console && console.log('Hello world');
      //$.getJSON('jsonLanguage.php', function(data) {
        //window.console && console.log('Hello world');
        //window.console && console.log(data.first);
        //var y = data;
        // $.each(data, function(i, field){
        //   $('#gl').append(field + " ");
        //   $('#gl').html("data dot first");
        //  });
      //});
      $(document).on('click', '.goal-box', 'input[type="text"]', function(){
            var p = $(this);
            var goalId = $(this).attr("id");
            termgl = document.getElementById(id=goalId).value;
            window.console && console.log(termgl+goalId);
           //var value="This is a string";
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
                  p.css("background-color", 'rgb(249,255,185)');
                  p.css("borderWidth", "1px");
                  p.css("border-color", "886600");
                  //border: 1px solid #008800;
                  //background-color: #eef;
                }
              });
          }      //$('#gl').html.append("data dot first");
      });
      $(document).on('click', '.text-box', 'input[type="text"]', function(){
            var p = $(this);
            var goalId = $(this).attr("id");
            termgl = document.getElementById(id=goalId).value;
            window.console && console.log(termgl+goalId);
            var len = termgl.length;
            if( len > 0){
            $.getJSON('jsonLanguage.php?ter'+'m='+termgl, function(data) {
                window.console && console.log(data.first);
                if(data.first=='bad'){
                    var info = p.val();
                    p.val(info+': Questionable language detected . . . ');
                    p.css("background-color", "bisque");
                    p.css("borderWidth", "2px");
                    p.css("border-color", "#00EEDD");
                } else {
                  p.css("background-color", 'rgb(249, 255, 185)');
                  p.css("borderWidth", "1px");
                  p.css("border-color", 'rgb(88,66,00)');
                }
              });
          }      //$('#gl').html.append("data dot first");
      });
      $(document).on('click', '.position-box', 'input[type="text"]', function(){
           var p = $(this);
           var posId = $(this).attr("id");
           termPos = document.getElementById(id=posId).value;
           window.console && console.log(termPos+posId);
           //var value="This is a string";
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
                    p.css("border-color", "#00EEDD");
                } else {
                  p.css("background-color", 'rgb(249, 255, 185)');
                  p.css("borderWidth", "1px");
                  p.css("border-color", "#886600");
                  //border: 1px solid #008800;
                  //background-color: #eef;
                }
              });
          }      //$('#gl').html.append("data dot first");
      });
          //     var textboxId = $(this).attr("id");
          //     txtentry = document.getElementById(id=textboxId).value;
          //     if( len > 0){
          //         $.getJSON('checkLanguage.php?txtentr'+'y='+txtentry, function(data) {
          //             var y = data.first;
          //             $.each(data, function(i, field){
          //                 $(#gl".goal-box-layout").append(field + " ");
          //             });
          //         });
          //     }              //$('.goal-box').autocomplete({ source: y });
          //   //document.getElementById(textboxId).innerHTML = txtentry + y;
          //     document.getElementById(textboxId).innerHTML = txtentry + " new";
          //     document.getElementById(textboxId).style.color = "#FF0000";
          //   //$.getJSON("demo_ajax_json.js", function(result){
          //         //$.each(result, function(i, field){
          //           //$("div").append(field + " ");
          //         //});
          //     //});
          // //goals = document.getElementById('gl').value;
          // //if (firstname == null || firstname == "" ) {
          //   //goals == null || goals == "" )
          //   //    alert("All fields must be filled out");
          //     //  return false;
          // //}
          //   return true;
        //} catch(e) {
             //return false;
        //}
        $('#addSkill').click(function(event){
            event.preventDefault();
            // Stored value of skillRemoved is pre-incremented by one count
            if( countSkill - skillRemoved + 1 > 9){
                alert('Maximum of nine skills exceeded');
                //JcountSkill = JcountSkill - 1;
                return;
            } else {
                countSkill = countSkill + 1;
            }
            window.console && console.log("Adding skill-"+countSkill);
            // Fill out the template block within the html code
            var source = $('#skill-template').html();
            $('#skill_fields').append(source.replace(/@COUNT@/g, countSkill));
              alert('Added skill, skill count increases to '+ (countSkill - skillRemoved));
            });
            $(document).on('click', '.skill', 'input[type="text"]', function(){
                var skillId = $(this).attr("id");
                var termskill = document.getElementById(id=skillId).value;
                $.getJSON('skill.php?ter'+'m='+termskill, function(data) {
                     //var y = data.Result;
                     var ys = data;
                     $('.skill').autocomplete({ source: ys });
                });
            });
         $('#addEdu').click(function(event) {
              event.preventDefault();
              if( countEdu - eduRemoved + 1 > 9){
                  alert('Maximum of nine education entries exceeded');
                  //countPosition = countPosition - 1;
                  return;
              } else {
                  countEdu = countEdu + 1;
              }
              var source = $('#edu-template').html();
              window.console && console.log("Adding education");
              $('#edu_fields').append(source.replace(/@COUNT@/g, countEdu));
              // auto-completion handler for new additions
              alert('Adding education entry, now there are '+ (countEdu - eduRemoved) + ' entries.');
              //var y = "school.php";
              $(document).on('click', '.school', 'input[type="text"]', function(){
                      var eduId = $(this).attr("id");
                      term = document.getElementById(id=eduId).value;
                      //var value="This is a string";
                      var len = term.length;
                      if( len > 0){
                          $.getJSON('school.php?ter'+'m='+term, function(data) {
                              var y = data;
                              $('.school').autocomplete({ source: y });
                          });
                      }
              });
        });  //end of addedu
        $('#addPos').click(function(event){
            event.preventDefault();
            // Stored value of positionRemoved is pre-incremented by one count
            if( countPosition - positionRemoved + 1 > 5){
                alert('Maximum of five positions exceeded');
                return;
            } else {
                countPosition = countPosition +   1;
            }
            window.console && console.log("Adding Position-"+countPosition);
            $('#position_fields').append(
               '<div class="form-background div-form-group border-top-bottom more-top-margin left" id="position' + countPosition + '\" > \
                   <div class="container-form-entry"> \
                      <div class="year-form-entry"> \
                          <div class="less-bottom-margin less-top-margin year-input-label"> Start Year \
                          </div><div class="less-top-margin less-bottom-margin short-input-form"> \
                              <input class="year-entry-box box-size" type="text" name="yearStart' + countPosition + '"' + ' id="ys'+ countPosition + '\" /> \
                          </div> \
                      </div> \
                      <div class="year-form-entry indent"> \
                          <div class="less-bottom-margin less-top-margin year-input-label"> Final Year \
                          </div><div class="less-top-margin less-bottom-margin short-input-form"> \
                              <input class="year-entry-box" type="text" name="yearStart' + countPosition + '"' + ' id="yl'+ countPosition + '\" /> \
                          </div> \
                      </div> \
                  </div> \
                  <div class="more-top-margin less-bottom-margin"> Organization \
                  </div><div class="less-top-margin goal-box-layout"> \
                      <input class="text-box" type="text" name="org'+countPosition+'" /> \
                  </div> \
                  <div class="more-top-margin less-bottom-margin">Description \
                  </div><div class="less-bottom-margin goal-box-layout"> \
                     <textarea class="position-box" name="desc'+countPosition+
                         '\" rows = "8" id=\"positionDesc' + countPosition +
                         '\"> ' +
                    '</textarea> \
                  </div> \
                  <div class="less-top-margin less-bottom-margin more-left-margin"> \
                  Delete this position: \
                  <input type="button" class="click-plus" value="-" \
                  onclick="$(\'#position' + countPosition +
                        '\').remove(); removePosition(countPosition, positionRemoved);return false;"/></div> \
              </div>');
              alert('Adding position, now there are '+ (countPosition - positionRemoved) + ' positions.');
          });
});
</script>
</body>
</html>
