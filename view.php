<?php
// 'view.php'
require_once "pdo.php";
require_once "util.php";
session_start();
?>
<!--  VIEW or HTML code for model-view-controller  -->
<!DOCTYPE HTML>
<html>
<head>
    <title>View Resume</title>
<?php
    require_once 'header.php';
?>
<style type="text/css">
    body {
      line-height: normal;
    }
    #main {
        left: 4%;
        right: 4%;
        width: 92%;
        border: 0px solid #008800;
    }
    div.edu-label {
        display: inline-block;
        vertical-align: top;
        text-align: left;
        width: 1.4rem;
        height: 1.1em;
        border: 0px solid #008800;
        padding: 0px;
        margin-top: -4px;
        margin-bottom: 0px;
    }
    div.edu-year-label {
        display: inline-block;
        vertical-align: top;
        text-align: left;
        width: 3.6rem;
        height: 1.1em;
        border: 0px solid #008800;
        padding: 0px;
        margin-top: 0px;
        margin-bottom: 0px;
    }
    div.job-label {
      display: inline-block;
      vertical-align: top;
      text-align: left;
      width: 5.9rem;
      height: 1.1em;
      border: 0px solid #008800;
      padding: 0px;
      margin-top: 0px;
      margin-bottom: 0px;
    }
    div.job-desc {
      display: inline-block;
      box-sizing: border-box;
      text-align: left;
      width: 99%;
      height: 1.1em;
      border: 0px solid #008800;
      padding: 0px;
      margin-top: 2px;
      margin-bottom: 0px;
    }
    div.container-edu-info {
      width: 30em;
      max-width: 100%;
      box-sizing: border-box;
      width: 100%;
      border: 0px solid black;
      padding-left: 1px;
      padding-right: 1px;
      padding-top: 0px;
      padding-bottom: 0px;
      margin-top: 4px;
      margin-bottom: 15px;
    }
    div.edu-info {
        display: inline-block;
        text-align: left;
        box-sizing: border-box;
        border: 0px solid #888800;
        margin-top: 0px;
        margin-bottom: 0px;
        width: 75%;
    }
    div.job-info {
        display: inline-block;
        text-align: left;
        box-sizing: border-box;
        border: 0px solid #888800;
        margin-top: 0px;
        margin-bottom: 0px;
        word-break: break-word;
        width: calc(100% - 6rem);
        min-width: 180px;
        border: 0px solid #888800;
    }
    .edu-row1 {
       border: 0px solid #008800;
       margin: 0px;
       text-align: left;
    }
    .edu-row2 {
       border: 0px solid #008800;
       margin: 0px;
       text-align: left;
    }
    .more-top-margin {
        margin-top: 8px;
    }
    .more-bottom-margin {
        margin-bottom: 10px;
    }
    .more-padding-top-4px {
        margin-top: 4px;
    }
    .job-title {
        color: #008888;
    }
    h4 {
        font-size: 1.2rem;
    }
    ul {
       margin-top: 0px;
    }
    li {
      padding-bottom: 0px;
      margin-bottom: 0px;
    }
</style>
</head>
<body>
  <div class="center" id="main">
        <br />
<?php
    $profileid = $_GET['profile_id'];
    $stmt = $pdo->prepare('SELECT first_name, last_name, phone,
                  email, profession, goal FROM Profile WHERE profile_id = :pid');
    $stmt->execute(array(':pid' => $_GET['profile_id']));
    $row =  $stmt->fetch(PDO::FETCH_ASSOC);
        echo '<h4 class="center big more-bottom-margin">'.htmlentities($row['first_name']).' '.
                              htmlentities($row['last_name']).'
              </h4><h4 class="center more-bottom-margin">'.
                               htmlentities($row['profession']).
              '</h4>';
    $sqlContactCount = 'SELECT COUNT(*) FROM ContactList WHERE profile_id = :pid';
    $stmtCount = $pdo->prepare($sqlContactCount);
    $stmtCount->execute(array(':pid' => $_GET['profile_id']) );
    $contactrows = $stmtCount->fetch(PDO::FETCH_ASSOC);
    $contact_cnt = array_values($contactrows)[0];

    //if( $length !== 0) {
    if( $contact_cnt  > 0) {
        $sqlContactList = 'SELECT contact_id FROM ContactList WHERE profile_id = :pid';
        $stmt_contactList = $pdo->prepare($sqlContactList);
        $stmt_contactList->execute(array(':pid' => $_GET['profile_id']));
        $contactList = $stmt_contactList->fetchALL(PDO::FETCH_ASSOC);
      // school id list
        $contact_list_rows = $contactList;
        $length_contact_list_rows = count($contact_list_rows);

        echo '<p class="center small less-bottom-margin">e-mail: '
             .htmlentities($row['email']).' | phone: '.htmlentities($row['phone']);
        for ($i = 0; $i < $length_contact_list_rows; $i++){
           $contact_id = $contact_list_rows[$i]['contact_id'];
           $sqlContact = 'SELECT info FROM Contact WHERE contact_id = :iid';
           $stmt_contact = $pdo->prepare($sqlContact);
           $stmt_contact->execute(array(':iid' =>   $contact_id));
           $contactrow = $stmt_contact->fetch(PDO::FETCH_ASSOC);
           //$skill = array_values($skillrow)[0];
           echo ' | '.htmlentities($contactrow['info']);
        }
        echo '</p>';
    }



        //echo '<br />';
        echo '<h4 class="more-top-margin-3x">Goals</h4>';
        echo '<p class="job-description-box justify">'.htmlentities($row['goal']).'</p>';

        $sqlSkillCount = 'SELECT COUNT(*) FROM SkillSet WHERE profile_id = :pid';
        $stmtCount = $pdo->prepare($sqlSkillCount);
        $stmtCount->execute(array(':pid' => $_GET['profile_id']) );
        $rowcount = $stmtCount->fetch(PDO::FETCH_ASSOC);
        $cnt = array_values($rowcount)[0];
        //if( $length !== 0) {
        if( $cnt > 0) {
            $sqlSkillSet = 'SELECT skill_id FROM SkillSet WHERE profile_id = :pid';
            $stmt_skillSet = $pdo->prepare($sqlSkillSet);
            $stmt_skillSet->execute(array(':pid' => $_GET['profile_id']));
            $skillSet = $stmt_skillSet->fetchALL(PDO::FETCH_ASSOC);
          // school id list
            $rows = $skillSet;
            $length = count($rows);

            echo '<h4 class="less-bottom-margin">Job Skills</h4>';
            echo '<ul class="less-top-margin">';
            for ($i = 0; $i < $length; $i++){
               $skill_id = $rows[$i]['skill_id'];
               $sqlSkill = 'SELECT name FROM Skill WHERE skill_id = :iid';
               $stmt_skillname = $pdo->prepare($sqlSkill);
               $stmt_skillname->execute(array(':iid' =>   $skill_id));
               $skillrow = $stmt_skillname->fetch(PDO::FETCH_ASSOC);
               //$skill = array_values($skillrow)[0];
               echo '<li class="job-description-box left">'.htmlentities($skillrow['name']).' &nbsp;&nbsp;&nbsp; </li>';
            }
            echo '</ul>';
        } else {
              //echo '<p style="color:orange">No skills found</p>';
        }
     // Education  ----------------------------------------------------------
        // get the school name
        $sqlid = 'SELECT year, institution_id, award_id FROM Education WHERE profile_id = :pid';
        $stmt_schoolid = $pdo->prepare($sqlid);
        $stmt_schoolid->execute(array(':pid' => $_GET['profile_id']));
        $school_ids = $stmt_schoolid->fetchALL(PDO::FETCH_ASSOC);

        // The profile_id was provided as a 'get' parameter.
        // There are two other foreign keys in the Education Table.

        // school id list
        $rows = $school_ids;
        $length = count($rows);
        if( $length !== 0) {
            echo '<h4 class="">Education</h4>';

            // If any of the entires are missing the year, the year will be
            // deleted.
            $year_block = false;
            for ($i = 0; $i < $length; $i++){
                $year_string = $rows[$i]['year'];
                if ( ! strlen($year_string) > 0 ) {
                      $year_block = true;
                }
            }

            for ($i = 0; $i < $length; $i++){
                // get the year
                $year_string = $rows[$i]['year'];
                if ( strlen($year_string) > 0 ) {
                      $year = (int) $year_string;
                } else {
                      $year = -99999;
                }
                // get the school name
                //print_r($rows[$i]);
                $institution_id = $rows[$i]['institution_id'];
                $sqlinst = 'SELECT name FROM Institution WHERE institution_id = :iid';
                $stmt_schoolname = $pdo->prepare($sqlinst);
                $stmt_schoolname->execute(array(':iid' => $institution_id));
                $school_name = $stmt_schoolname->fetch(PDO::FETCH_ASSOC);
                $school = array_values($school_name)[0];

                // get the degree award
                $degree_id = $rows[$i]['award_id'];
                $sqlaward = 'SELECT name FROM Award WHERE award_id = :aid';
                $stmt_award = $pdo->prepare($sqlaward);
                $stmt_award->execute(array(':aid' => $degree_id));
                $award_name = $stmt_award->fetch(PDO::FETCH_ASSOC);
                $award = array_values($award_name)[0];

                echo '<div class="container-edu-info">
                        <div class="edu-row1">';
                              if($year_block === false){
                echo               '<div class="edu-year-label">'
                                      .htmlentities($rows[$i]['year'])
                                  .'</div>';
                              } else {
                echo              '<div class="edu-label" style="font-size: 26px;"> &#9642</div>';
                                              // &#8226 for round bullet
                              }
                echo          '<div class="edu-info">'
                                   .'<div><p class="margin-bottom-small">'.htmlentities($school).'</p></div>'
                                   .'<div class="job-desc">'
                                    .'<p class="job-description-box">'
                                        .htmlentities($award)
                                    .'</p>'
                                   .'</div>'
                              .'</div>'
                         .'</div>'
                    .'</div>';
                    //.htmlentities($rows[$i]['major']).'&nbsp;'
            }
        } else {
              //echo '<p style="color:orange">No education found</p>';
        }
// Positions
  $sql = 'SELECT position_id, yearStart, yearLast, organization, title, summary FROM Position WHERE profile_id = :pid';
  $stmt_positions = $pdo->prepare($sql);
  $stmt_positions->execute(array(':pid' => $_GET['profile_id']));
  $row = $stmt_positions->fetchALL(PDO::FETCH_ASSOC);
  $rows = array_values($row);
  //$position_array = array_values($position_id);
  // $rows is a key-value pair array
  $worklength = count($rows);
  echo '<h4 class="more-top-margin-2x more-bottom-margin-1p5x">Employment History</h4>';
  if($worklength > 0) {
        foreach($rows as $job){
          //$task_id = $activity_rows[$i]['activity_id'];
          $position_id = $job['position_id'];
          echo '<div class="container-edu-info more-top-margin">
                       <div class="edu-row1">';
                             //if($year > 0){
          echo          '<div class="job-label job-title">'
                                     .htmlentities($job['yearStart'])
                                     .'-'
                                     .htmlentities($job['yearLast'])
                             .'</div>';
          echo          '<div class="job-info">'  // &#8226 for round bullet
                           .'<div>'  // &#9642
                               .'<p class="job-title">'
                                   .htmlentities($job['organization'])
                               .'</p>'.'<p class="job-title">'
                                   .htmlentities($job['title'])
                               .'</p>'
                           .'</div><div class="job-desc">'
                               .'<p class="job-description-box justify">'
                                           .htmlentities($job['summary'])
                               .'</p>';
                                  //echo '<h4 class="less-bottom-margin">Job Skills</h4>';
          echo '<ul class="less-top-margin">';
          $sqlActivitySet = 'SELECT activity_id FROM Activity
                       WHERE profile_id = :pid AND position_id = :posid';
          $stmt_activitySet = $pdo->prepare($sqlActivitySet);
          $stmt_activitySet->execute(array(':pid' => $_GET['profile_id'],
                                         ':posid' => $position_id));
          $activityList = $stmt_activitySet->fetchALL(PDO::FETCH_ASSOC);
       // activity list
          $activity_rows = $activityList;
          $length = count($activity_rows);
          for ($i = 0; $i < $length; $i++){
              $task_id = $activity_rows[$i]['activity_id'];
              $sqlActivity = 'SELECT description FROM Activity
                      WHERE profile_id = :pid AND position_id = :posid AND
                          activity_id = :aid';
              $stmt_activityname = $pdo->prepare($sqlActivity);
              $stmt_activityname->execute(array(':pid' =>   $_GET['profile_id'],
                      ':posid' =>   $position_id, ':aid' =>   $task_id));
              $taskrow = $stmt_activityname->fetch(PDO::FETCH_ASSOC);
                                     //$skill = array_values($skillrow)[0];
           echo '<li class="job-description-box justify">'.htmlentities($taskrow['description']).'</li>';
        }
        echo '</ul>'
                            .'</div>'
                        .'</div>'
                    .'</div>'
               .'</div>';
        }
  } else {
        //<p style="color:orange">No positions found</p>
  }
?>
<p class="double-space center"><a href="index.php">Return to Main Page</a>
</p>
<!-- <button id="getmain">Get #Main Width</button>
<button id="getw">Get Window Width</button>
<button id="getd">Get Document Width</button> -->
</div>
<script>
$(document).ready(function() {
  window.console && console.log('Document ready called ');
  var w = $( window ).width();
  window.console && console.log('The window width is = ' + w);
  isMobileDevice = Boolean("<?php echo isMobile() ?>" == 1);
  isLargeDevice = !isMobileDevice;
  window.console && console.log('Mobile device = ' + isMobileDevice);
  adjustWindow();
});
</script>
</body>
</html>
