<?php
require_once('/usr/local/bin/vendor/autoload.php');
//require_once('/usr/share/php/vendor/autoload.php');
//use PHPMailer\PHPMailer\PHPMailer.php;
//use PHPMailer\PHPMailer\Exception.php;
//Import PHPMailer classes into the global namespace
// require '/usr/share/php/PHPMailer/src/Exception.php';
// require '/usr/share/php/PHPMailer/src/PHPMailer.php';
// require '/usr/share/php/PHPMailer/src/SMTP.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
//require_once 'PHPMailer/src/PHPMailer.php';
//require_once 'PHPMailer/src/SMTP.php';
//require_once("includes/lib/src/phpmailer/class.phpmailer.php");
$mail = new PHPMailer(true);
$mail->isSMTP();
$mail->Mailer = "smtp";
$mail->SMTPAuth = true;
//$mail->SMTPSecure = 'ssl';
$mail->SMTPSecure = 'tls';
$mail->Host = 'tls://smtp.gmail.com';
$mail->SMTPDebug = SMTP::DEBUG_SERVER;
$mail->SMTPDebug = 2;
//$mail->Host = 'localhost';
//$mail->Port = '465';
$mail->Port = 587;
//$mail->Port = '467';
//$mail->isHTML();
//$mail->Username = 'merchat77@gmail.com';
$mail->Username = 'merchatDataTools@gmail.com';
//$mail->Password = 'oP$1950Los';
$mail->Password = 'jpS%sis$1973R7';
//$mail->FromName = "Support Team";
$mail->SetFrom('merchatDataTools@gmail.com','Support Team');
//$mail->From = "merchatDataTools@gmail.com";
// $mail->Subject = $_SESSION['emailSubject'];
$mail->Subject = 'Account Change';
$mail->Body = $_SESSION['emailMessage'];
//$address = "marcosmoothy@gmail.com";
$address =  $_SESSION['email'];
echo 'to address is '.$address;
try{
    $mail->AddAddress($address, "Marcel Merchat");
}
catch (phpmailerException $e) {
    echo $e->errorMessage(); //Pretty error messages from PHPMailer
} catch (Exception $e) {
  //echo $e->getMessage(); //Boring error messages from anything else!
    echo 'Message: ' .$e->getMessage();
    $_SESSION['error'] = "mail->AddAddress Error: " . $e->getMessage().$mail->ErrorInfo;
}

//$_SESSION['success'] = $_SESSION['success'].", Ready to try ";
try{
    $mail->send();
    echo "Message sent!";
    //$_SESSION['success'] = $_SESSION['success'] .'Mail Sent. New password assigned. You may now login.';
    $_SESSION['success'] = $_SESSION['success'] .'New password assigned. You may now login.';
    $_SESSION['emailsuccess'] = true;
    unset($_SESSION['emailSubject']);
    //unset($_SESSION['emailMessage']);
    error_log('new password application success for User-'.$_POST['user_id']);
}
catch (phpmailerException $e) {
  //echo $e->errorMessage(); //Pretty error messages from PHPMailer
} catch (Exception $e) {
  //echo $e->getMessage(); //Boring error messages from anything else!
  //echo 'Message: ' .$e->getMessage();
  $_SESSION['error'] = "Mailer Error: " . $e->getMessage().$mail->ErrorInfo;
    //echo 'ERROR: '. $e->errorMessage();
  //error_log('New password application failure for User-'.$_POST['user_id']);
    //See the "errors" folder for details...
}
//} catch {
//catch (\Exception $e) { //The leading slash means the Global PHP Exception class will be caught
    //echo 'ERROR: '. $e->errorMessage(); //Pretty error messages from PHPMailer
    //echo $e->getMessage(); //Boring error messages from anything else!
//}
//    $_SESSION['error'] = 'Mailer Error: '.$_SESSION['error'] ;
//}
