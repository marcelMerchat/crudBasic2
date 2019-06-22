<?php
// util.php utilities
function isMobile() {
    return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
}
function loadMobilityStyles() {
    if(isMobile()==1) {
        echo '<link rel="stylesheet" type="text/css" href="styleMobile.css">';
    } else {
        //require_once 'desktop.php';
        echo '<link rel="stylesheet" type="text/css" href="styleDesktop.css">';
    }
}
function flashMessages(){
    if ( isset($_SESSION['error']) ) {
          echo '<p class="message" style="color:red">'.$_SESSION['error'].'</p>';
          $_SESSION['error'] = '';
          unset($_SESSION['error']);
    }
    if ( isset($_SESSION['success']) ) {
          echo '<p class="message" style="color:green">'.$_SESSION['success'].'</p>';
          $_SESSION['success'] = '';
          unset($_SESSION['success']);
    }
    if ( isset($_SESSION['message']) ) {
    //       echo '<p class="message" style="color:green">'.$_SESSION['message'].'</p>';
           $_SESSION['message'] = '';
           unset($_SESSION['message']);
    }
}
function validateName($pdo) {
    $f_n = trim($_POST['first_name']);
    $l_n = trim($_POST['last_name']);
    if ( (strlen($f_n) > 0) && (strlen($l_n) > 0) ) {
        $f_nOffn = ofnsvCheck($f_n,$pdo);
        $l_nOffn = ofnsvCheck($l_n,$pdo);
        if($f_nOffn){
           $_SESSION['message'] = ' Submitted first name was invalid. ';
           return false;
        }
        if($l_nOffn){
           $_SESSION['message'] = $_SESSION['message'] .' Submitted last name was invalid. ';
           return false;
        }
    } else{
        $_SESSION['message'] = ' First and last names are required. ';
        return false;
    }
    return true;
}
//function insertName($pdo,$isUpdate) {

//}
function validateEmail($pdo) {
   $e_m = trim($_POST['email']);
   if ( strpos($_POST['email'],'@') === false ) {
      $_SESSION['message'] = ' Email address was invalid. ';
      store_error_messages();
      return false;
   }
   if(!( strlen($e_m) > 4) ) {
      $_SESSION['message'] = ' Email address is too short. ';
      store_error_messages();
      return false;
   }
   $emLen = strlen($e_m);
   if(!( strpos($_POST['email'],'@') > 0) ) {
      $_SESSION['message'] = ' Email name is required. ';
      store_error_messages();
      return false;
   } else {
     $emailArray = explode('@',$e_m);
     $emailName = $emailArray[0];
     $emailOrg = $emailArray[1];
     // only one @
     $emailLen = sizeof($emailArray);
     if(!( $emailLen == 2)) {
        $_SESSION['message'] = ' Email has wrong form.'.$emailLen;
        store_error_messages();
        return false;
     }
     // no spaces
     $nameArray = explode(" ",$emailName);
     $orgArray = explode(" ",$emailOrg);
     $nameArrayLen = sizeof($nameArray);
     $orgArrayLen = sizeof($orgArray);
     if(!( $nameArrayLen == 1 && $orgArrayLen == 1 )) {
        $_SESSION['message'] = ' Email name or org has wrong form. '.$nameArrayLen.' '.$orgArrayLen;
        store_error_messages();
        return false;
     }
   }
   $nameLen = strlen($emailName);
   if(!(  strlen($emailName) > 0 )) {
      $_SESSION['message'] = ' Email name is missing. ';
      store_error_messages();
      return false;
   }
   $orgTypeLen = $emLen - $nameLen - 1;
   if(!(  strlen($emailName) > 0 )) {
      $_SESSION['message'] = ' Email name is required. ';
      store_error_messages();
      return false;
   }
   $orgArray = explode(".",$emailOrg);
   $emailOrg = $orgArray[0]; // gmail
   $emailOrgType = $orgArray[1]; // .org
   if(!(  strlen($emailOrg) > 0)) {
      $_SESSION['message'] = ' Email org is required. ';
      store_error_messages();
      return false;
   }
      if(!(  strlen($emailOrgType) == 3 )) {
      $_SESSION['message'] = ' Email org type must have three letters. ';
      store_error_messages();
      return false;
   }
   $e_mOffn = ofnsvCheck($e_m,$pdo);
   if($e_mOffn){
       //$e_m = filterPhrase($pdo, $e_m);
       $_SESSION['message'] = 'Email address was rejected. Try a different address.';
       store_error_messages();
       return false;
   }
   store_error_messages();
   return true;
}
function insertEmail($pdo) {

  return true;
}
  // $sql = "UPDATE Profile SET email = :em WHERE profile_id = :pid ";
  //               $stmt = $pdo->prepare($sql);
  // $stmt->execute(array(':em' => $_POST['email'],
  //                      ':pid' => $_GET['profile_id']));
function insertProfile($pdo,$isUpdate) {
  //$nameInserted = insertName($pdo,$isUpdate);
  if ( ! isset($_POST['first_name']) ||
       ! strlen($_POST['first_name']) > 0 ||
       ! isset($_POST['last_name']) ||
       ! strlen($_POST['last_name']) > 0 ||
       ! isset($_POST['email']) ||
       ! strlen($_POST['email']) > 0
     )
     {
       $_SESSION['message'] = ' Valid name and email are required. ';
       store_error_messages();
       return false;
     } else {
        $fn = 'first_name';
        $ln = 'last_name';
        $f_n = trim($_POST[$fn]);
        $l_n = trim($_POST[$ln]);
        $f_n = censorPost($fn,$pdo);
        $f_n = trim($f_n);
        $l_n = censorPost($ln,$pdo);
        $l_n = trim($l_n);
        if ( ! strlen($f_n) > 0 || ! strlen($l_n) > 0 ) {
          $_SESSION['message'] = ' Name was removed. ';
          store_error_messages();
          return false;
        }
        if($isUpdate===true){
            $sql = 'UPDATE Profile SET first_name = :fnm, last_name = :lnm, email = :em
              WHERE profile_id = :pid';
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array(
             ':fnm' => $f_n, ':lnm' => $l_n,':em' => $_POST['email'],
             ':pid' => $_POST['profile_id']));
        } else {
          $sql = "INSERT INTO Profile (user_id, first_name, last_name, email) VALUES ( :uid, :fnm, :lnm, :em )";
          $stmt = $pdo->prepare($sql);
          $stmt->execute(array(
                ':uid' => $_SESSION['user_id'], ':fnm' => $f_n,
                ':lnm'  => $l_n,  ':em' => $_POST['email'] ));
          $_SESSION['profile_id'] = $pdo->lastInsertId() + 0;
        }
    }
}
// function insertProfile($pdo,$update) {
//     if ( ! isset($_POST['first_name']) ||
//          ! strlen($_POST['first_name']) > 0 ||
//          ! isset($_POST['last_name']) ||
//          ! strlen($_POST['last_name']) > 0 ||
//          ! isset($_POST['email']) ||
//          ! strlen($_POST['email']){
//         $_SESSION['message'] = ' Name is required. ';
//         store_error_messages();
//         return false;
//     }
//     $fn = 'first_name';
//     $ln = 'last_name';
//     $f_n = trim($_POST[$fn]);
//     $l_n = trim($_POST[$ln]);
//     $f_n = censorPost($fn,$pdo);
//     $f_n = trim($f_n);
//     $l_n = censorPost($ln,$pdo);
//     $l_n = trim($l_n);
//     if ( ! strlen($f_n) > 0 || ! strlen($l_n) > 0 ) {
//         $_SESSION['message'] = ' Name was removed. ';
//         store_error_messages();
//         return false;
//     }
//     if($update===true){
//         $sql = 'UPDATE Profile SET first_name = :fnm, last_name = :lnm, email = :em
//                 WHERE profile_id = :pid';
//                 $stmt = $pdo->prepare($sql);
//         $stmt->execute(array(
//                ':fn' => $f_n, ':lnm' => $l_n, ':em' => $l_n,
//                ':em' => $_POST['email'], ':pid' => $_POST['profile_id']));
//     } else {
//         $sql = "INSERT INTO Profile (user_id, first_name, last_name, email) VALUES ( :uid, :fnm, :lnm, :em )";
//         $stmt = $pdo->prepare($sql);
//         $stmt->execute(array(
//                   ':uid' => $_SESSION['user_id'], ':fn' => $f_n,
//                   ':ln'  => $l_n,  ':em' => $_POST['email'] ));
//     }
//         //$sql = "INSERT INTO Profile (user_id, first_name, last_name, email, profession, goal) VALUES ( :uid, :fn, :ln, :em, :prof, :goal )";
//         //    $stmt = $pdo->prepare($sql);
//         //    $stmt->execute(array(
//         //          ':uid' => $_SESSION['user_id'], ':fn' => $fn,
//         //          ':ln'  => $ln,  ':em' => $_POST['email'],
//         //          ':prof'  => $_POST['profession'], ':goal' => $_POST['goal']) );
//     store_error_messages();
//     return true;
// }
// Profession is always an UPDATE MYSQL COMMAND
// because name and email are used to INSERT the profil.
function insertProfession($profile_id,$pdo,$isUpdate) {
    if ( ! isset($_POST['profession']) ||
         ! strlen($_POST['profession']) > 0){
        if($isUpdate){
            $_SESSION['message'] = $_SESSION['message']
                .'Change for profession was rejected.';
        } else {
          $_SESSION['message'] = $_SESSION['message']
                .'Profession not entered. You may edit the profile.';
        }
        store_error_messages();
        return false;
    }
    $prof = censorPost('profession',$pdo);
    print_r('checking progress, ', $prof);
    $prof = trim($prof);
    if ( strlen($prof) == 0 ) {
        $_SESSION['message'] = $_SESSION['message']
                               .' Change for profession was rejected. ';
        store_error_messages();
        return false;
    }
    $sql = 'UPDATE Profile SET profession = :prof WHERE profile_id = :pid ';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(':prof' => $prof,
                         ':pid' => $profile_id));
    store_error_messages();
    return true;
}
// Professional goals are always an UPDATE MYSQL COMMAND
// because name and email are used to INSERT the profil.
function insertProfessionalGoals($profile_id,$pdo,$isUpdate) {
    if ( ! isset($_POST['goal']) ||
         ! strlen($_POST['goal']) > 0){
        if($isUpdate){
            $_SESSION['message'] = $_SESSION['message']
                .'Change for professional goal was rejected.
                  You may edit the profile.';
        } else {
            $_SESSION['message'] = $_SESSION['message']
                .'Profession goal not entered.
                 You may edit the profile.';
        }
        store_error_messages();
        return false;
    }
    $g_l = trim($_POST['goal']);
    $g_l = censorPost('goal',$pdo);
    $g_l = trim($g_l);
    if ( strlen($g_l) == 0 ) {
        $_SESSION['message'] = $_SESSION['message']
                               .' professional goals were completely removed. You may edit the profile. ';
        store_error_messages();
        //print_r(' zero length, aborting change ');
        return false;
    }
    $sql = "UPDATE Profile SET goal = :goal
                  WHERE profile_id = :pid ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
                  ':goal' => $g_l, ':pid' => $profile_id ) );
    store_error_messages();
    return true;
}
function validateInstitution() {
    for($i=1; $i<=9; $i++) {
        if ( ! isset($_POST['name'.$i]) ) continue;
        $institution = $_POST['name'.$i];
        if ( strlen($institution) == 0 ) {
            return "The name of the educational institution is required. ";
        }
    }
    return true;
}
function loadSkill($profile_id,$pdo) {
  $sql = 'SELECT Skill.name FROM SkillSet JOIN Skill
      ON Skill.skill_id = SkillSet.skill_id
      WHERE profile_id = :prof';
  $stmt = $pdo->prepare($sql);
  $stmt->execute(array(':prof' => $profile_id) );
  $skill = $stmt->fetchALL(PDO::FETCH_ASSOC);
  return $skill;
}
function loadPos($profile_id,$pdo) {
 $stmt = $pdo->prepare('SELECT * FROM Position
     WHERE profile_id = :prof ORDER BY rank');
 $stmt->execute(array(':prof' => $profile_id) );
 $positions = $stmt->fetchALL(PDO::FETCH_ASSOC);
 return $positions;
}
function loadEdu($profile_id,$pdo) {
  $sql = 'SELECT Education.year, Institution.name, Education.major FROM Education JOIN Institution
      ON Education.institution_id = Institution.institution_id
      WHERE Education.profile_id = :prof ORDER BY `rank`';
  $stmt = $pdo->prepare($sql);
  $stmt->execute(array(':prof' => $profile_id) );
  $education = $stmt->fetchALL(PDO::FETCH_ASSOC);
  return $education;
}
// InsertSkillSet is always an INSERT MYSQL COMMAND
// because the old set is always erased first
function insertSkillSet($profile_id,$pdo) {
    $rank = 1;
    for($i=1; $i<=12; $i++) {
      $skillNum = 'skill_name'.$i;
      if ( ! isset($_POST[$skillNum]) ||
           ! strlen($_POST[$skillNum]) > 0){
          continue;
      }
      $skill = trim($_POST[$skillNum]);
      $skill = censorPost($skillNum,$pdo);
      $skill = trim($skill);
      if ( ! strlen($skill) > 0 ) {
          $_SESSION['message'] = $_SESSION['message']
                                 .' a skill was completely removed. ';
          store_error_messages();
          continue;
      }
  //  lookup the skill in the Skill Table
      $sql = 'SELECT skill_id FROM Skill WHERE name = :nme';
      $stmt = $pdo->prepare($sql);
      $stmt->execute(array(':nme' => $skill) );
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      //print_r($row);
      //var_dump($row);
      $skill_id = false;
      if($row !== false) {
          $skill_id = $row['skill_id'] + 0;
      }
  //  if skill does not exist in Skill table, insert it
      if($skill_id === false) {
          $sql = 'INSERT INTO Skill (`name`) VALUES (TRIM(:nme))';
          $stmt = $pdo->prepare($sql);
          $stmt->execute(array(':nme' => trim($skill)) );
          // Retrieve the newly assigned skill_id
          $skill_id = $pdo->lastInsertId() + 0;
      }
      $sql = 'INSERT INTO SkillSet (profile_id, skill_id, `rank`)
                    VALUES ( :pid, :sid, :rnk)';
      $stmt = $pdo->prepare($sql);
      $stmt->execute(array(
                  ':pid' => $profile_id, ':sid' => $skill_id,
                  ':rnk' => $rank ) );

      $rank++;
      }
      store_error_messages();
      return true;
}
// InsertEducation is always an INSERT MYSQL COMMAND
// because the old set is always erased first
function insertEducations($profile_id,$pdo) {
    $rank = 1;
    $count = 0;
    for($i=1; $i<=9; $i++) {
       $yearNum = 'edu_year'.$i;
       $schoolNum = 'edu_school'.$i;
       $majorNum = 'edu_major'.$i;
       if ( ! isset($_POST[$yearNum]) ) {
        // The year not required
       } else {
          $year = $_POST['edu_year'.$i];
          if ( strlen($year) == 0 ) {
                  $_SESSION['message'] = $_SESSION['message']
                                     .'The education year was not provided for edu'.$i.'. ';
                  store_error_messages();
          }
          if ( strlen($year) > 0 && (! is_numeric($year)) ) {
                 $_SESSION['message'] = $_SESSION['message']
                                     .'The education year must be numeric for edu'.$i.'. ';
                  store_error_messages();
                  continue;
          }
    }
    if (
         ! isset($_POST[$schoolNum]) || !(strlen($_POST['edu_school'.$i]) > 0) ||
         ! isset($_POST[$majorNum])  || !(strlen($_POST['edu_major'.$i]) > 0 )
    ) continue;
    $school = trim($_POST['edu_school'.$i]);
//  Name of school cannot be changed or filtered only rejected.
    $bad = ofnsvCheck($school,$pdo);
    if($bad ) {
          $_SESSION['message'] = $_SESSION['message']
                                         .' Name for school'.$i.' was rejected. ';
          store_error_messages();
          continue;
    }
    $major = trim($_POST[$majorNum]);
    $major = censorPost($majorNum,$pdo);
    $major = trim($major);
    if ( ! strlen($major) > 0 ) {
                  $_SESSION['message'] = $_SESSION['message']
                   .' The education for '.$major.' was rejected. ';
            //     //print_r(' Skill with zero length, aborting change ');
                  store_error_messages();
                  continue;
    }
   // lookup the school
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
    $sql = 'INSERT INTO Education (profile_id, institution_id, `rank`)
                    VALUES ( :pid, :iid, :rnk)';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
                  ':pid' => $profile_id, ':iid' => $institution_id,
                  ':rnk' => $rank ));
    $sql = 'UPDATE Education SET major = :maj WHERE
         (profile_id = :pid) AND (institution_id = :iid) AND ( `rank` = :rnk)';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(':pid' => $profile_id,
                   ':iid' => $institution_id,
                   ':rnk' => $rank, ':maj' => $major ) );
//  The year is not required
    if ( isset($_POST['edu_year'.$i]) && (strlen($_POST['edu_year'.$i]) > 0) ) {
          $year = $_POST['edu_year'.$i];
          $sql = 'UPDATE Education SET year = :yr WHERE
          (profile_id = :pid) AND (institution_id = :iid) AND ( `rank` = :rnk)';
          $stmt = $pdo->prepare($sql);
          $stmt->execute(array(':pid' => $profile_id,':iid' => $institution_id,
                               ':rnk' => $rank,      ':yr' => $year ) );
    }
    $rank++;
    $count++;
    }
    if($rank > 0){
        //$_SESSION['success'] = "There are ".$rank." education entries.";
    }
    $_SESSION['countSchools'] = $count;
}
function insertPositions($profile_id,$pdo) {
    $rank = 1;
    $count = 0;
    for($i=1; $i<=9; $i++) {
       if ( isset($_POST['wrkStartYr'.$i]) && isset($_POST['org'.$i]) &&
            isset($_POST['wrkFinalYr'.$i]) && isset($_POST['desc'.$i]) &&
            strlen($_POST['org'.$i]) > 0 && (strlen($_POST['desc'.$i]) > 0) ) {
              if ( ! isset($_POST['wrkStartYr'.$i]) ) continue;
              if ( ! isset($_POST['wrkFinalYr'.$i]) ) continue;
              if ( ! isset($_POST['org'.$i]) ) continue;
              if ( ! isset($_POST['desc'.$i]) ) continue;
              $yearStart = $_POST['wrkStartYr'.$i];
              $yearLast = $_POST['wrkFinalYr'.$i];
              $org = $_POST['org'.$i];
              $desc = $_POST['desc'.$i];
              if ( ! (is_numeric($yearStart) && is_numeric($yearLast)) ) {
                  $_SESSION['message'] = $_SESSION['message'].'. Position years must be numeric for. '.$org;
                  store_error_messages();
                  continue;
              }
              if ( strlen($org) == 0 ) {
                  $_SESSION['message'] = $_SESSION['message'].'. Organization field is required for year '.$yearStart;
                  store_error_messages();
                  continue;
              }
              if ( strlen($desc) == 0 || strlen($desc) == 0 ) {
                  $_SESSION['message'] = $_SESSION['message'].' Position description is required for. '.$org;
                  store_error_messages();
                  continue;
              }
              $org = trim($_POST['org'.$i]);
              $offOrg = ofnsvCheck($org, $pdo);
              if($offOrg==true){
                  $org = trim(filterPhrase($org,$pdo));
                  $_SESSION['message'] = $_SESSION['message']
                                     .' Language filter for organization '.$org.' was triggered. ';
                  store_error_messages();
              }
              $positionid = 9999;
              if ( strlen($org) > 0 ) {
                $stmt = $pdo->prepare('INSERT INTO Position (
                        profile_id, `rank`, yearStart, yearLast, organization)
                             VALUES ( :pid, :rnk, :yrStart, :yrLast, TRIM(:org)) ');
                $stmt->execute(array(
                         ':pid' => $profile_id,  ':rnk' => $rank,
                         ':yrStart' => $_POST['wrkStartYr'.$i],
                         ':yrLast' => $_POST['wrkFinalYr'.$i],
                         ':org' => $org));
                $positionid = $pdo->lastInsertId() + 0;
                $rank++;
              } else {
                    $_SESSION['message'] = $_SESSION['message']
                          .'The profile was saved but at least one position
                            was not entered, you can edit the profile.
                            The organization for years '
                            .$_POST['wrkStartYr'.$i].'-'.$_POST['wrkFinalYr'.$i]
                            .' is missing.  ';
                    store_error_messages();
                    continue;
              }
              $desc = trim($_POST['desc'.$i]);
              $offDesc = ofnsvCheck($desc, $pdo);
              if($offDesc==true){
                 //print_r(' Filtering desc . ');
                 $desc = trim(filterPhrase($desc,$pdo));
                 //print_r($desc.' ');
                 $_SESSION['message'] = $_SESSION['message']
                                    .' Language filter may have triggered for
                                       job description '.$desc;
                 store_error_messages();
              }
              if ( strlen($desc) > 0 ) {
                  $sql = 'UPDATE Position SET description = TRIM(:de)
                            WHERE position_id = :posid';
                  $stmt = $pdo->prepare($sql);
                  $stmt->execute(array(':de'  => $desc,
                                       ':posid' => $positionid ));
              } else {
                  $_SESSION['message'] = $_SESSION['message']
                                   .'The description for position'.$i.' is missing. ';
                  store_error_messages();
              }
       } else {
         //$_SESSION['success'] = $_SESSION['success'].' test failed to start - ';
       }
    }
    $_SESSION['countPosition'] = $rank - 1;
}
function get_name($user_id,$pdo) {
    $stmt = $pdo->prepare('SELECT name FROM users WHERE user_id= :id');
    $stmt->execute(array(':id' => $user_id) );
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $fullname = array_values($row)[0];
    return $fullname;
}
function get_profile_information($profile_id, $user_id,$pdo) {
  // Retrieve the profile information for the profile_id
  $sql = 'SELECT * FROM Profile WHERE profile_id = :pid AND user_id = :uid';
  $stmt = $pdo->prepare($sql);
  $stmt->execute(array(':pid' => $profile_id, ':uid' => $user_id));
  $profile = $stmt->fetch(PDO::FETCH_ASSOC);
  if($profile==false){
      $_SESSION['message'] = 'Could not load profile';
  }
  store_error_messages();
  return $profile;
}
function store_error_messages() {
  if(isset($_SESSION['error'])  &&  isset($_SESSION['message']) ) {
      $_SESSION['error'] = $_SESSION['error'].' '.$_SESSION['message'];
      //$_SESSION['error'] = 'Check here';
  } else if( isset($_SESSION['message']) && (!isset($_SESSION['error']))) {
      $_SESSION['error'] = $_SESSION['message'];
  }
  unset($_SESSION['message']);
  $_SESSION['message'] = '';
}
// Count the number of existing skills
function get_skill_count($profile_id, $pdo) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM SkillSet where profile_id = :profileid");
    $stmt->execute(array(':profileid' => $profile_id));
    $obj = $stmt->fetch(PDO::FETCH_NUM);
    return $obj[0];
}
// Count the number of existing conditions
function get_position_count($profile_id,$pdo) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM Position where profile_id = :profileid");
    $stmt->execute(array(':profileid' => $profile_id));
    $obj = $stmt->fetch(PDO::FETCH_NUM);
    return $obj[0];
}
function multiexplode ($delimiters,$string) {
    $ready = str_replace($delimiters, $delimiters[0], $string);
    $launch = explode($delimiters[0], $ready);
    return  $launch;
}
function censorPost($postField,$pdo) {
   $field = trim($_POST[$postField]);
   $bad = ofnsvCheck($field,$pdo);
   if($bad){
       $field = filterPhrase($field,$pdo);
       $_SESSION['message'] = $_SESSION['message']
                              .' Word in '.$postField.' was modified. ';
   }
   store_error_messages();
   return $field;
}
function getPhraseArray($phrase) {
    $newPhrase = '';
    $replaced ='';
    $punct = array(',','.','!','?','@');
    $replace = array(' ');
    foreach ($punct as $punc) {
            $replaced = str_replace($punc,' ',$phrase);
    }
    $exploded = explode(' ',$replaced);
    return  $exploded;
}
function ofnsvCheck($phrase, $pdo) {
    $offense = false;
    $exploded = getPhraseArray($phrase);
    foreach ($exploded as $word) {
        $check = checkDictionary($word,$pdo);
        if($check == true) {
                // Found in dictionary; do nothing;
        } else {
                // Word is not in main dictionary
                // Rather than find our word with an offensive word, we need the Reverse
                // We use the mySQL CONCAT operator to find offensive words as internal substrings
                // find "abcMyOffensiveWordabc"
                // (A) Reject offensive words with exactly 3-letters
                // (B) Accept longer words that contain offensive 3-letter words internally.
                $offense = checkOffensiveList($word, $pdo);
                if($offense == true) {
                    break;
                }
             }
    }
    return $offense;
}
function checkOffensiveList($word, $pdo){
   $offensive = true;
   $stmt = $pdo->prepare('SELECT COUNT(*) FROM Offensive WHERE (? = word) AND (LENGTH(word) = 3) OR ((? LIKE CONCAT("%", word, "%")) AND LENGTH(word) > 3)');
   $stmt->execute([$word, $word]);
   $rowdata2 = $stmt->fetch(PDO::FETCH_ASSOC);
   // COUNT(*) does not return an array.
   $cnt = array_values($rowdata2)[0];
   if($cnt > 0) {

   } else {
       $offensive = false;
   }
   return $offensive;
}
function checkDictionary($word, $pdo){
   $stmt = $pdo->prepare('SELECT COUNT(*) FROM Dictionary WHERE word = ?');
   $stmt->execute(array($word));
   $rowdata = $stmt->fetch(PDO::FETCH_ASSOC);
   $cnt = $stmt->fetch(PDO::FETCH_COLUMN);
   //print_r('direct count '.$cnt);
   $cnt = array_values($rowdata)[0];
   if($cnt > 0) {
     return true; // Found in dictionary; do nothing;
   } else {
     return false;
   }
}
function filterPhrase($phrase, $pdo) {
    $newPhrase = '';
    $exploded = getPhraseArray($phrase);
    foreach ($exploded as $word) {
        $check = checkDictionary($word, $pdo);
        if($check === true) {
            // Word exists in main dictionary
            $newPhrase = trim($newPhrase.' '.$word);
        } else {
            // Word is not in main dictionary
            // Rather than find our word with an offensive word, we need the Reverse
            // We use the mySQL CONCAT operator to find offensive words as internal substrings
            // find "abcMyOffensiveWordabc"
            // (A) Reject offensive words with exactly 3-letters
            // (B) Accept longer words that contain offensive 3-letter words internally.
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM Offensive WHERE (? = word) AND (LENGTH(word) = 3) OR ((? LIKE CONCAT("%", word, "%")) AND LENGTH(word) > 3)');
            $stmt->execute([$word, $word]);
            $cnt2 = $stmt->fetch(PDO::FETCH_COLUMN);
            $chk = checkOffensiveList($word, $pdo);
            if($chk == true) {
               //$_SESSION['message'] = 'Rejected word at : '.$word.'. ';
            } else {
               $newPhrase = trim($newPhrase.' '.$word);
            }
        }
    }
    return trim($newPhrase);
}
function generateRandomString($length = 8) {
    $characters = '123456789abcdefghijkmnopqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
