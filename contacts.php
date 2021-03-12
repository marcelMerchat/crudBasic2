<?php
// 'contacts.php'
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
// Only approved users may enter contact information.
if ($_SESSION['contact_info'] == 0) {
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
$em = htmlentities(trim($profile['email']));
$ph = htmlentities(trim($profile['phone']));
$linkedin = htmlentities(trim($profile['linkedin']));

// Check for initial GET request without (or without) post information
// Only required entries are the first and laat names and the email.
if (isset($_POST['email']) ) {
    unset($_SESSION['error']);
    unset($_SESSION['success']);
    unset($_SESSION['message']);
    $_SESSION['message'] = ' ';
    $profileid = $_SESSION['profile_id'];
    $profileInserted = insertEmail($pdo,$IsUpdate=true);
    if($profileInserted===false){
           $_SESSION['error'] = $_SESSION['error']
              . ' Could not change contact or email. Please try again.';
           header('Location: contacts.php?profile_id='.$_POST['profile_id']);
           return;
    }
    insertPhone($pdo);
    insertLinkedin($pdo);
    // Delete old contact entries; insert new list
    $stmt = $pdo->prepare('DELETE FROM ContactList WHERE profile_id = :pid');
    $stmt->execute(array(':pid' => $profileid));
    insertContactList($pdo);
    // foreach ($_POST as $key => $value) {
    //      $_SESSION['message'] = $_SESSION['message']
    //      ."Field ".htmlspecialchars($key)." is ".htmlspecialchars($value)."<br>";
    //      store_error_messages();
    //  }
    header("Location: index.php");
    return;
}
?>

<!--            VIEW                -->

<!DOCTYPE html>
<html>
<head>
<title>Contacts</title>
<?php
   require_once 'header.php';
   // Get contacts from database
   $contacts = loadContactList($profileid,$pdo);
?>
<style type="text/css">
    div.box-input-label-wide {
        width: calc(100% - 18em);
    }
</style>
</head>
<body>
    <div class="center" id="main">
    <h4 class="less-bottom-margin center">Editing Contact Information</h4>

    <div id="dialog-confirm" title="New Message: ">
      <p id="message_field">
        <!-- Alert message is placed in the span tag below. -->
        <script type="text" id="message_template">
            <span></span>
        </script>
        <?php
              flashMessages();
        ?>
      </p>
    </div>
<?php
          flashMessages();
?>
<h5 class="less-bottom-margin left">This final editing screen is for contact
       information that is displayed at the top of the resume.
       When finished, click the 'Save' button at the bottom.</h5>
<form method="post" name="form1">
          <!-- hidden unchangeable information -->
          <input type="hidden" name="profile_id" value ="<?= $profileid ?>" />
          <p><input type="hidden" name="user_id" value ="<?= $uid ?>" id="userid"/></p>
          <!-- Modifiable Information -->

          <div class="container-form-entry more-top-margin-3x">
            <div class="less-bottom-margin less-top-margin box-input-label-wide inline-block right">E-mail &nbsp;
            </div><div class="less-top-margin less-bottom-margin profile-input-form">
              <input class="text-box" type="text" name="email" value="<?= $em ?>" id="em">
            </div></div>

          <div class="container-form-entry more-top-margin-3x">
              <div class="less-bottom-margin less-top-margin box-input-label-wide inline-block right">Phone number &nbsp;
              </div><div class="less-top-margin less-bottom-margin profile-input-form inline-block">
                <input class="text-box" type="text" name="phone" value="<?= $ph ?>" id="ph">
              </div></div>

          <div class="container-form-entry more-top-margin-3x">
                  <div class="less-bottom-margin less-top-margin box-input-label-wide inline-block right">www.linkedin.com/in/ &nbsp;
                  </div><div class="less-top-margin less-bottom-margin profile-input-form inline-block">
                    <input class="text-box" type="text" name="linkedin" value="<?= $linkedin ?>" id="ph">
                  </div></div>

<!-- End of contact information -->
<!-- Contacts -->
<h4 class="more-top-margin-3x less-bottom-margin center">Contacts</h4>
<!-- the id addContact point to a JavaScript function -->
<div class="less-top-margin less-bottom-margin" id="contact_fields">
<?php
  $count_contact = 0;
  foreach($contacts as $contact){
           $count_contact++;
echo '<div id="contact'.$count_contact.'" >
                 <div class="less-top-margin less-bottom-margin input-form center">
                   <input class="skill ui-autocomplete-custom text-box-long"
                        name="contact'.$count_contact.
                   '" value="'.htmlentities(trim($contact['info'])).'"
                   id="contact_id'.$count_contact.'" >
                 </div>
                   <p class="less-top-margin box-input-label less-bottom-margin center">Delete preceding contact:
                   <input class="click-plus" type="button" value="-"
                          onclick = "deleteContact(\'#contact'.$count_contact.'\',\'contact_id'.$count_contact.'\'); return false">
                   </p>
      </div>';
  }
?>
</div>
<!-- Added html for contacts -->
<script id="contact-template" type="text">
    <div id="contact@COUNT@">
    <div class="less-top-margin less-bottom-margin input-form center">
        <input class="skill ui-autocomplete-custom text-box-long center"
           name="contact@COUNT@" value='<?= htmlentities("") ?>' id="contact_id@COUNT@"/>
    </div>
        <p class="less-top-margin box-input-label less-bottom-margin center"> Delete preceeding contact:
        <input type="button" class="click-plus" value="-"
               onclick="deleteContact('#contact@COUNT@','contact_id@COUNT@'); return false;"/></p>
    </div>
</script>

<h5 class="more-top-margin-3x center">Add Contact: <button class="click-plus less-bottom-margin" id="addContact">+</button></h5>
<!-- 'addContact' is an argument of a JavaScript
 function object [ $('#addContact') ] described below. -->
<!-- end of contacts -->

<h4 class="more-top-margin-3x headline-green center"><span class="link-info">Save changes</span>
      <button class="button-submit" onclick="return doValidateContacts();">Save</button>
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
        count_contact =   Number("<?php echo $count_contact ?>");
        contact_array = makeContactArray(count_contact);
        contact_removed =  0;
        last_text_box =  'em';
        test_box =  'em';
        audit_array = ["em","ph"];
        audit_list = {"em": -1,"ph": -1 };
        // When a new contact is added, the immediate previous contact is checked
        // for offensive language.
        $('#addContact').click(function(event){
            event.preventDefault();
            window.console && console.log("Adding contact");
            if(count_contact - contact_removed + 1 > 12) {
                 triggerAlert('Maximum of twelve contact exceeded', replace=true);
                 return;
            } else {
                 count_contact = count_contact + 1;
            }
            window.console && console.log("Adding Contact-"+count_contact);
        //  Fill out the template block within the html code
            var source = $('#contact-template').html();
            $('#contact_fields').append(source.replace(/@COUNT@/g, count_contact));
            $(document).on('click', '.skill', 'input[type="text"]', function(){
                var contactid = $(this).attr("id");
                var term_contact = document.getElementById(id=contactid).value;
                $.getJSON('contacts.php?ter'+'m='+term_contact, function(data) {
                     window.console && console.log(' Data returned: '+data);
                     var ys = data;
                     $('.skill').autocomplete({ source: ys });
                });
            });
            var field = "contact_id"+count_contact;
            contact_array.push(field);
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
                var source1 = $('#activity-template1').html();
                var location1 =  "#" + "activity_fields_a" + job_num;
                $(location1).append(source1.replace(/@COUNT@/g, activity_num));
                $(location1).append('<input type="hidden" \
                     name="activity_position_tag' + activity_num + '" \
                    value=' + job_num +
                     ' id="activity_parent' + activity_num + '"/>');
                var source2 = $('#activity-template2').html();
                $(location1).append(source2.replace(/@COUNT@/g, activity_num));
                window.console && console.log(
                   "Added Activity-"+ activity_num + " for Job-" + location);
                var field = "activity"+activity_num;
                activity_array.push(field);
       });
});
</script>
</body>
</html>
