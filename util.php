<?php
// util.php utilities
function isMobile() {
    return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
}
function loadMobilityStyles() {
    if(isMobile()==1) {
        echo '<link rel="stylesheet" type="text/css" href="styleMobile.css">';
    } else {
        echo '<link rel="stylesheet" type="text/css" href="styleDesktop.css">';
    }
}
function flashMessages(){
    if ( isset($_SESSION['error']) ) {
          echo '<p class="message" style="color: #aa4400">'.$_SESSION['error'].'</p>';
          $_SESSION['error'] = '';
          unset($_SESSION['error']);
    }
    if ( isset($_SESSION['success']) ) {
          echo '<p class="message" style="color:green">'.$_SESSION['success'].'</p>';
          $_SESSION['success'] = '';
          unset($_SESSION['success']);
    }
    if ( isset($_SESSION['message']) ) {
           $_SESSION['message'] = '';
           unset($_SESSION['message']);
    }
}
function get_mysql_time_stamp($mailaddress='marcosmoothy@gmail.com',
                              $mysql_field='password_time',$pdo){
    $sql = 'SELECT '.$mysql_field.' FROM users WHERE email = :em';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array( ':em' => $mailaddress));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row[$mysql_field];
}
function getElapsedSeconds($mailaddress,$mysql_field='password_time',$pdo){
  $mysql_field='password_time';
  $ts = get_mysql_time_stamp($mailaddress,$mysql_field,$pdo);
  $request_time = date("Y-m-d H:i:s", strtotime($ts));
  $current_time = date("Y-m-d H:i:s");
  $requested = new DateTime($request_time);
  $current = new DateTime($current_time);
  $interval = $requested->diff($current);
  $days = $interval->d;
  $hrs = $interval->h;
  $mins = $interval->i;
  $secs = $interval->s;
  //$totalmins = 24*60*$days+60*$hrs + $mins;
  $totalseconds = 24*3600*$days + 3600*$hrs + 60*$mins + $secs;
  return $totalseconds;
}
function isPassWordSet($mail_address,$mysql_field,$pdo){
    $sql = "SELECT email, random, password_time, timeout, block FROM users WHERE email = :em";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array( ':em' => $mail_address));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $totalsecs = -1;
    if($row['timeout'] == 1){
        $mysql_field = 'password_time';
        $totalsecs = getElapsedSeconds($mail_address,$mysql_field,$pdo);
        if($totalsecs > 3000) {
            $_SESSION['error'] =
               '<p class="center message">Temporary passwword expired after'
                 .' 30-minutes.'
                 .' Go to the <a href="forgotpass.php">replace password</a>'
                 .' page to obtain a new password.</p>';
            error_log('Temporary password expired for '.$_SESSION['email']);
            return "PastTimeout";
        } else if ( !($totalsecs > 0) ){
            unset($_SESSION['user_id']);
            $_SESSION['error'] = 'Something went wrong. To get a new password, '
                 .' contact the administrator at merchatDataTools@gmail.com or '
                 .' call 773-852-1689. ';
            error_log('Time difference was not greater than zero for '.$_SESSION['email']);
            return "Something went wrong. ";
        } else {
            $_SESSION['error'] =
              '<p class="center message">Temporary password will expire within'
              .' approximately 30 minutes.'
              .' Please <a href="changePassword.php">reset</a>'
              .' before timeout.</p>';
            return "SetTimeout";
        }
    } else {
            return "OK";
    }
}
function get_text_input_validation($field_name,$long_name,$pdo) {
      $valid = true;
      $offn = false;
      if ( ! isset($_POST[$field_name]) ){
          return false;
      }      $trimmed = trim($_POST[$field_name]);
      if ( ! (strlen($trimmed) > 0)  )  {
          return false;
      }
      if ( strlen($trimmed) > 2 ) {
            $offn = ofnsvCheck( $trimmed, $pdo);
      }
      if ( $offn ) {
         $_SESSION['message'] = $_SESSION['message']
           .' The '.$long_name.' was rejected. ';
         store_error_messages();
         return false;
      }
      return $valid;
}
function get_censored_input($field_name,$long_name,$pdo) {
      $text_output = "";
      $delete = true;
      $valid = true;
  //  $offn is the offensive boolean variable
      $offn = false;
      if ( ! isset($_POST[$field_name]) ){
        //$_SESSION['message'] = $_SESSION['message']
          //.' A '.$long_name.' was not posted. ';
          //store_error_messages();
          return array(false,"");
      }
      $trimmed = trim($_POST[$field_name]);
      if ( ! (strlen($trimmed) > 0)  )  {
            //$_SESSION['message'] = $_SESSION['message']
              //.' A '.$long_name.' was not provided. ';
              //store_error_messages();
              return array(false,"");
      }
      if ( ! $valid) {return array(false,"");}
      if ( strlen($trimmed) > 2 ) {
            $offn = ofnsvCheck( $trimmed, $pdo);
      }
      if ( $offn ) {
            $valid = false;
            $censored = censorPost($field_name,$pdo);
            $censored = trim($censored);
            $diff = strlen($trimmed) - strlen($censored);
            $trimmed = $censored;
            $_SESSION['message'] = $_SESSION['message']
                .' The '.$long_name.' generated a warning: "'
                .$_POST[$field_name].'." ';
            if ( ! (strlen($trimmed) > 0) ) {
               $_SESSION['message'] = $_SESSION['message']
                  .' The '.$long_name.' was removed. ';
            } else {
                $_SESSION['message'] = $_SESSION['message']
                  .' After any deletions, it becomes "'.$censored.'". ';
            }
      }
      return array($valid,$trimmed);
}
function validate_year($year_string){
  $year_string = trim($year_string);
  if ( strlen($year_string) > 3 && ctype_digit($year_string) ) {
       $num = (int) $year_string;
       if( $num > 999 && $num < 10000 ) {
          return true;
       } else {
          $_SESSION['message'] = $_SESSION['message']
               .' Please enter a 4-digit year. ';
          store_error_messages();
          return false;
       }
  } else {
     // $_SESSION['message'] = $_SESSION['message']
     //   .' A 4-digit year is required. ';
     // store_error_messages();
     return false;
  }
  return false;
}
// convert year string to type 'int'
function get_year($year_string,$message){
  $valid = validate_year($year_string);
  $year_int = 9999;
  if ( $valid ) {
       $year_string = trim($year_string);
       $year_int = (int) $year_string;
  } else {
       $_SESSION['message'] = $_SESSION['message'].$message;
       store_error_messages();
       return false; //false
  }
  return $year_int;
}
function validateName($n,$pdo) {
     $n = trim($n);
     if ( strlen($n) > 0) {
          $badChar= preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $n);
          if($badChar){
               $_SESSION['message'] = $_SESSION['message']
                  .'Invalid character. Please use letters and numbers. ';
          return false;
          }
     }
     return true;
}
function validateEmail($pdo) {
   $e_m = trim($_POST['email']);
   if ( strpos($_POST['email'],'@') === false ) {
      $_SESSION['message'] = $_SESSION['message']
          .' Email address was invalid. ';
      store_error_messages();
      return false;
   }
   if(!( strlen($e_m) > 6) ) {
      $_SESSION['message'] = $_SESSION['message']
          .' Email address is too short. ';
      store_error_messages();
      return false;
   }
   $em_len = strlen($e_m);
   if( ! ( strpos($_POST['email'],'@') > 0) ) {
      $_SESSION['message'] = $_SESSION['message']
          .' Email name is required. ';
      store_error_messages();
      return false;
   }
   $emailArray = explode('@',$e_m);
   $email_name = $emailArray[0];
   $email_tail = $emailArray[1];
   $array_size = sizeof($emailArray);
   if(( $array_size == 2) && !(strlen($email_tail) > 4) ) {
        $_SESSION['message'] = $_SESSION['message']
            .' Email section after "at" symbol is too short. '.$email_name.$email_tail;
        store_error_messages();
        return false;
   }
// no spaces
   $badCharName = preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $email_name);
   $badCharTail  = preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $email_tail);
   if($badCharName || $badCharTail) {
          $_SESSION['message'] = $_SESSION['message']
              .'Email has illegal character. '.$email_tail.$badCharTail;
          store_error_messages();
          return false;
   }
// no internal spaces
   $nameArray = explode(" ",$email_name);
   $tailArray = explode(" ",$email_tail);
   $nameArrayLen = sizeof($nameArray);
   $tailArrayLen = sizeof($tailArray);
   if(!( $nameArrayLen == 1 && $tailArrayLen == 1 )) {
        $_SESSION['message'] = $_SESSION['message']
                  .' Email address has spaces in it. ';
        store_error_messages();
        return false;
   }
// Check parts
// length of parts
   $name_len = strlen($email_name);
   $tail_len = strlen($email_tail);
   $tailArray = explode(".",$email_tail);
   $last_index = sizeof($tailArray) - 1;
   $email_org_type = $tailArray[$last_index];
   $email_org_type_len = strlen($email_org_type);
   $domain_name = substr($email_tail, 0, $tail_len - 4);
   $domain_name_len = strlen($domain_name);
   if(!( $last_index > 0)) {
        $_SESSION['message'] = $_SESSION['message']
        .' Email ending is missing the '
        .' "." symbol. ';
        store_error_messages();
        return false;
   }
   if( ! ($name_len > 0)) {
      $_SESSION['message'] = $_SESSION['message']
          .' Email name is missing. ';
      store_error_messages();
      return false;
  }
  $org_type_length = strlen($email_org_type);
  if(!(  strlen($domain_name) > 0 ) ) {
        $_SESSION['message'] = $_SESSION['message']
          .' The organization domain name for the email address is missing. ';
        store_error_messages();
        return false;
   }
   if(!(  strlen($email_org_type) == 3 )) {
        $_SESSION['message'] = $_SESSION['message']
          .' The organization type for the email'
            .' address must have three letters such as ".com" and ".org." ';
        store_error_messages();
        return false;
   }
   $em_name_offn = ofnsvCheck( $email_name, $pdo);
   $em_domain_name_offn = ofnsvCheck($domain_name, $pdo);
   $em_org_type_offn = ofnsvCheck( $email_org_type, $pdo);
   if($em_name_offn || $em_domain_name_offn || $em_org_type_offn){
        $_SESSION['message'] = $_SESSION['message']
           .'Email address was rejected. ';
        store_error_messages();
        return false;
   }
   return true;
}
function insertProfile($pdo,$isUpdate) {
    if
     ( ! isset($_POST['first_name']) ||
       ! strlen($_POST['first_name']) > 0 ||
       ! isset($_POST['last_name']) ||
       ! strlen($_POST['last_name']) > 0 ||
       ! isset($_POST['email']) ||
       ! strlen($_POST['email']) > 0
     )
     {
       $_SESSION['message'] = $_SESSION['message']
                .' First name, last name, and email are required. ';
       store_error_messages();
       return false;
     } else {
        $f_n = trim($_POST['first_name']);
        $l_n = trim($_POST['last_name']);
        $e_m = trim($_POST['email']);
        if ( ! strlen($f_n) > 0 || ! strlen($l_n) > 0 ) {
          $_SESSION['message'] = $_SESSION['message']
              .' Name is missing. ';
          store_error_messages();
          return false;
        }
        $valid_first = get_text_input_validation('first_name','first name',$pdo);
        $valid_last = get_text_input_validation('last_name','last name',$pdo);
    //  Check for illegal characters in name
        $valid_fn = validateName($f_n,'first name',$pdo);
        $valid_ln = validateName($l_n,'last name',$pdo);
        $valid_email = validateEmail($pdo);
        $valid = $valid_first && $valid_last &&
                 $valid_email &&
                 $valid_fn && $valid_ln;
        if(! $valid ) {
            store_error_messages();
            return false;
        }
        if($isUpdate===true){
            $sql = 'UPDATE Profile SET first_name = :fnm, last_name = :lnm, email = :em
              WHERE profile_id = :pid';
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array(
             ':fnm' => $f_n, ':lnm' => $l_n,':em' => $e_m,
             ':pid' => $_POST['profile_id']));
        } else {
          $sql = "INSERT INTO Profile (user_id, first_name, last_name, email) VALUES ( :uid, :fnm, :lnm, :em )";
          $stmt = $pdo->prepare($sql);
          $stmt->execute(array(
                ':uid' => $_SESSION['user_id'], ':fnm' => $f_n,
                ':lnm'  => $l_n,  ':em' => $e_m ));
          $_SESSION['profile_id'] = $pdo->lastInsertId() + 0;
        }
        return true;
    }
}
function insertEmail($pdo,$isUpdate) {
    if
     ( ! isset($_POST['email']) ||
       ! strlen($_POST['email']) > 0
     )
     {
       $_SESSION['message'] = $_SESSION['message']
                .' E-mail address is required. ';
       store_error_messages();
       return false;
     } else {
        $e_m = trim($_POST['email']);
        $valid_email = validateEmail($pdo);
        $valid = $valid_email;
        if(! $valid ) {
            store_error_messages();
            return false;
        }
        $sql = 'UPDATE Profile SET email = :em
              WHERE profile_id = :pid';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
             ':em' => $e_m,
             ':pid' => $_POST['profile_id']));
        return true;
    }
}
function changeResumeStyle($pdo) {
  if ( isset($_POST['resume_type']))
  {
    $style = trim($_POST['resume_type']);
    $_SESSION['message'] = $_SESSION['message']
             .' Changing resume style to '.$_POST['resume_type'];
    store_error_messages();
    $sql = 'UPDATE Profile SET resume_style = :rs
              WHERE profile_id = :pid';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
         ':rs' => $style,
         ':pid' => $_POST['profile_id']));
    return true;
  } else {
    // $_SESSION['message'] = $_SESSION['message']
    //          .' No resume style was provided. ';
    // store_error_messages();
    return false;
  }
}
function insertPhone($pdo) {
    if
     ( ! isset($_POST['phone']) ||
       ! strlen($_POST['phone']) > 0
     )
     {
       $_SESSION['message'] = $_SESSION['message']
                .' No phone number was provided. ';
       store_error_messages();
       return false;
     } else {
        $phone = trim($_POST['phone']);
        $valid_phone = true; // validateEmail($pdo);
        $valid = $valid_phone;
        if(! $valid ) {
            store_error_messages();
            return false;
        }
        $sql = 'UPDATE Profile SET phone = :ph
              WHERE profile_id = :pid';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
             ':ph' => $phone,
             ':pid' => $_POST['profile_id']));
        return true;
    }
}
function insertLinkedin($pdo) {
    if
     ( ! isset($_POST['linkedin']) ||
       ! strlen($_POST['linkedin']) > 0
     )
     {
       $_SESSION['message'] = $_SESSION['message']
                .' No linked-in address was provided. ';
       store_error_messages();
       return false;
     } else {
        $linkedin = trim($_POST['linkedin']);
        $valid = true; // validateEmail($pdo);
        if(! $valid ) {
           return false;
        }

        $sql = 'UPDATE Profile SET linkedin = :li
                   WHERE profile_id = :pid';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
                  ':li' => $linkedin,
                  ':pid' => $_POST['profile_id']));
        return true;
    }
}
function insertProfession($profile_id,$pdo,$isUpdate) {
    $prof = trim($_POST['profession']);
    $valid = get_text_input_validation('profession','profession',$pdo);
    if( ! ($valid===true)){
        $prof = get_censored_input('profession','profession',$pdo)[1];
    }
    $sql = 'UPDATE Profile SET profession = :prof WHERE profile_id = :pid ';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(':prof' => $prof,
                      ':pid' => $profile_id));
    return $valid;
}
// Professional goals are always an UPDATE MYSQL COMMAND
// because name and email are used to INSERT the profil.
function insertProfessionalGoals($profile_id,$pdo,$isUpdate) {
  $g_l = trim($_POST['goal']);
  $valid = get_text_input_validation('goal','professional goal',$pdo);
  if( ! ($valid===true)){
      $g_l = get_censored_input('goal','professional goal',$pdo)[1];
  }
  $sql = "UPDATE Profile SET goal = :goal WHERE profile_id = :pid ";
  $stmt = $pdo->prepare($sql);
  $stmt->execute(array(
            ':goal' => $g_l, ':pid' => $profile_id ) );
  return $valid;
}

function insertContactList($pdo) {
    $rank = 1;
    for($i=1; $i<=5; $i++) {
      $field_name = 'contact'.$i;
      if ( ! isset($_POST[$field_name]) ){
          continue;
      }
      //$_SESSION['message'] = $_SESSION['message']
        //                   .$field_name. ' is set with value '.$_POST[$field_name];
        //store_error_messages();

      $info = trim($_POST[$field_name]);
      if ( ! strlen($info) > 0){
        $_SESSION['message'] = $_SESSION['message']
                             .' trimmed too short: '.$field_name;
          store_error_messages();
          continue;
      }
      $text_insert = 'Contact-'.$i;
      $valid = get_text_input_validation($field_name,$text_insert,$pdo);
      if ( ! $valid) {
          $_SESSION['message'] = $_SESSION['message']
                               .' Contact-'.$i.' was rejected for '
                               .': '.$_POST[$field_name].'; ';
          store_error_messages();
          continue;
      }
      //$_SESSION['message'] = $_SESSION['message']
        //                   .' Contact info was validated for '
          //                 .': '.$_POST[$field_name].'; ';
      //store_error_messages();
  //  lookup the contact in the Contacts Table
      $sql = 'SELECT COUNT(*) FROM Contact WHERE info = :info';
      $stmt = $pdo->prepare($sql);
      $stmt->execute(array(':info' => $info) );
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      $cnt = (int) array_values($row)[0];
      //  If the contact already exists, retrieve it using the Contacts ID number.
      $contact_id = false;
      if($cnt > 0) {
          //$_SESSION['message'] = $_SESSION['message']
            //                 .' Positive count exists:  ';
          //store_error_messages();
          $sql = 'SELECT contact_id FROM Contact WHERE info = :info';
          $stmt = $pdo->prepare($sql);
          $stmt->execute(array(':info' => $info) );
          $row = $stmt->fetch(PDO::FETCH_ASSOC);
          $contact_id = $row['contact_id'] + 0;
          //$_SESSION['message'] = $_SESSION['message'].' Found contact with id = '
            //               .$contact_id;
          //store_error_messages();
      }
  //  Look for duplicate contact
      $sqldup = 'SELECT COUNT(*) FROM ContactList WHERE profile_id = :pid AND contact_id = :cid';
      $stmtdup = $pdo->prepare($sqldup);
      $stmtdup->execute(array(':pid' => $_POST['profile_id'], ':cid' => $contact_id) );
      $rowcountdup = $stmtdup->fetch(PDO::FETCH_ASSOC);
      $cntduplicate = (int) array_values($rowcountdup)[0];
      if ($cntduplicate > 0){
        //$_SESSION['message'] = $_SESSION['message'].' Duplicate contact found'
          // .' for "'.$contact.'". ';
           //' ('.$cntduplicate.'). ';
        // store_error_messages();
        continue;
      }
  //  if contact does not exist in Contact table, insert it
      if($cnt === 0) {
          $sql = 'INSERT INTO Contact (info) VALUES (TRIM(:info))';
          $stmt = $pdo->prepare($sql);
          $stmt->execute(array(':info' => $info) );
          // Retrieve the newly assigned skill_id
          $contact_id = $pdo->lastInsertId() + 0;
      }
      //$_SESSION['message'] = $_SESSION['message']
        //                 .' Ready for insertion:  '.$contact_id;
      //store_error_messages();
      if (is_numeric($contact_id) && $contact_id > 0 ) {
          $sql = 'INSERT INTO ContactList(profile_id, contact_id, `rank`)
                    VALUES ( :pid, :cid, :rnk)';
          $stmt = $pdo->prepare($sql);
          $stmt->execute(array(
                  ':pid' => $_POST['profile_id'], ':cid' => $contact_id,
                  ':rnk' => $rank ) );
          $rank++;
      }
      }
      return true;
}
// InsertSkillSet is always an INSERT MYSQL COMMAND
// because the old set is always erased first
function insertSkillSet($profile_id,$pdo) {
    $rank = 1;
    for($i=1; $i<=12; $i++) {
      $field_name = 'skill_name'.$i;
      store_error_messages();
      if ( ! isset($_POST[$field_name]) ){
         // $_SESSION['message'] = $_SESSION['message']
         //                        .' The entry box for '
         //                        .' Skill-'.$i.' was empty. ';
        //store_error_messages();
        continue;
      }
      // $_SESSION['message'] = $_SESSION['message']
      //                         .' The entry box for '
      //                         .' Skill-'.$i.' is now evaluated. ';
      $skill = trim($_POST[$field_name]);
      if ( ! strlen($skill) > 0){
         $_SESSION['message'] = $_SESSION['message']
                                .' The entry box for '
                                .' Skill-'.$i.' was not completed. ';
        store_error_messages();
        continue;
      }
      $len = strlen($skill);
      if ($len > 125){
              $_SESSION['message'] = $_SESSION['message'].'Skill '
                  .' of length '.$len.' exceeds maximum length of 125 '
                  .' characters for '.$skill.'". ';
              store_error_messages();
              continue;
      }
      $text_insert = 'Skill-'.$i;
      $valid = get_text_input_validation($field_name,$text_insert,$pdo);
      if ( ! $valid) {
          $_SESSION['message'] = $_SESSION['message']
                               .' Skill-'.$i.' was rejected for '
                               .': '.$_POST[$field_name].'; ';
          store_error_messages();
          continue;
      }
  //  lookup the skill in the Skill Table
      $sql = 'SELECT COUNT(*) FROM Skill WHERE name = :nme';
      $stmt = $pdo->prepare($sql);
      $stmt->execute(array(':nme' => $skill) );
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      $cnt = (int) array_values($row)[0];
       // $_SESSION['message'] = $_SESSION['message']
       //     .' For Skill-'.$i.', the count in the database is '
       //     .': '.$cnt.'; ';
      //print_r($row);
      //var_dump($row);
  //  If the job skill already exists, retrieve it using the Skill ID number.
      $skill_id = false;
      if($cnt > 0) {
          $sql = 'SELECT skill_id FROM Skill WHERE name = :nme';
          $stmt = $pdo->prepare($sql);
          $stmt->execute(array(':nme' => $skill) );
          $row = $stmt->fetch(PDO::FETCH_ASSOC);
          $skill_id = $row['skill_id'] + 0;
          // $_SESSION['message'] = $_SESSION['message']
          //                          .$_POST[$field_name].'at Skill-'.$i
          //                          .' already exists. ';
      }
  //  Look for duplicate skills
      $sqldup = 'SELECT COUNT(*) FROM SkillSet WHERE profile_id = :pid AND skill_id = :sid';
      $stmtdup = $pdo->prepare($sqldup);
      $stmtdup->execute(array(':pid' => $profile_id, ':sid' => $skill_id) );
      $rowcountdup = $stmtdup->fetch(PDO::FETCH_ASSOC);
      $cntduplicate = (int) array_values($rowcountdup)[0];
      if ($cntduplicate > 0){
        $_SESSION['message'] = $_SESSION['message'].' Duplicate skill found'
           .' for "'.$skill.'". ';
           //' ('.$cntduplicate.'). ';
        store_error_messages();
        continue;
      }
  //  if skill does not exist in Skill table, insert it
      if($cnt === 0) {
          // $_SESSION['message'] = $_SESSION['message']
          //    .' For Skill-'.$i.', the zero count condition is true '
          //    .' and the skill is being added to the skill table. ';
          $sql = 'INSERT INTO Skill (`name`) VALUES (TRIM(:nme))';
          $stmt = $pdo->prepare($sql);
          $stmt->execute(array(':nme' => trim($skill)) );
          // Retrieve the newly assigned skill_id
          $skill_id = $pdo->lastInsertId() + 0;
          // $_SESSION['message'] = $_SESSION['message']
          //                        .' Skill-'.$i.' was added to the Skill Table'
          //                        .': '.$_POST[$skillNum].'; ';
      }
      if (is_numeric($skill_id) && $skill_id > 0 ) {
          // $_SESSION['message'] = $_SESSION['message']
          //  .' For Skill-'.$i.', '
          //  .$skill.' is being added to the SkillSet Table'
          //  .' for '.$skill_id;
          $sql = 'INSERT INTO SkillSet (profile_id, skill_id, `rank`)
                    VALUES ( :pid, :sid, :rnk)';
          $stmt = $pdo->prepare($sql);
          $stmt->execute(array(
                  ':pid' => $profile_id, ':sid' => $skill_id,
                  ':rnk' => $rank ) );
          $rank++;
      }
   }
   return true;
}
// InsertHobyList is always an INSERT MYSQL COMMAND
// because the old set is always erased first
function insertHobbyList($profile_id,$pdo) {
    $rank = 1;
    for($i=1; $i<=12; $i++) {
      $field_name = 'hobby_name'.$i;
       // $_SESSION['message'] = $_SESSION['message']
       //                        .' The entry box for '
       //                        .' Hobby-'.$i.' is now evaluated. ';
      store_error_messages();
      if ( ! isset($_POST[$field_name]) ){
        //  $_SESSION['message'] = $_SESSION['message']
        //                         .' The entry box for '
        //                         .' Hobby-'.$i.' was empty. ';
        // store_error_messages();
        continue;
      }
      $hobby = trim($_POST[$field_name]);
      if ( ! strlen($hobby) > 0){
         $_SESSION['message'] = $_SESSION['message']
                                .' The entry box for '
                                .' Hobby-'.$i.' was not completed. ';
        store_error_messages();
        continue;
      }
      $text_insert = 'Hobby-'.$i;
      $valid = get_text_input_validation($field_name,$text_insert,$pdo);
      if ( ! $valid) {
          $_SESSION['message'] = $_SESSION['message']
                               .' Hobby-'.$i.' was rejected for '
                               .': '.$_POST[$field_name].'; ';
          store_error_messages();
          continue;
      }
  //  lookup the hobby in the Hobby Table
      $sql = 'SELECT COUNT(*) FROM Hobby WHERE name = :nme';
      $stmt = $pdo->prepare($sql);
      $stmt->execute(array(':nme' => $hobby) );
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      $cnt = (int) array_values($row)[0];
       // $_SESSION['message'] = $_SESSION['message']
       //    .' For Hobby-'.$i.', the count in the database is '
       //     .': '.$cnt.'; ';
      //print_r($row);
      //var_dump($row);
  //  If the hobby already exists, retrieve it using the Hobby ID number.
      $hobby_id = false;
      if($cnt > 0) {
          $sql = 'SELECT hobby_id FROM Hobby WHERE name = :nme';
          $stmt = $pdo->prepare($sql);
          $stmt->execute(array(':nme' => $hobby) );
          $row = $stmt->fetch(PDO::FETCH_ASSOC);
          $hobby_id = $row['hobby_id'] + 0;
          // $_SESSION['message'] = $_SESSION['message']
          //                           .$_POST[$field_name].'at Hobby-'.$i
          //                           .' already exists. ';
      }
  //  Look for duplicate hobbies
      $sqldup = 'SELECT COUNT(*) FROM HobbyList WHERE profile_id = :pid AND hobby_id = :sid';
      $stmtdup = $pdo->prepare($sqldup);
      $stmtdup->execute(array(':pid' => $profile_id, ':sid' => $hobby_id) );
      $rowcountdup = $stmtdup->fetch(PDO::FETCH_ASSOC);
      $cntduplicate = (int) array_values($rowcountdup)[0];
      if ($cntduplicate > 0){
        // $_SESSION['message'] = $_SESSION['message'].' Duplicate hobby found'
        //    .' for "'.$hobby.'". ';
        //store_error_messages();
        continue;
      }
  //  if hobby does not exist in Hobby table, insert it
      if($cnt === 0 && strlen($hobby) > 0) {
          $len = strlen($hobby);
          if ($len > 250){
              $_SESSION['message'] = $_SESSION['message'].'Hobby or interest '
                  .' of length '.$len.' exceeds maximum length of 250 '
                  .' characters for '.$hobby.'". ';
              store_error_messages();
              continue;
          }
          // $_SESSION['message'] = $_SESSION['message']
          //    .' For Hobby-'.$i.', the zero count condition is true '
          //    .' and the hobby is being added to the Hobby table. ';
          $sql = 'INSERT INTO Hobby (`name`) VALUES (:hob)';
          $stmt = $pdo->prepare($sql);
          $stmt->execute(array(':hob' => $hobby) );
          // Retrieve the newly assigned hobby_id
          $hobby_id = $pdo->lastInsertId() + 0;
          // $_SESSION['message'] = $_SESSION['message']
          //                         .' Hobby-'.$i.' was added to the Hobby Table'
          //                         .': '.' with ID '.$hobby_id;
      }
      if (is_numeric($hobby_id) && $hobby_id > 0 ) {
          // $_SESSION['message'] = $_SESSION['message']
          //  .' For Hobby-'.$i.', '
          //  .$hobby.' is being added to the HobbyList Table'
          //  .' for '.$hobby_id;
          $sql = 'INSERT INTO HobbyList (profile_id, hobby_id, `rank`)
                    VALUES ( :pid, :sid, :rnk)';
          $stmt = $pdo->prepare($sql);
          $stmt->execute(array(
                  ':pid' => $profile_id, ':sid' => $hobby_id,
                  ':rnk' => $rank ) );
          $rank++;
      }
   }
   return true;
}
// InsertEducation is always an INSERT MYSQL COMMAND
// because the old set is always erased first
function insertEducations($profile_id,$pdo) {
  $rank = 0;
  //print_r('Inserting education');
  for( $i=1 ; $i <= 9 ; $i++) {
    $year_field = 'edu_year'.$i;
    $school_field_name = 'edu_school'.$i;
    $award_field_name = 'edu_award'.$i;
    //print_r('The posted award is '.$_POST[$award_field_name]);
    if ( ! (
             isset($_POST[$school_field_name]) &&
             isset($_POST[$award_field_name])
           )
       )
    {
        //print_r('Nothing posted '.$i);
        continue;
    }
    $school = trim($_POST[$school_field_name]);
    if ( ! strlen($school) > 0){
      $_SESSION['message'] = $_SESSION['message']
        .' The school name entry box for '
        .' Education-'.$i.' was not completed. ';
      store_error_messages();
      continue;
    }
    $len = strlen($school);
    if ($len > 125){
            $_SESSION['message'] = $_SESSION['message'].'School '
                .' of length '.$len.' exceeds maximum length of 125 '
                .' characters for '.$school.'". ';
            store_error_messages();
            continue;
    }
    $text_insert = 'School-'.$i;
    $valid = get_text_input_validation($school_field_name,$text_insert,$pdo);
    if ( ! $valid) {
        $_SESSION['message'] = $_SESSION['message']
            .' The school name was rejected for Education-'.$i
            .': '.$_POST[$school_field_name].'; ';
        store_error_messages();
        continue;
    }
//  Educational Degree
    $award = trim($_POST[$award_field_name]);
    if ( ! strlen($award) > 0){
      $_SESSION['message'] = $_SESSION['message']
        .' The educational degree award for '
        .' Education-'.$i.' was not completed. ';
      store_error_messages();
      continue;
    }
    $text_insert = 'award for Education-'.$i;
    $valid_award = get_text_input_validation($award_field_name ,$text_insert,$pdo);
    if( ! ($valid_award===true)){
          $award = get_censored_input($award_field_name ,$text_insert,$pdo)[1];
    }
    if ( ! (strlen($award) > 0)) {
        $_SESSION['message'] = $_SESSION['message']
          .' The award for '
          .' Education-'.$i.' was removed entirely. ';
          store_error_messages();
          return;
    }
    // Check if the school already exists in the Institution Table
    $institution_id = false;
    $sqlinst = 'SELECT COUNT(*) FROM Institution WHERE name = :nme';
    $stmtinst = $pdo->prepare($sqlinst);
    $stmtinst->execute(array(':nme' => $school) );
    $rowinst = $stmtinst->fetch(PDO::FETCH_ASSOC);
    $cntinst = (int) array_values($rowinst)[0];
       // If the institution already exists, fetch the name of the school
    //$_SESSION['message'] = $_SESSION['message'].' The institution count is '.$cntinst;
    if($cntinst > 0){
            $sqlinst_if = 'SELECT institution_id FROM Institution WHERE name = :nme';
            $stmtinst_if = $pdo->prepare($sqlinst_if);
            $stmtinst_if->execute(array(':nme' => $school) );
            $rowinst_if = $stmtinst_if->fetch(PDO::FETCH_ASSOC);
            //$_SESSION['message'] = $_SESSION['message'].' The existing institution ID is '.$rowinst_if['institution_id'];
            if($rowinst_if !== false) {
                $institution_id = $rowinst_if['institution_id'] + 0;
            }
    }
    //if school was not found in the Institution Table, insert it.
    if($cntinst === 0) {
              $sqlinsert_sch = 'INSERT INTO Institution (`name`) VALUES (TRIM(:nme))';
              $stmtinsert_sch = $pdo->prepare($sqlinsert_sch);
              $stmtinsert_sch->execute(array(':nme' => $school) );
              $institution_id = $pdo->lastInsertId() + 0;
              // $_SESSION['message'] = $_SESSION['message'].$school
              //      .' is being added to the Institution Table: '
              //      .$school.'. ';
              //store_error_messages();
    }
        // lookup the educational award
        // Check if the degree award already exists in the Award Table
    $awardid = false;
    $sqlaward = 'SELECT COUNT(*) FROM Award WHERE name = :nme';
    $stmtaward = $pdo->prepare($sqlaward);
    $stmtaward->execute(array(':nme' => $award) );
    $rowaward = $stmtaward->fetch(PDO::FETCH_ASSOC);
    $cntaward = (int) array_values($rowaward)[0];
    //  If the Educational award already exists, get it from the Award Table.
    //$_SESSION['message'] = $_SESSION['message'].' The award count is '.$cntaward;
    if($cntaward > 0){
            $sql = 'SELECT award_id FROM Award WHERE name = :nme';
            $stmtaward_if = $pdo->prepare($sql);
            $stmtaward_if->execute(array(':nme' => $award) );
            $rowaward_if = $stmtaward_if->fetch(PDO::FETCH_ASSOC);
                //$_SESSION['message'] = $_SESSION['message'].' The existing Award ID is '.$rowaward_if['award_id'];
            if($rowaward_if !== false) {
                $awardid = $rowaward_if['award_id'] + 0;
            }
    }
        //if award not found, insert it
    if($cntaward === 0) {
        $len = strlen($award);
        if ($len > 125){
              $_SESSION['message'] = $_SESSION['message'].'Award '
                  .' of length '.$len.' exceeds maximum length of 125 '
                  .' characters for '.$award.'". ';
              store_error_messages();
              continue;
        }
        $sqlinsert_awd = 'INSERT INTO Award (`name`) VALUES (TRIM(:nme))';
        $stmtinsert_awd = $pdo->prepare($sqlinsert_awd);
        $stmtinsert_awd->execute(array(':nme' => $award) );
        $awardid = $pdo->lastInsertId() + 0;
            //$_SESSION['message'] = $_SESSION['message'].$award
            //         .' is being added to the Award Table: '
            //         .$award.'. ';
            //store_error_messages();
    }
   // look for duplicate education
   //$_SESSION['message'] = $_SESSION['message'].' Looking for duplicate education. '.$institution_id.'-'.$awardid;
   $sqldup = 'SELECT COUNT(*) FROM Education WHERE profile_id = :pid AND institution_id = :iid AND award_id = :aid';
   $stmtdup = $pdo->prepare($sqldup);
   $stmtdup->execute(array(':pid' => $profile_id, ':iid' => $institution_id, ':aid' => $awardid) );
   $rowdup = $stmtdup->fetch(PDO::FETCH_ASSOC);
   $cntdup = (int) array_values($rowdup)[0];
   if ($cntdup > 0){
     $_SESSION['message'] = $_SESSION['message'].' Duplicate education found'
        .' for '.$school.' and '.$award.'. ';
     //    '('.$cntdup.'). ';
     store_error_messages();
     continue;
   }
   //$_SESSION['message'] = $_SESSION['message'].' Inserting education '
      //.' for '.$school.' and '.$award.'('.$cntdup.'). ';
   //store_error_messages();
   //continue;
    if(
        is_numeric($institution_id) && $institution_id > 0 &&
        is_numeric($awardid)       &&       $awardid > 0
      )
      {
          $sql = 'INSERT INTO Education
                 (profile_id, institution_id, award_id, `rank`)
                    VALUES ( :pid, :iid, :awd,  :rnk)';
          $stmt = $pdo->prepare($sql);
          $stmt->execute(array(
                  ':pid' => $profile_id, ':iid' => $institution_id,
                  ':awd' => $awardid,  ':rnk' => $rank ));
      // The year not required
         $year_int = " ";
         if ( ! isset($_POST[$year_field]) ) {
              $_SESSION['message'] = $_SESSION['message']
              .' The year field was not set: '.$_POST[$year_field].'; ';
         } else {
              $year_string = trim($_POST['edu_year'.$i]);
              $valid_yr = validate_year($year_string);
              if ( ! $valid_yr ) {
                      // $_SESSION['message'] = $_SESSION['message']
                      //     .' Please check the year for Education-'.$i
                      //     .' for '.$school.'.'
                      //     .' When a year is not provided for an education,'
                      //     .' it is removed for all education in the resume'
                      //     .' view. The year for any other education is still'
                      //     .' saved in the database if it was submitted. ';
              } else {
                 $year_int = get_year($year_string,' ');
              }
        }
        if($valid_yr){
              $sql = 'UPDATE Education SET year = :yr WHERE
                profile_id = :pid AND institution_id = :iid AND
                award_id = :aid ';
              $stmt = $pdo->prepare($sql);
              $stmt->execute(array(
                ':pid' => $profile_id,':iid' => $institution_id,
                ':aid' => $awardid, ':yr'  => $year_int ) );
        }
        $rank++;
      }
  } // end 'for' loop
  if($rank == 0){
     return false;
  }
  //$_SESSION['success'] = "There are ".$rank." education entries.";
  store_error_messages();
  $_SESSION['countSchools'] = $rank;
  return true;
}
// InsertActivitySet is always an INSERT MYSQL COMMAND
// because the old set is always erased first
function insertActivityList($profile_id,$loop_id,$position_id,$pdo) {
    //$_SESSION['message'] = $_SESSION['message']
      //                      .' The task position-tag is Number '.$activity_position_tag.'.';
    store_error_messages();
    $rank = 0;
    for($i=1; $i<=100; $i++) {
       $tag = 'activity_position_tag'.$i;
       if ( isset($_POST[$tag])) {
          $positionid = $_POST[$tag];
       } else {
          continue;
       }
       //$_SESSION['message'] = $_SESSION['message']         //                       .' In activity loop: '
        //     .'The tag was set for '.$tag;
       //store_error_messages();
       $field_name = 'task'.$i;
       if (  isset($_POST[$field_name])) {
          $activity = trim($_POST[$field_name]);
       } else {
          continue;
       }

       //if ( !isset($_POST[$tag]) || (!isset($_POST[$field_name])) ){
        //$_SESSION['message'] = $_SESSION['message']         //                       .' In activity loop: '
          //    .'Tag and field were set for '.$field_name;
        //store_error_messages();

       //}
       // The position tag will only match for the particular position ID.
       if ( $_POST[$tag] != $loop_id ){
       // $_SESSION['message'] = $_SESSION['message']         //                       .' In activity loop: '
       //                        .'Positions do not match for '.
       //                        ' Task-'.$i.'. The activity tag of '.$_POST[$tag]
       //                        .' does not equal the passed tag value of '
       //                        .$activity_position_tag;
       //   store_error_messages();
          continue;
       }
       if ( isset($_POST[$tag])) {
          //$positionid = $_POST[$tag];
          $activity = trim($_POST[$field_name]);
         //$_SESSION['message'] = $_SESSION['message']
          //.$tag.' is '.$positionid.'.';
          //store_error_messages();
       }
       // $_SESSION['message'] = $_SESSION['message']         //                       .' In activity loop: '
       //                         .' Task-'.$i.' posted for '
       //                         . ' Task Position-Tag-'.$i;
       // store_error_messages();
       //$pos_id = $activity_rows[$i]['position_id'];
        if ( ! strlen($activity) > 0){
             $_SESSION['message'] = $_SESSION['message']
                                .' The entry box for '
                                .' Task-'.$i.' was not completed. ';
                                store_error_messages();
                                continue;
      }
      $text_insert = 'Task-'.$i;
      //$_SESSION['message'] = $_SESSION['message']
        //                 .' The activity for Field '.$field_name
          //               .' is '.$activity.' Ready for text validation: ';
      store_error_messages();
      $valid = get_text_input_validation($field_name,$text_insert,$pdo);
      // $_SESSION['message'] = $_SESSION['message']
      //                         .' Completed text validation: ';
      // store_error_messages();
      if ( ! $valid) {
          $_SESSION['message'] = $_SESSION['message']
                               .' Task-'.$i.' was rejected for '
                               .': '.$_POST[$field_name].'; ';
          store_error_messages();
          continue;
      }
      //print_r($row);
      //var_dump($row);
      //$_SESSION['message'] = $_SESSION['message']
        //                      .' Ready to add to database for '
          //                    .' Task-'.$i.': '.$activity;
      //store_error_messages();
      $len = strlen($activity);
      if ($len > 500){
              $_SESSION['message'] = $_SESSION['message'].'Activity '
                  .' of length '.$len.' exceeds maximum length of 500 '
                  .' characters for '.$activity.'". ';
              store_error_messages();
              continue;
      }
      $sql = 'INSERT INTO Activity (profile_id,position_id,description, activity_rank)
                           VALUES ( :pid, :pos ,TRIM(:dscrp), :rnk)';
      $stmt = $pdo->prepare($sql);
      $stmt->execute(array(
                   ':pid' => $profile_id, ':pos' => $position_id,
                   ':dscrp' => $activity,
                   ':rnk' => $rank ) );
      // $rank++;
      // $_SESSION['message'] = $_SESSION['message']
      //                         .' Next rank is '.$rank;
      // store_error_messages();
    }
    return true;
}
function insertPositions($profile_id,$pdo) {
    $rank = 0;
    $count = 0;

    for($i=1; $i<=9; $i++) {
      $org_field_name = 'org'.$i;
      $job_title_field_name = 'title'.$i;
      $job_description_field_name = 'job_summary'.$i;
      $yr1_field_name = 'wrk_start_yr'.$i;
      $yr2_field_name = 'wrk_final_yr'.$i;
      if ( ! (isset($_POST[$org_field_name]))) {
          continue;
      }
      $org = trim($_POST[$org_field_name]);
      if ( ! strlen($org) > 0){
        $_SESSION['message'] = $_SESSION['message']
                  .' The organization name for '
                  .' Work History-'.$i.' was empty. ';
        store_error_messages();
        continue;
      }
      if ( ! (isset($_POST[$job_title_field_name]))) {
          continue;
      }
      $title = trim($_POST[$job_title_field_name]);
      if ( ! strlen($title) > 0){
        $_SESSION['message'] = $_SESSION['message']
                  .' The job title for '
                  .' Work History-'.$i.' was empty. ';
        store_error_messages();
        continue;
      }
      if
      ( !(
              isset($_POST[$yr1_field_name])          &&
              ( strlen($_POST[$yr1_field_name]) > 0 )
          )
      )
      {
        // &&
        //isset($_POST[$yr2_field_name])          &&
        //( strlen($_POST[$yr2_field_name]) > 0 )
         $_SESSION['message'] = $_SESSION['message']
                .' The starting year entry box for '
                .' Work History-'.$i.' was empty. ';
        store_error_messages();
        continue;
      }
      $year_start_string = trim($_POST[$yr1_field_name]);
      if ( ! (strlen($year_start_string) > 0)){
          $_SESSION['message'] = $_SESSION['message']
                  .' The starting year entry box for '
                  .' Work History-'.$i.' was not completed. ';
          store_error_messages();
          continue;
      }
      $year_final_string = trim($_POST[$yr2_field_name]);
      //if ( ! (strlen($year_final_string) > 0)){
        // $_SESSION['message'] = $_SESSION['message']
        //           .' The final year entry box for '
        //           .' Work History-'.$i.' was not completed. ';
        // store_error_messages();
        // continue;
      //}
      $valid_yr1 = validate_year($year_start_string);
      $valid_yr2 = validate_year($year_final_string);
      if ( ! $valid_yr1 ) {
            $_SESSION['message'] = $_SESSION['message']
               .' Please enter a 4-digit year for Work History-'.$i
               .': '.$valid_yr1;
             // $_SESSION['message'] = $_SESSION['message']
             //     .' Year data'
             //     .$year_start_string.'-'.$year_final_string;
            store_error_messages();
            continue;
        }
        $year_start = get_year($year_start_string,'year start issues');
        if ( ! $valid_yr2 ) {
             $year_final = 9999;
        } else {
             $year_final = get_year($year_final_string,'year final issues');
        }
        // $_SESSION['message'] = $_SESSION['message']
        //   .' Year validation: '
        //   .$valid_yr1.'-'.$valid_yr2;
        // $_SESSION['message'] = $_SESSION['message']
        //     .' Year string data: '
        //     .$year_start_string.'-'.$year_final_string;
        // $_SESSION['message'] = $_SESSION['message']
        //    .' We are processing the position years for Work History-'.$i
        //    .' : '.$org_field_name.'; '.$year_start.'-'.$year_final;
        //store_error_messages();
        $text_insert = 'organization name for Work History-'.$i;
        $valid = get_text_input_validation($org_field_name,$text_insert,$pdo);
        if ( ! $valid) {
            $_SESSION['message'] = $_SESSION['message']
              .' The organization name was rejected for Work History-'.$i
              .'. ';
            store_error_messages();
            continue;
        }
        $text_insert = 'Job title for Work History-'.$i;
        $valid = get_text_input_validation($job_title_field_name,$text_insert,$pdo);
        if ( ! $valid) {
            $_SESSION['message'] = $_SESSION['message']
              .' The job title was rejected for Work History-'.$i
              .'. ';
            store_error_messages();
            continue;
        }
        $rank++;
        $stmt = $pdo->prepare('INSERT INTO Position (
                profile_id, job_rank, yearStart, yearLast, organization,
                     title)
                VALUES ( :pid, :rnk, :yrStart, :yrLast, :org, :title) ');
        $stmt->execute(array(
               ':pid' => $profile_id,  ':rnk' => $rank,
               ':yrStart' => $year_start,
               ':yrLast' => $year_final,
               ':org' => $org,
               ':title' => $title
             ));
        $position_id = $pdo->lastInsertId() + 0;
        // $_SESSION['message'] = $_SESSION['message']
        //             .' The new position number '
        //             .' Work History-'.$i.' is '.$position_id;
        // store_error_messages();
        $rank++;
        //  job description
        if ( ! (isset($_POST[$job_description_field_name]))) {
          // $_SESSION['message'] = $_SESSION['message']
          //           .' The job summary for '
          //           .' Work History-'.$i.' was not posted. ';
          // store_error_messages();
          //continue;
        }
        $summary = trim($_POST[$job_description_field_name]);
        if ( ! strlen($summary) > 0){
          // $_SESSION['message'] = $_SESSION['message']
          //           .' The job summary for '
          //           .' Work History-'.$i.' was empty. ';
          // store_error_messages();
          //continue;
        }
        $text_insert = 'job description for Work History-'.$i;
        $valid = get_text_input_validation($job_description_field_name ,$text_insert,$pdo);
        if( ! ($valid===true)){
            $summary = trim(get_censored_input($job_description_field_name ,$text_insert,$pdo)[1]);
            //$_SESSION['message'] = $_SESSION['message']
               //.' After any deletions, it becomes "'.$desc.'." ';
        }
        if ( ! (strlen($summary) > 0)) {
            // $_SESSION['message'] = $_SESSION['message']
            //      .' The job description for '
            //        .' Work History-'.$i.' was removed entirely. ';
            // store_error_messages();
            //continue;
        }
        $sql = 'UPDATE Position SET summary = :jobsumm
                      WHERE position_id = :posid';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(':jobsumm' => $summary,
                               ':posid' => $position_id ));
        // $_SESSION['message'] = $_SESSION['message']
        //           .' Ready to insert activities for '
        //           .' Work History-'.$i.'.';
        //store_error_messages();
        insertActivityList($profile_id,$i,$position_id,$pdo);
        // $_SESSION['message'] = $_SESSION['message']
        //     .' Activities were inserted.';
        // store_error_messages();
      } // end of 'for' loop
      $_SESSION['count_position'] = $rank;
      if($rank < 1){
        return false;
      }
      store_error_messages();
      return true;
}
function loadContactList($profile_id,$pdo) {
  $sql = 'SELECT Contact.info FROM ContactList JOIN Contact
      ON Contact.contact_id = ContactList.contact_id
      WHERE profile_id = :prof';
  $stmt = $pdo->prepare($sql);
  $stmt->execute(array(':prof' => $profile_id) );
  $contacts = $stmt->fetchALL(PDO::FETCH_ASSOC);
  return $contacts;
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
function loadHobbies($profile_id,$pdo) {
  $sql = 'SELECT Hobby.name FROM HobbyList JOIN Hobby
      ON Hobby.hobby_id = HobbyList.hobby_id
      WHERE profile_id = :prof';
  $stmt = $pdo->prepare($sql);
  $stmt->execute(array(':prof' => $profile_id) );
  $hobbies = $stmt->fetchALL(PDO::FETCH_ASSOC);
  return $hobbies;
}
function loadActivity($profile_id,$position_id,$pdo) {
  $sql = 'SELECT position_id, description, activity_rank FROM Activity
          WHERE profile_id = :prof AND position_id = :posid';
  $stmt = $pdo->prepare($sql);
  $stmt->execute(array(':prof' => $profile_id, ':posid' => $position_id) );
  $tasks = $stmt->fetchALL(PDO::FETCH_ASSOC);
  return $tasks;
}
function loadPos($profile_id,$pdo) {
 $stmt = $pdo->prepare('SELECT * FROM Position
     WHERE profile_id = :prof ORDER BY job_rank');
 $stmt->execute(array(':prof' => $profile_id) );
 $positions = $stmt->fetchALL(PDO::FETCH_ASSOC);
 return $positions;
}
function loadEdu($profile_id,$pdo) {
  $sql = 'SELECT Education.year, Institution.name As institution , Award.name As degree FROM Education JOIN Institution JOIN Award
      ON Education.institution_id = Institution.institution_id AND Education.award_id = Award.award_id
      WHERE Education.profile_id = :pid';
  $stmt = $pdo->prepare($sql);
  $stmt->execute(array(':pid' => $profile_id) );
  $education = $stmt->fetchALL(PDO::FETCH_ASSOC);

  $sqlaward = 'SELECT Education.year, Award.name FROM Education JOIN Award
      ON Education.award_id = Award.award_id
      WHERE Education.profile_id = :pid';
  $stmtaward = $pdo->prepare($sql);
  $stmt->execute(array(':pid' => $profile_id) );
  $education = $stmt->fetchALL(PDO::FETCH_ASSOC);
  return $education;
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
   }
   store_error_messages();
   return $field;
}
function getPhraseArray($phrase) {
    //$newPhrase = '';
    $trimmed = trim($phrase);
    if ( ! (strlen($trimmed) > 0)  )  {
        return false;
    }
    $replaced ='';
    $punct = array(',','.','!','?','@');
    $replace = array(' ');
    foreach ($punct as $punc) {
            $replaced = str_replace($punc,' ',$trimmed);
    }
    $exploded = explode(' ',$replaced);
    return  $exploded;
}
function ofnsvCheck($phrase, $pdo) {
    $offense = false;
    $no_punct = str_replace(array('.','. ',   ',',', ',  '?','? ',
                  '!','! ',  ';','; ',  ':',': ', '[','[ '),
                            ' ' , $phrase);
    $exploded = getPhraseArray($no_punct);
    foreach ($exploded as $word) {
        // $_SESSION['message'] = $_SESSION['message']
        //                      .' Checking dictionary: '.$phrase;

        $len = strlen($word);
        if( $len > 2 ) {
            $check = checkDictionary($word,$pdo);
            // $_SESSION['message'] = $_SESSION['message']
            //                      .' Dictionary Result: '.$check;
            if($check == true) {
                // Found in dictionry; do nothing;
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
        } else {
            continue;
        }
    }
    // $_SESSION['message'] = $_SESSION['message']
    //                      .' Returning Result: '.$offensive;
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
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM Offensive
                 WHERE (? = word) AND (LENGTH(word) = 3)
                 OR ((? LIKE CONCAT("%", word, "%")) AND LENGTH(word) > 3)');
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
