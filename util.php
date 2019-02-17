<?php
// util.php utilities

function isMobile() {
    return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
}
function loadMobilityStyles() {
    if(isMobile()==1) {
        //require_once 'mobile.php';
        echo '<link rel="stylesheet" type="text/css" href="styleMobile.css">';
    } else {
        //require_once 'desktop.php';
        echo '<link rel="stylesheet" type="text/css" href="styleDesktop.css">';
    }
}
function flashMessages(){
    //$_SESSION["success"] = 'Record edited Nov 6: there are now '.$posCount.' positions.';
    //echo '<p class="big message" style="color:red">'.$_SESSION['success'].'</p>';
    if ( isset($_SESSION['error']) ) {
          echo '<p class="message" style="color:red">'.$_SESSION['error'].'</p>';
          unset($_SESSION['error']);
          unset($_SESSION['message']);
    }
    if ( isset($_SESSION['success']) ) {
          echo '<p class="message" style="color:green">'.$_SESSION['success'].'</p>';
          unset($_SESSION['success']);
          unset($_SESSION['message']);
    }
  }
function validateProfile() {
  $_SESSION['success'] = $_SESSION['success'].' profile check . . . ';
  if ( (strlen($_POST['first_name']) > 0) && (strlen($_POST['last_name']) > 0)
                                          &&
            (strlen($_POST['email']) > 0) && (strlen($_POST['profession']) > 0)
                                          &&
            strlen($_POST['goal']) > 0  ) {
       //$_SESSION['error'] = "No errors yet";
       $_SESSION['success'] = $_SESSION['success'].' profile post check ok . . . ';
       if ( strpos($_POST['email'],'@') === false ) {
            $_SESSION['error'] = "Invalid email address";
            $_SESSION['error'] = "Invalidate email profile error";
            return "Invalid email address";
       }
  } else {
      $_SESSION['error'] = "Invalidate profile error"; "All values are required";
  }
  return true;
}
function multiexplode ($delimiters,$string) {
    $ready = str_replace($delimiters, $delimiters[0], $string);
    $launch = explode($delimiters[0], $ready);
    return  $launch;
}
function detectOffensive($phrase) {
    $offense = '';
    $punct = array(',','.','!','?','@');
    $replace = array(' ');
    foreach ($punct as $punc) {
       $replaced = str_replace($punc,' ',$phrase,$replaceCount);
       $replaced = preg_replace('#[[:punct:]]#', ' ', $replaced);
    }
    $exploded = explode(' ',$replaced);
    $newPhrase = '';
    foreach ($exploded as $word) {
           $stmt = $pdo->prepare("SELECT COUNT(*) FROM Dictionary WHERE Word = ?");
           $stmt->execute(array($word));
          //$cnt = $stmt->fetch(PDO::FETCH_COLUMN);
           $rowdata = $stmt->fetch(PDO::FETCH_ASSOC);
           $cnt = array_values($rowdata)[0];
           if($cnt > 0) {
    //           Found in dictionary; do nothing;
           } else {
              // Rather than find our word with an offensive word, we need the Reverse
              // We use the mySQL CONCAT operator to find offensive words as internal substrings
              // find "abcMyOffensiveWordabc"
              $stmt = $pdo->prepare('SELECT COUNT(*) FROM Offensive WHERE ? LIKE CONCAT("%", Word, "%")');
              $stmt->execute([$word]);
              $rowdata2 = $stmt->fetch(PDO::FETCH_ASSOC);
              // COUNT(*) does not return an array.
              $cnt2 = array_values($rowdata2)[0];
              if($cnt2 > 0) {
    // //           $_SESSION['error'] = 'offensive';
    // //           $_SESSION['message'] = 'Rejected word at : '.$word;
                    $offense = "questionable language";
               }
           }
    }
    return $offense;
}
function ofnsvCheck($pdo, $phrase) {
    //$offense = '';
    $offense = false;
    $punct = array(',','.','!','?','@');
    $replace = array(' ');
    foreach ($punct as $punc) {
       $replaced = str_replace($punc,' ',$phrase,$replaceCount);
       $replaced = preg_replace('#[[:punct:]]#', ' ', $replaced);
    }
    $exploded = explode(' ',$replaced);
    $newPhrase = '';
    $message = "inside ofnsvJSON";
    // echo "<script type='text/javascript'>alert('$message.': '.$exploded);</script>";
    foreach ($exploded as $word) {
             $stmt = $pdo->prepare("SELECT COUNT(*) FROM Dictionary WHERE Word = ?");
             $stmt->execute(array($word));
             $rowdata = $stmt->fetch(PDO::FETCH_ASSOC);
             $cnt = array_values($rowdata)[0];
             if($cnt > 0) {
    // //           Found in dictionary; do nothing;
             } else {
    //            // Rather than find our word with an offensive word, we need the Reverse
    //            // We use the mySQL CONCAT operator to find offensive words as internal substrings
    //            // find "abcMyOffensiveWordabc"
                $stmt = $pdo->prepare('SELECT COUNT(*) FROM Offensive WHERE ? LIKE CONCAT("%", Word, "%")');
                $stmt->execute([$word]);
                $rowdata2 = $stmt->fetch(PDO::FETCH_ASSOC);
    //        // COUNT(*) does not return an array.
                $cnt2 = array_values($rowdata2)[0];
                if($cnt2 > 0) {
                    //$offense = ': Questionable language detected . . . ';
                    $offense = true;
                }
             }
    }
    return $offense;
}
function filterWord($pdo, $phrase) {
    //$text = "here is a sample: this text, and this will be exploded. this also | this one too :)";
    //$exploded = multiexplode(array(",",".","|",":"),$text);
    //print_r($exploded);
    //$exploded = explode(' ',multiexplode(array(",",".","|",":"),$phrase));
    $punct = array(',','.','!','?','@');
    $replace = array(' ');

    //$arr = array("Hello","world","!");
    //$replaced = str_replace('.'," ");
    $replaceCount = 0;
    foreach ($punct as $punc) {
      $replaced = str_replace($punc,' ',$phrase,$replaceCount);
      $replaced = preg_replace('#[[:punct:]]#', ' ', $replaced);
    }
    $exploded = explode(' ',$replaced);
    $newPhrase = '';
    foreach ($exploded as $word) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM Dictionary WHERE Word = ?");
        $stmt->execute([$word]);
        $cnt = $stmt->fetch(PDO::FETCH_COLUMN);
        if($cnt > 0) {
            $newPhrase = $newPhrase.' '.$word;
        } else {
            // Rather than find our word with an offensive word, we need the Reverse
            // We use the mySQL CONCAT operator to find offensive words as internal substrings
            // find "abcMyOffensiveWordabc"
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM Offensive WHERE ? LIKE CONCAT("%", Word, "%")');
            $stmt->execute([$word]);
            $cnt2 = $stmt->fetch(PDO::FETCH_COLUMN);
            if($cnt2 > 0) {
              //$_SESSION['error'] = 'offensive';
              $_SESSION['message'] = 'Rejected word at : '.$word;
            } else {
              $newPhrase = $newPhrase.' '.$word;
            }
        }
    }
    return trim($newPhrase);
}
function validateSkill() {
    $count = 0;
    for($i=1; $i<=9; $i++) {
        if ( ! isset($_POST['skill'.$i] )) continue;
        $skill = $_POST['skill'.$i];
        if ( strlen($skill) == 0 ) {
            return "All skill entries were required";
        }
        $count = $count  + 1;
    }
    $_SESSION['countJobSkills'] = $count;
    return true;
}
function validatePos() {
    $_SESSION['countJobs'] = 0;
    $count = 0;
    $_SESSION['success'] = $_SESSION['success'].' validating position . . . ';
    //echo 'start pos entry loop';
    for($i=1; $i<=9; $i++) {
        $_SESSION['success'] = $_SESSION['success'].' validating position'.$_POST['wrkStartYr'.$i];
        if ( ! isset($_POST['wrkStartYr'.$i]) ) continue;
        if ( ! isset($_POST['wrkFinalYr'.$i]) ) continue;
        if ( ! isset($_POST['org'.$i]) ) continue;
        if ( ! isset($_POST['desc'.$i]) ) continue;
        //echo 'loop '.$i.' set';
        $_SESSION['success'] = $_SESSION['success'].' validating year'.$_POST['wrkStartYr'.$i].$_POST['wrkFinalYr'.$i];
        $yearStart = $_POST['wrkStartYr'.$i];
        $yearLast = $_POST['wrkFinalYr'.$i];
        $org = $_POST['org'.$i];
        $desc = $_POST['desc'.$i];
        //echo 'assigned variables';
        if ( ! (is_numeric($yearStart) && is_numeric($yearLast)) ) {
            return "Both position years must be numeric.";
        }
        if ( strlen($org) == 0 ) {
            return "Organization field is required";
        }
        if ( strlen($org) == 0 || strlen($desc) == 0 ) {
            return "Position description is required";
        }
        //echo 'data validatin checked';
        $count = $count + 1;
    }
    $_SESSION['countJobs'] = $count;
    //echo 'job count is '.$_SESSION['countJobs'] ;
    return true;
}
function validateEducation() {
    $count = 0;
    for($i=1; $i<=9; $i++) {
        if ( ! isset($_POST['edu_school'.$i]) ) {
            continue;
        } else {
            $school = $_POST['edu_school'.$i];
            if ( strlen($school) == 0 ) {
               return "The name of the educational institution is required";
            }
        }
        if ( ! isset($_POST['edu_major'.$i]) ) {
            continue;
        } else {
            $major = $_POST['edu_major'.$i];
            if ( strlen($major) == 0 ) {
               return "The name of the major subject is required";
            }
        }
        if ( ! isset($_POST['edu_year'.$i]) ) {
            continue;
        } else {
            $year = $_POST['edu_year'.$i];
            if ( strlen($year) == 0 ) {
                return "The education year is required";
            }
            if ( ! is_numeric($year) ) {
                return "Education year must be numeric";
            }
        }
        $count = $count + 1;
        }
    $_SESSION['countSchools'] = $count;
    return true;
}
function validateInstitution() {
    for($i=1; $i<=9; $i++) {
        if ( ! isset($_POST['name'.$i]) ) continue;
        $institution = $_POST['name'.$i];
        if ( strlen($institution) == 0 ) {
            return "The name of the educational institution is required";
        }
    }
    return true;
}
function loadSkill($pdo, $profile_id) {
  $sql = 'SELECT Skill.name FROM SkillSet JOIN Skill
      ON Skill.skill_id = SkillSet.skill_id
      WHERE profile_id = :prof';
  $stmt = $pdo->prepare($sql);
  $stmt->execute(array(':prof' => $profile_id) );
  $skill = $stmt->fetchALL(PDO::FETCH_ASSOC);
  return $skill;
}
function loadPos($pdo, $profile_id) {
 $stmt = $pdo->prepare('SELECT * FROM Position
     WHERE profile_id = :prof ORDER BY rank');
 $stmt->execute(array(':prof' => $profile_id) );
 $positions = $stmt->fetchALL(PDO::FETCH_ASSOC);
 return $positions;
}

function loadEdu($pdo, $profile_id) {
  $sql = 'SELECT Education.year, Institution.name, Education.major FROM Education JOIN Institution
      ON Education.institution_id = Institution.institution_id
      WHERE Education.profile_id = :prof ORDER BY `rank`';
  $stmt = $pdo->prepare($sql);
  $stmt->execute(array(':prof' => $profile_id) );
  $education = $stmt->fetchALL(PDO::FETCH_ASSOC);
  return $education;
}
function insertSkillSet($pdo, $profile_id) {
    $rank = 1;
    //echo "inserting skill fields";
    for($i=1; $i<=9; $i++) {
      if ( ! isset($_POST['skill_name'.$i]) ) continue;
      $skill = $_POST['skill_name'.$i];
      unset($_SESSION['error']);
      $skill = trim(filterWord($pdo, $skill));
      if(isset($_SESSION['error']) && $_SESSION['error'] == "offensive"){
          $msg = 'The profile was saved but at least one skill was not entered, you can edit the profile. ';
          unset($_SESSION['error']);
          continue;
      }
  //  lookup the skill
      $skill_id = false;
      $sql = 'SELECT skill_id FROM Skill WHERE name = :nme';
      $stmt = $pdo->prepare($sql);
      $stmt->execute(array(':nme' => $skill) );
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      print_r($row);
      var_dump($row);
      if($row !== false) {
          // add 0 to convert variable type to numeric
          $skill_id = $row['skill_id'] + 0;
          echo 'The skill id for '.$skill.' was found: '.$skill_id;
      }
      //if skill is not found, insert it
      echo 'The skill id is '.$skill_id;
      if($skill_id === false) {
          echo 'The skill id is false ';
          $sql = 'INSERT INTO Skill (`name`) VALUES (TRIM(:nme))';
          $stmt = $pdo->prepare($sql);
          $stmt->execute(array(':nme' => trim($skill)) );
          $skill_id = $pdo->lastInsertId() + 0;
      }
      $sql = 'INSERT INTO SkillSet (profile_id, skill_id, `rank`)
                    VALUES ( :pid, :sid, :rnk)';
      $stmt = $pdo->prepare($sql);
      $stmt->execute(array(
                  ':pid' => $profile_id, ':sid' => $skill_id,
                  ':rnk' => $rank ) );

      $rank++;
      $_SESSION['success'] = "New skill added. ";
      }
}
function insertEducations($pdo, $profile_id) {
    $rank = 1;
    $count = 0;
    for($i=1; $i<=9; $i++) {
      if ( ! isset($_POST['edu_year'.$i]) ) continue;
      if ( ! isset($_POST['edu_school'.$i]) ) continue;
      if ( ! isset($_POST['edu_major'.$i]) ) continue;
      $year = $_POST['edu_year'.$i];
      unset($_SESSION['error']);
      $school = trim($_POST['edu_school'.$i]);
      $school = filterWord($pdo, $school);
      $major = trim($_POST['edu_major'.$i]);
      $major = filterWord($pdo, $major);
      if(isset($_SESSION['error']) && $_SESSION['error'] == "offensive"){
          $msg = $msg.' The profile was saved but at least one education was not entered, you can edit the profile. ';
          unset($_SESSION['error']);
          continue;
      }
      //lookup the school
      $institution_id = false;
      $sql = 'SELECT institution_id FROM Institution WHERE name = :nme';
      $stmt = $pdo->prepare($sql);
      $stmt->execute(array(':nme' => $school) );
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      if($row !== false) {
          $institution_id = $row['institution_id'] + 0;
      }
      //if school not found, insert it
      if($institution_id === false) {
          $sql = 'INSERT INTO Institution (`name`) VALUES (TRIM(:nme))';
          $stmt = $pdo->prepare($sql);
          $stmt->execute(array(':nme' => $school) );
          $institution_id = $pdo->lastInsertId() + 0;
      }
      $sql = 'INSERT INTO Education (profile_id, institution_id, `rank`, major, year)
                    VALUES ( :pid, :iid, :rnk, TRIM(:maj), :yr)';
      $stmt = $pdo->prepare($sql);
      $stmt->execute(array(
                  ':pid' => $profile_id, ':iid' => $institution_id,
                  ':rnk' => $rank, ':maj' => trim($major),
                  ':yr' => $year ) );
      $rank++;
      $count++;
      }
      if($rank > 0){
        //$_SESSION['success'] = "There are ".$rank." education entries.";
      }
}
function insertPositions($pdo, $profile_id) {
    $rank = 1;
    $count = 0;
    //$_SESSION['success'] = $_SESSION['success'].' inserting position - '.$_POST['workStartYear'.$i];
    // $_SESSION['success'] = $_SESSION['success'].' starting language check - '
    //      .$_POST['workStartYear1'].$_POST['org1'].$_POST['workFinalYear1'].$_POST['desc1'] ;
    for($i=1; $i<=9; $i++) {
    //   //echo 'years '.$_POST['yearStart'.$i].$_POST['yearLast'.$i].$_POST['org'.$i].$_POST['desc'.$i];
       if ( isset($_POST['wrkStartYr'.$i]) && isset($_POST['org'.$i]) &&
            isset($_POST['wrkFinalYr'.$i]) && isset($_POST['desc'.$i]) &&
            strlen($_POST['org'.$i]) > 0 && (strlen($_POST['desc'.$i]) > 0) ) {
                   //$_SESSION['success'] = $_SESSION['success'].' starting language test - ';
                   $org = trim($_POST['org'.$i]);
                   $org = filterWord($pdo, $org);
                   $desc = trim($_POST['desc'.$i]);
                   $off = ofnsvCheck($pdo, $desc);
                 //echo 'offensive test completed '.$off;
                 //$_SESSION['success'] = $_SESSION['success'].' starting language check - '.$off;
                 if($off==true){
                     $desc = filterWord($pdo, $desc);
                     $_SESSION['error'] = ' Offensive language may be present, please try again. ';
                     $msg = $msg.' The profile was saved but at least one position was not entered, you can edit the profile. ';
                     $_SESSION['success'] = $_SESSION['success'].$msg;
                     continue;
                 } else {
                     //$_SESSION['success'] = $_SESSION['success'].' -good language test- ';
                     $stmt = $pdo->prepare('INSERT INTO Position
                       (profile_id, `rank`, yearStart, yearLast, organization, description)
                           VALUES ( :pid, :rnk, :yrStart, :yrLast, TRIM(:org), TRIM(:de))');
                     $stmt->execute(array(
                       ':pid' => $profile_id,  ':rnk' => $rank,
                       ':yrStart' => $_POST['wrkStartYr'.$i],
                       ':yrLast' => $_POST['wrkFinalYr'.$i],
                       ':org' => trim($_POST['org'.$i]),
                       ':de' => trim($_POST['desc'.$i]))  );
                       //echo 'added position set';
                     $rank++;
                 }
       } else {
         //$_SESSION['success'] = $_SESSION['success'].' test failed to start - ';

       }
    }
    $_SESSION['countPosition'] = $rank - 1;
    $_SESSION['success'] = $_SESSION['success']."There are ".$_SESSION['countPosition']." positions.";
}
function get_name($pdo, $user_id) {
    $stmt = $pdo->prepare('SELECT name FROM users WHERE user_id= :id');
    $stmt->execute(array(':id' => $user_id) );
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $fullname = array_values($row)[0];
    return $fullname;
}
function get_profile_information($pdo, $profile_id, $user_id) {
  // Retrieve the profile information for the profile_id
  $sql = 'SELECT * FROM Profile WHERE profile_id = :pid AND user_id = :uid';
  $stmt = $pdo->prepare($sql);
  $stmt->execute(array(':pid' => $profile_id, ':uid' => $user_id));
  $profile = $stmt->fetch(PDO::FETCH_ASSOC);
  if($profile==false){
      $_SESSION['error'] = 'Could not load profile';
  }
  return $profile;
}
// Count the number of existing skills
function get_skill_count($pdo) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM SkillSet where profile_id = :profileid");
    $stmt->execute(array(':profileid' => $_REQUEST['profile_id']));
    $obj = $stmt->fetch(PDO::FETCH_NUM);
    return $obj[0];
}
// Count the number of existing conditions
function get_position_count($pdo) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM Position where profile_id = :profileid");
    $stmt->execute(array(':profileid' => $_REQUEST['profile_id']));
    $obj = $stmt->fetch(PDO::FETCH_NUM);
    return $obj[0];
}
// function get_json($pdo) {
//     require_once "pdo.php";
//     session_start();
//     header('Content-Type: application/json; charset=utf-8');
//     $t = $_GET['term'];
//     echo $t;
//     error_log('Looking for type-ahead term '.$t);
//     $stmt = $pdo->prepare('SELECT name FROM Institution
//                       WHERE name LIKE :prefix');
//     $stmt->execute(array( ':prefix' => "%$t%" ) );
//     $row = $stmt->fetchColumn();
//     $retval[] = array();
//     while( $row = $stmt->fetchColumn()) {
//         $retval[] = $row['name'];
//     }
// return json_encode($retval, JSON_PRETTY_PRINT);
// }
