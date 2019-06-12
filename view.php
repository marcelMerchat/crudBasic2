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
    div.edu-label {
        display: inline-block;
        text-align: left;
        width: 3.2em;
        height: 1.1em;
        border: 0px solid #008800;
        padding-top: 0px;
        margin-top: 0px;
        padding-bottom: 0px;
        margin-bottom: 0px;
    }
    div.job-label {
        display: inline-block;
        text-align: left;
        width: 5.5em;
        height: 1.1em;
        border: 0px solid #008800;
        padding-top: 0px;
        margin-top: 0px;
        padding-bottom: 0px;
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
      margin-bottom: 1px;
    }
    div.edu-info {
        display: inline-block;
        text-align: left;
        width: 65%;
        padding-top: 1px;
        padding-bottom: 0px;
        border: 0px solid #008800;
        margin-top: 1px;
        margin-bottom: 0px;
    }
    .edu-row1 {
       border: 0px solid #008800;
       margin-top: 1px;
       margin-bottom: 0px;
       padding-top: 1px;
       padding-bottom: 0px;
    }
    .edu-row2 {
       border: 0px solid #008800;
       padding-top: 1px;
       padding-bottom: 1px;
       margin-top: 1px;
       margin-bottom: 1px;
    }
    .more-top-margin {
        margin-top: 8px;
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
<div class="content" id="main">
<br />
<?php
    $profileid = $_GET['profile_id'];
    $stmt = $pdo->prepare('SELECT first_name, last_name,
                  email, profession, goal FROM Profile WHERE profile_id = :pid');
    $stmt->execute(array(':pid' => $_GET['profile_id']));
    $row =  $stmt->fetch(PDO::FETCH_ASSOC);
        echo '<p class="center big less-bottom-margin">'.htmlentities($row['first_name']).' '.
                               htmlentities($row['last_name']).'
              </p><p class="center">'.
                               htmlentities($row['profession']).
              '</p>';
        echo '<p class="center small more-top-margin">Email: '.htmlentities($row['email']).'</p>';
        //echo '<br />';
        echo '<h4>Goals</h4>';
        echo '<p>'.htmlentities($row['goal']).'</p>';
        $sqlSkillSet = 'SELECT skill_id FROM SkillSet WHERE profile_id = :pid';
        $sqlSkill = 'SELECT name FROM Skill WHERE skill_id = :iid';
        $stmt_skillSet = $pdo->prepare($sqlSkillSet);
        $stmt_skillSet->execute(array(':pid' => $_GET['profile_id']));
        $skillSet = $stmt_skillSet->fetchALL(PDO::FETCH_ASSOC);
        // school id list
        $rows = $skillSet;
        $length = count($rows);
        if( $length !== 0) {
            echo '<h4 class="less-bottom-margin">Job Skills</h4>';
            echo '<ul class="less-top-margin">';
            for ($i = 0; $i < $length; $i++){
                $skill_id = $rows[$i]['skill_id'];
                $stmt_skillname = $pdo->prepare($sqlSkill);
                $stmt_skillname->execute(array(':iid' =>   $skill_id));
                $skill_name = $stmt_skillname->fetch(PDO::FETCH_ASSOC);
                $skill = array_values($skill_name)[0];
                echo '<li>'.htmlentities($skill).' &nbsp;&nbsp;&nbsp; </li>';
            }
            echo '</ul>';
        } else {
              //echo '<p style="color:orange">No skills found</p>';
        }
     // Education  ----------------------------------------------------------
        $sqlid = 'SELECT institution_id, year, major FROM Education WHERE profile_id = :pid';
        $sqlinst = 'SELECT name FROM Institution WHERE institution_id = :iid';
        $stmt_schoolid = $pdo->prepare($sqlid);
        $stmt_schoolid->execute(array(':pid' => $_GET['profile_id']));
        $school_ids = $stmt_schoolid->fetchALL(PDO::FETCH_ASSOC);
        // school id list
        $rows = $school_ids;
        $length = count($rows);
        if( $length !== 0) {
            echo '<h4 class="">Education</h4>';
            for ($i = 0; $i < $length; $i++){
                $institution_id = $rows[$i]['institution_id'];
                $stmt_schoolname = $pdo->prepare($sqlinst);
                $stmt_schoolname->execute(array(':iid' =>   $institution_id));
                $school_name = $stmt_schoolname->fetch(PDO::FETCH_ASSOC);
                $school = array_values($school_name)[0];
                echo '<div class="container-edu-info more-top-margin less-bottom-margin">'
                         .'<div class="edu-label">'
                            .'<div class="edu-row1">'.htmlentities($rows[$i]['year']).'</div>'
                         .'</div><div class="edu-info">'
                            .'<div class="edu-row1">'.htmlentities($school).'</div>'
                         .'</div>'
                     .'</div>'
                     .'<div class="edu-row2">'
                         .'<div class="edu-label">'
                            .'<div class="edu-row2"></div>'
                         .'</div><div class="edu-info">'
                            .'<div class="edu-row2">Major:&nbsp;'.htmlentities($rows[$i]['major']).'</div>'
                        .'</div>'
                     .'</div>';
            }
        } else {
              //echo '<p style="color:orange">No education found</p>';
        }
  $sql = 'SELECT yearStart, yearLast, organization, description FROM Position WHERE profile_id = :pid';
  $stmt_positions = $pdo->prepare($sql);
  $stmt_positions->execute(array(':pid' => $_GET['profile_id']));
  $row = $stmt_positions->fetchALL(PDO::FETCH_ASSOC);
  $rows = array_values($row);
  $worklength = count($rows);
  if($worklength > 0) {
      echo '<h4>Employment History</h4>';
      foreach($rows as $job){
          echo '<div class="container-edu-info">'
                    .'<div class="job-label">'
                       .'<div class="edu-row1">'.htmlentities($job['yearStart']).'-'
                          .htmlentities($job['yearLast']).'</div>'
                    .'</div><div class="edu-info">'
                       .'<div class="edu-row1">'.htmlentities($job['organization']).'</div>'
                    .'</div>'
               .'</div>'
               .'<div class="edu-row2">'
                  .'<div class="job-label">'
                      .'<div class="edu-row2"></div>'
                  .'</div><div class="edu-info">'
                      .'<div class="edu-row2">'.htmlentities($job['description']).'</div>'
                  .'</div>'
               .'</div>';
        }
  } else {
        //echo '<p style="color:orange">No positions found</p>';
  }
?>
<br />
<p class="double-space center"><a href="index.php">Return to Main Page</a>
</p>
</div>
</body>
</html>
