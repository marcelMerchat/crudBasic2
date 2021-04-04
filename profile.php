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
        width: 20px;
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
    .org-title-color {
        color: #008888;
        font-size: 17px;
    }
    .job-title-color {
        color: #008888;
        font-size: 17px;
    }
    div.job-desc {
      display: inline-block;
      box-sizing: border-box;
      text-align: left;
      width: 99%;
      /*height: 1.1em;*/
      border: 0px solid #008800;
      padding: 0px;
      margin-top: 2px;
      margin-bottom: 0px;
    }
    .job-activity {
        box-sizing: border-box;
        text-align: left;
        word-wrap: break-word;
        padding-top: 1px;
        padding-bottom: 1px;
        width: 98%;
    }
    .hobby {
        box-sizing: border-box;
        text-align: left;
        word-wrap: break-word;
        padding-top: 1px;
        padding-bottom: 1px;
        width: 98%;
        margin-top: 0.5em;
        margin-bottom: 0.3em;
        border: 0px solid #008800;
    }
    div.container-edu-info {
      width: 30em;
      max-width: 100%;
      box-sizing: border-box;
      width: 100%;
      border: 0px solid black;
      padding: 1px;
      margin-top: 0.6em;
      margin-bottom: 4px;
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
    div.job-label {
      display: inline-block;
      vertical-align: top;
      text-align: left;
      width: 7.5rem;
      height: 1.1em;
      border: 0px solid #008800;
      margin-top: 0px;
      margin-bottom: 0px;
      padding-top: 0px;
      color: #008888;
    }
    div.job-info {
        display: inline-block;
        text-align: left;
        box-sizing: border-box;
        border: 0px solid #888800;
        margin-top: 0px;
        margin-bottom: 0px;
        word-break: break-word;
        width: calc(100% - 7.5rem);
        min-width: 180px;
        padding-top: 0px;
        border: 0px solid #888800;
    }
    .edu-row1 {
       border: 0px solid #008800;
       margin: 1px;
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
    $stmt = $pdo->prepare('SELECT first_name, last_name, phone, linkedin,
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
    //$contact_cnt = array_values($contactrows)[0];
    $phoneinfo = htmlentities($row['phone']);
    if ( strlen($phoneinfo) > 5)  {
        $sqlContactList = 'SELECT contact_id FROM ContactList WHERE profile_id = :pid';
        $stmt_contactList = $pdo->prepare($sqlContactList);
        $stmt_contactList->execute(array(':pid' => $_GET['profile_id']));
        $contactList = $stmt_contactList->fetchALL(PDO::FETCH_ASSOC);
      // school id list
        $contact_list_rows = $contactList;
        $length_contact_list_rows = count($contact_list_rows);
      // contacts
        $contacts = htmlentities($row['email']);
        $contacts = $contacts.' | ph: '.$phoneinfo
              .' | www.linkedin.com/in/'.htmlentities($row['linkedin']);
        echo '<p class="center small less-bottom-margin">'.$contacts;
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
        echo '<h4 class="more-top-margin-3x more-bottom-margin-1x">Goals</h4>';
        echo '<p class="resume-paragraph justify more-bottom-margin-1x">'.htmlentities($row['goal']).'</p>';

        // Positions
           $sql = 'SELECT position_id, yearStart, yearLast, organization, title, summary FROM Position WHERE profile_id = :pid';
           $stmt_positions = $pdo->prepare($sql);
           $stmt_positions->execute(array(':pid' => $_GET['profile_id']));
           $row = $stmt_positions->fetchALL(PDO::FETCH_ASSOC);
           $rows = array_values($row);
          //$position_array = array_values($position_id);
          // $rows is a key-value pair array
           $worklength = count($rows);
           echo '<h4 class="more-top-margin-2x more-bottom-margin-1x">Employment History</h4>';
           if($worklength > 0) {
                foreach($rows as $job){
                  //$task_id = $activity_rows[$i]['activity_id'];
                  $position_id = $job['position_id'];
                  if($job['yearLast']==9999){
                      $job['yearLast'] = 'Present';
                  }
                  $jobsummary = htmlentities($job['summary']);
                  echo '<div class="container-edu-info more-top-margin">
                           <div class="job-label"><p class="job-title-color">'
                                             .htmlentities($job['yearStart'])
                                             .'-'
                                             .htmlentities($job['yearLast'])
                                     .'</p>'
                          .'</div>';
                  echo      '<div class="job-info">'  // &#8226 for round bullet
                               .'<div>'  // &#9642
                                 .'<p class="org-title-color
                                             small-bottom-margin">'
                                       .htmlentities($job['organization'])
                                 .'</p>'.'<p class="job-title-color
                                                    more-top-margin-0x3
                                                    small-bottom-margin">'
                                        .htmlentities($job['title'])
                                 .'</p>'
                               .'</div>'
                           .'<div class="job-desc">';
                  if(strlen($jobsummary) > 1){
                      echo  '<p class="resume-paragraph
                                       more-top-margin-0x3 justify">'
                               .htmlentities($job['summary'])
                           .'</p>';
                  }
                  $sqlActivitySet = 'SELECT activity_id FROM Activity
                               WHERE profile_id = :pid AND position_id = :posid';
                  $stmt_activitySet = $pdo->prepare($sqlActivitySet);
                  $stmt_activitySet->execute(array(':pid' => $_GET['profile_id'],
                                                 ':posid' => $position_id));
                  $activityList = $stmt_activitySet->fetchALL(PDO::FETCH_ASSOC);
               // activity list
                  $activity_rows = $activityList;
                  $length = count($activity_rows);
                  if($length > 0){
                      echo '<ul class="less-top-margin">';

                  for ($i = 0; $i < $length; $i++){
                       $task_id = $activity_rows[$i]['activity_id'];
                       $sqlActivity = 'SELECT description FROM Activity
                              WHERE profile_id = :pid AND position_id = :posid AND
                                  activity_id = :aid';
                       $stmt_activityname = $pdo->prepare($sqlActivity);
                       $stmt_activityname->execute(array(':pid' => $_GET['profile_id'],
                              ':posid' =>   $position_id, ':aid' => $task_id));
                       $taskrow = $stmt_activityname->fetch(PDO::FETCH_ASSOC);
                                             //$skill = array_values($skillrow)[0];
                      echo '<li class="resume-paragraph more-top-margin-1x justify">'
                            .htmlentities($taskrow['description']).'</li>';
                    }
                    echo '</ul>';
                  }
                  echo '</div>
                    </div>
                 </div>';
                }
          } else {
                //<p style="color:orange">No positions found</p>
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
               echo '<h4 class="more-top-margin-2x more-bottom-margin-1x">Education</h4>';

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
                   echo              '<div class="edu-label" style="font-size: 21px;"> &#9642</div>';
                                                 // &#8226 for round bullet
                                 }
                   echo          '<div class="edu-info">'
                                      .'<div><p class="margin-bottom-small">'.htmlentities($school).'</p></div>'
                                      .'<div class="edu-award">'
                                       .'<p class="small-bottom-margin">'
                                           .htmlentities($award)
                                       .'</p>'
                                      .'</div>'
                                 .'</div>'
                            .'</div>'
                       .'</div>';
               } //end of for loop
           } else {
                 //echo '<p style="color:orange">No education found</p>';
           }

  // Certificates ----------------------------------------------------------
     // get the school name
     $sqlid = 'SELECT year, institution_id, edu_provider_id, award_id, award_link FROM Certificates WHERE profile_id = :pid';
     $stmt_schoolid = $pdo->prepare($sqlid);
     $stmt_schoolid->execute(array(':pid' => $_GET['profile_id']));
     $rows = $stmt_schoolid->fetchALL(PDO::FETCH_ASSOC);
     // The profile_id was provided as a 'get' parameter.
     // There are two other foreign keys in the Education Table.
     // school id list
     //$rows = $school_ids;
     //var_dump($rows);
     $length = count($rows);
     if( $length !== 0) {
         echo '<h4 class="more-top-margin-2x more-bottom-margin-1x">Certificates</h4>';
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
             //var_dump($rows);
             $institution_id = $rows[$i]['institution_id'];
             $provider_id = $rows[$i]['edu_provider_id'];
             $sqlinst = 'SELECT name FROM Institution WHERE institution_id = :iid';
             $stmt_edu = $pdo->prepare($sqlinst);
             $stmt_edu->execute(array(':iid' => $institution_id));
             $edu = $stmt_edu->fetch(PDO::FETCH_ASSOC);
             $school = array_values($edu)[0];

             $sqlinst = 'SELECT name FROM Edu_Provider WHERE provider_id = :prvid';
             $stmt_edu = $pdo->prepare($sqlinst);
             $stmt_edu->execute(array(':prvid' => $provider_id));
             $providerlist = $stmt_edu->fetch(PDO::FETCH_ASSOC);
             $provider = array_values($providerlist)[0];

             // get the degree award
             $degree_id = $rows[$i]['award_id'];
             $award_link = $rows[$i]['award_link'];
             $award_link = $rows[$i]['award_link'];
             $sqlaward = 'SELECT name FROM Award WHERE award_id = :aid';
             $stmt_award = $pdo->prepare($sqlaward);
             $stmt_award->execute(array(':aid' => $degree_id));
             $award_name = $stmt_award->fetch(PDO::FETCH_ASSOC);
             $award = array_values($award_name)[0];
             //$provider = array_values($provider)[0];
             echo '<div class="container-edu-info more-top-margin-3x">
                     <div class="edu-row1">';
             if($year_block === false){
                echo               '<div class="edu-year-label">'
                                   .htmlentities($rows[$i]['year'])
                               .'</div>';
              } else {
                echo              '<div class="edu-label"
                                     style="font-size: 21px;">
                                     &#9642</div>';
                                     // square bullet, &#8226 for round
                echo          '<div class="edu-info">';
                echo          '<p class="small-bottom-margin">';
                 //echo strlen($award_link);
                if(strlen($award_link) > 16){
                   https://www.a.com
                   echo '<a href="'.$award_link.'" target="_blank">'
                          .htmlentities($award)
                     .'</a>';
                } else {
                   echo htmlentities($award);
                }
                if(strlen($provider) > 0 && $provider != 'NA') {
                         echo   ' by '
                               .htmlentities($school)
                               .' on '.$provider;
                 } else {
                         echo ' from '
                               .htmlentities($school);
                 }
                 echo    '</p>'
                   .'</div>'
                  .'</div>'
                 .'</div>';
             } // end of else
         } // end of for loop
     } else {
           //echo '<p style="color:orange">No education found</p>';
     }

        // Skills
        $sqlSkillCount = 'SELECT COUNT(*) FROM SkillSet WHERE profile_id = :pid';
        $stmtCount = $pdo->prepare($sqlSkillCount);
        $stmtCount->execute(array(':pid' => $_GET['profile_id']) );
        $rowcount = $stmtCount->fetch(PDO::FETCH_ASSOC);
        $cnt = array_values($rowcount)[0];
        if( $cnt > 0) {
            $sqlSkillSet = 'SELECT skill_id FROM SkillSet WHERE profile_id = :pid';
            $stmt_skillSet = $pdo->prepare($sqlSkillSet);
            $stmt_skillSet->execute(array(':pid' => $_GET['profile_id']));
            $skillSet = $stmt_skillSet->fetchALL(PDO::FETCH_ASSOC);
          // skill id list
            $rows = $skillSet;
            $length = count($rows);

            echo '<h4 class="more-top-margin-2x more-bottom-margin-0p3x">Job Skills</h4>';
            echo '<ul class="less-top-margin">';
            for ($i = 0; $i < $length; $i++){
               $skill_id = $rows[$i]['skill_id'];
               $sqlSkill = 'SELECT name FROM Skill WHERE skill_id = :iid';
               $stmt_skillname = $pdo->prepare($sqlSkill);
               $stmt_skillname->execute(array(':iid' =>   $skill_id));
               $skillrow = $stmt_skillname->fetch(PDO::FETCH_ASSOC);
               //$skill = array_values($skillrow)[0];
               echo '<li class="resume-paragraph left">'.htmlentities($skillrow['name']).' &nbsp;&nbsp;&nbsp; </li>';
            }
            echo '</ul>';
        } else {
              //echo '<p style="color:orange">No skills found</p>';
        }


             // Work Examples and Projects  ----------------------------------------------------------
                // get the project information
                // For all records .. for fetch you are going to need to loop
                // while($row = $stmt->fetch()) { work with the record }
                // for fetchAll() you have direct access to the records as a array.
                // – Raymond Nijland Mar 16 '19 at 12:51
                $sql   = 'SELECT year, name, report_link, github_link FROM Project WHERE profile_id = :pid';
                $stmt_proj = $pdo->prepare($sql);
                $stmt_proj->execute(array(':pid' => $profileid));
                $rows = $stmt_proj->fetchALL(PDO::FETCH_ASSOC);
                $length = count($rows);
                if( $length > 0) {
                    echo '<h4 class="more-top-margin-2x more-bottom-margin-1x">Projects</h4>';
                    // If any of the entires are missing the year, the year will be
                    // deleted.
                    $year_block = false;
                    // for ($i = 0; $i < $length; $i++){
                    //     $year_string = $rows[$i]['year'];
                    //     if ( ! strlen($year_string) > 0 ) {
                    //           $year_block = true;
                    //     }
                    // }
                    foreach ($rows as $row) {
                         //var_dump($row);
                         // get the year
                         $year_string = $row['year'];
                         if ( strlen($year_string) > 0 ) {
                                  $year = (int) $year_string;
                         } else {
                                  $year = -99999;
                                  $year_block = true;
                         }
                         $proj = array_values($row);
                         $proj_desc = $row['name'];
                         $report_lnk = $row['report_link'];
                         $git_lnk = $row['github_link'];

                        //$provider = array_values($provider)[0];
                        echo '<div class="container-edu-info">
                                <div class="edu-row1">';
                                      if($year_block === false){
                                        '<div class="edu-year-label">'
                                          .htmlentities($row['year'])
                                        .'</div>';
                                       } else {
                        echo            '<div class="edu-label"
                                              style="font-size: 21px;"> &#9642</div>';
                                              // &#8226 for round bullet
                                      }
                        echo          '<div class="edu-info">';
                        echo          '<p class="small-bottom-margin">';
                        //echo strlen($award_link);
                        if(strlen($report_lnk) > 16){
                              echo '<a href="'.$report_lnk.'" target="_blank">'
                                     .htmlentities($proj_desc)
                                .'</a>';
                        } else {
                            echo htmlentities($proj_desc);
                        }
                        echo    '</p>';
                        //if(strlen($git_lnk) > 16){
                        //    echo    '<p class="small-bottom-margin">
                        //               There are details and code at this
                        //               <a href="'.$git_lnk.'" target="_blank">'
                        //            .' Github link'
                        //        .'</a>.';
                        //}
                        //echo    '</p>';
                        echo '</div>'
                             .'</div>'
                            .'</div>';
                            //.htmlentities($rows[$i]['major']).'&nbsp;'
                    }
                } else {
                      //echo '<p style="color:orange">No education found</p>';
                }


  // Hobbies and Interests
  $sqlSkillCount = 'SELECT COUNT(*) FROM Personal WHERE profile_id = :pid';
  $stmtCount = $pdo->prepare($sqlSkillCount);
  $stmtCount->execute(array(':pid' => $_GET['profile_id']) );
  $rowcount = $stmtCount->fetch(PDO::FETCH_ASSOC);
  $cnt = array_values($rowcount)[0];
  //var_dump($cnt);
  //if( $length !== 0) {
  $sqlHobbyList = 'SELECT
        interest, languages, computer_skill, publication, licenses
       FROM Personal WHERE profile_id = :pid';
  $stmt_HobbyList = $pdo->prepare($sqlHobbyList);
  $stmt_HobbyList->execute(array(':pid' => $_GET['profile_id']));
  $row = $stmt_HobbyList->fetch(PDO::FETCH_ASSOC);
  //    var_dump($row);
  $hobbies = $row['interest'];
  $language = $row['languages'];
  $software = $row['computer_skill'];
  $pub = $row['publication'];
  $license = $row['licenses'];
      //echo '<h4 class="more-top-margin-2x more-bottom-margin-1x left">Hobbies and Interests</h4>';
      // Detailed list for hobbies
  $sqlHobbyCount = 'SELECT COUNT(*) FROM HobbyList WHERE profile_id = :pid';
  $stmtCount = $pdo->prepare($sqlHobbyCount);
  $stmtCount->execute(array(':pid' => $_GET['profile_id']) );
  $rowcount = $stmtCount->fetch(PDO::FETCH_ASSOC);
  $cnt = array_values($rowcount)[0];
  if( $cnt > 0 || strlen($hobbies) > 2 || strlen($language) > 2  ||
                      strlen($software) > 2 || strlen($pub) > 2  ||
                      strlen($license) > 2) {
          echo '<p class="more-top-margin-3x more-bottom-margin-1x left">
                 <strong>Hobbies and Interests:&nbsp;</strong>'
                  .'</p>';
          $sqlHobbyList = 'SELECT hobby_id FROM HobbyList WHERE profile_id = :pid';
          $stmt_HobbyList = $pdo->prepare($sqlHobbyList);
          $stmt_HobbyList->execute(array(':pid' => $_GET['profile_id']));
          $rows = $stmt_HobbyList->fetchALL(PDO::FETCH_ASSOC);
        // school id list
          $length = count($rows);
          echo '<ul class="less-top-margin">';
          for ($i = 0; $i < $length; $i++){
             $hobby_id = $rows[$i]['hobby_id'];
             $sqlHobby = 'SELECT name FROM Hobby WHERE hobby_id = :iid';
             $stmt_hobbyname = $pdo->prepare($sqlHobby);
             $stmt_hobbyname->execute(array(':iid' =>   $hobby_id));
             $hobbyrow = $stmt_hobbyname->fetch(PDO::FETCH_ASSOC);
             //$hobby = array_values($hobbyrow)[0];
             echo '<li class="hobby left" id="hobbyitem"'.$i.'>'
                .ucfirst(htmlentities($hobbyrow['name'])).' &nbsp;&nbsp;&nbsp; </li>';
          }
          echo '</ul>';
        } else {
            //echo '<p style="color:orange">No hobbies found</p>';
        }
        if(strlen($language) > 2){
            echo '<p class="more-top-margin-2x left">
                             <strong>Foreign Languages:&nbsp;</strong>'
                    .$language.'</p>';
        }
        if(strlen($software) > 2){
            echo '<p class="more-top-margin-2x left">
                             <strong>Computer Skills:&nbsp;</strong>'
                    .$software.'</p>';
        }
        if(strlen($pub) > 2){
            echo '<p class="more-top-margin-2x left">
                             <strong>Publication:&nbsp;</strong>'
                    .$pub.'</p>';
        }
        if(strlen($license) > 2){
            echo '<p class="more-top-margin-2x left">
                             <strong>Licenses:&nbsp;</strong>'
                    .$license.'</p>';
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
