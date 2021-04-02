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
if (isset($_POST['profile_id']) )  {

    unset($_SESSION['error']);
    unset($_SESSION['success']);
    unset($_SESSION['message']);
    $_SESSION['message'] = ' ';
    $profileid = $_SESSION['profile_id'];
    // Certificates
    // Delete old certificate entries; recreate new list
    $stmt = $pdo->prepare('DELETE FROM Certificates WHERE profile_id = :pid');
    $stmt->execute(array(':pid' => $profileid));
    insertCertificates($profileid,$pdo);

    // foreach ($_POST as $key => $value) {
    //    $_SESSION['message'] = $_SESSION['message']
    //      ."Field ".htmlspecialchars($key)." is ".htmlspecialchars($value)."<br>";
    //      store_error_messages();
    // }
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
   $Certificates = loadCertif($profileid,$pdo);
   // $sql = 'SELECT Certificates.year, Certificates.award_link as award, Award.name As degree,
   //      Institution.name As institution, Institution.provider As provider
   //      FROM Institution LEFT JOIN Certificates
   //         ON Certificates.institution_id = Institution.institution_id
   //      LEFT JOIN Award
   //         ON Certificates.award_id = Award.award_id
   //      WHERE Certificates.profile_id = :pid';
   // $stmt = $pdo->prepare($sql);
   // $stmt->execute(array(':pid' => $profileid) );
   // $retrieved = $stmt->fetchALL(PDO::FETCH_ASSOC);
   //   $_SESSION['message'] = 'Loading certifications '
   //     .' Profile-'.$profileid;
   // store_error_messages();
   // $Certifications = array_values($retrieved);
   // var_dump($Certifications);
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
          <!-- Certificates and Internet Education -->
<h4 class="more-top-margin-3x more-bottom-margin-1x center">Certificates</h4>
<div class="less-top-margin less-bottom-margin centered-row-layout" id="certif_fields">
  <?php
  //var_dump($Certificates);
  //print_r($profileid);
  $count_certif = 0;
  foreach($Certificates as $certificate){
        //var_dump($certificate);
        $count_certif++;
        echo '<div class="form-background div-form-group border-top-bottom
                 more-bottom-margin more-top-margin-3x" id="certif_div'.$count_certif.'">
                <div class="container-form-entry">
                    <div class="left div-year-group">Year
                     <input class="year-entry-box"
                             type="text" name="certif_year'.$count_certif.'"
                            value="'.$certificate['year'].'"
                               id="certif_year'.$count_certif.'" />
                    </div>
                </div><div class="container-form-entry less-bottom-margin">
                    <div class="less-bottom-margin box input-label less-top-margin short-input-label">Internet Provider</div>
                    <div class="less-bottom-margin less-top-margin input-form">
                      <input class="school ui-autocomplete-custom
                                    text-box-long less-bottom-margin"
                             type="text"
                             name="internet_edu'.$count_certif.'" value='.$certificate['provider'].' id="internet_edu'.$count_certif.'" />
                    </div>
                </div>
                <div class="container-form-entry">
                    <div class="less-bottom-margin less-top-margin
                                                      short-input-label">School
                    </div><div class="less-top-margin less-bottom-margin
                                                                 input-form">
                        <input class="school ui-autocomplete-custom text-box-long" type="text"
                            name="certif_school'.$count_certif.'" value="'
                            .htmlentities(trim($certificate['institution']))
                            .'" id="certif_school'.$count_certif.'">
                    </div>
                </div><div class="container-form-entry">
                    <div class="less-bottom-margin less-top-margin">
                      <p class="margin-bottom-small">Certificate Name</p>
                      <p class="margin-top-small margin-bottom-small small">
                           Example: Auto Mechanics
                      </p>
                    </div><div class="less-top-margin less-bottom-margin input-form">
                        <input class="award text-box-long" type="text" name="certif_award'.$count_certif.
                        '" value="'.htmlentities(trim($certificate['degree'])).'" id="certif'.$count_certif.'">
                    </div><div class="less-top-margin less-bottom-margin input-form">
                        <p class="margin-bottom-small">Certificate link; for example, https://www.myschool.org/certificate12345</p>
                        <input class="ui-autocomplete-custom award text-box-long" type="text" name="certif_link'.$count_certif.
                        '" value="'
                        .htmlentities(trim($certificate['award_link']))
                        .'" id="certif_link'.$count_certif.'">
                    </div>
                </div><div>
                    <p>Delete this certificate <input
                       class="click-plus" type="button" value="-"
                        onclick =
 "deleteCertif(\'#certif_div'.$count_certif.'\',\'internet_edu'.$count_certif.'\',\'certif_school'.$count_certif.'\',\'certif'.$count_certif.'\',\'certif_link'.$count_certif.'\'); return false;" />
            </div>
            </div>';
    }
?>
<script type="text" id="certif-template">
    <div class="form-background div-form-group border-top-bottom more-bottom-margin more-top-margin-3x left" id="certif_div@COUNT@">
    <div class="left div-year-group">Year <input class="year-entry-box"
             type="text" name="certif_year@COUNT@"
             id="certif_year@COUNT@" />
    </div>
    <div class="container-form-entry less-bottom-margin">
        <div class="less-bottom-margin box input-label less-top-margin short-input-label">Internet Provider</div>
        <div class="less-bottom-margin less-top-margin input-form">
          <input class="school ui-autocomplete-custom
                        text-box-long less-bottom-margin"
                 type="text"
                 name="internet_edu@COUNT@" id="internet_edu@COUNT@" />
        </div>
    </div>
    <div class="container-form-entry less-bottom-margin">
        <div class="less-bottom-margin box input-label less-top-margin short-input-label">School </div>
        <div class="less-bottom-margin less-top-margin input-form">
          <input class="school ui-autocomplete-custom
                        text-box-long less-bottom-margin"
                 type="text"
                 name="certif_school@COUNT@" value='' id="certif_school@COUNT@" />
        </div>
    </div>
    <div class="container-form-entry less-bottom-margin">
        <div class="less-bottom-margin less-top-margin left">
          <p class="margin-bottom-small">Certificate Name</p>
          <p class="margin-top-small margin-bottom-small small">
             Example: Auto Mechanics
          </p>
        </div>
        <div class="less-bottom-margin less-top-margin input-form">
          <input class="award ui-autocomplete-custom text-box-long"
                 type="text" size="80" name="certif_award@COUNT@"
                 id="certif@COUNT@" />
        </div>
        <div class="less-top-margin less-bottom-margin input-form">
            <p class="margin-bottom-small">Certificate link; for example, https://www.myschool.org/certificate12345</p>
            <input class="award text-box-long" type="text"
              name="certif_link@COUNT@" id="certif_link@COUNT@"/>
        </div>
    </div>
    <div class="less-bottom-margin">
        <p class="less-top-margin less-bottom-margin"> Delete this certificate entry:
            <input type="button" class="click-plus" value="-"
                onclick="deleteCertif('#certif_div@COUNT@','internet_edu@COUNT@','certif_school@COUNT@','certif@COUNT@','certif_link@COUNT@'); return false;"/>
        </p>
    </div>
    </div>
</script>
</div>
<h5 class="more-top-margin-3x center">Add certificate: <button class="click-plus less-bottom-margin button-small" id="addCertif" >+</button></h5>
<!-- End of Internet certificate         -->
<h4 class="more-top-margin-3x headline-green center"><span class="link-info">Save changes</span>
      <button class="button-submit" onclick="return validateCertifications();">Save</button>
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
        count_certif =    Number("<?php echo $count_certif ?>");
        certif_array = makeCertificationArray(count_certif);
        certif_school_array = makeCertificationSchoolArray(count_certif);
        certif_edu_provider_array = makeCertificationEduProviderArray(count_certif);
        certif_lnk_array = makeCertificationLinkArray(count_certif);
        window.console && console.log('The certification array is ' + certif_array);
        certif_removed = 0;
        last_text_box =  '';
        test_box =  '';
        audit_array = [];
        audit_list = {};
        $('#addCertif').click(function(event) {
            event.preventDefault();
            if( count_certif - certif_removed + 1 > 9){
              triggerAlert('Maximum of nine certifications exceeded', replace = true);
              return;
            } else {
              count_certif = count_certif + 1;
            }
            var source = $('#certif-template').html();
            triggerAlert("Adding Certification", true );
            $('#certif_fields').append(source.replace(/@COUNT@/g, count_certif));
            triggerAlert("Certification appended", true );
            $(function(){
              $('.school').click(function(e){e.preventDefault();}).click();
            });
            $(document).on('click', '.school', 'input[type="text"]', function(){
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
               $(document).on('click', '.award', 'input[type="text"]', function(){
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
            var field = "certif_school"+count_certif;
            certif_school_array.push(field);
            var field = "internet_edu"+count_certif;
            certif_edu_provider_array.push(field);
            var award_field = "certif"+count_certif;
            certif_array.push(award_field);
            var award_link_field = "certif_link"+count_certif;
            certif_lnk_array.push(award_link_field);
        });  //end of addedu
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
