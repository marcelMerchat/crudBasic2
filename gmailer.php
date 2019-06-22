<?php
require_once('/usr/local/bin/vendor/autoload.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
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
//////$mail->Port = '465';
//$mail->Port = 587;
///////$mail->Port = '467';
$mail->isHTML(true);
//$mail->Username = 'merchat77@gmail.com';
$mail->Username = 'merchatDataTools@gmail.com';
//$mail->Password = 'oP$1950Los';
$mail->Password = 'jpS%sis$1973R7';
//$mail->FromName = "Support Team";
$mail->SetFrom('merchatDataTools@gmail.com','Support Team');
// $mail->From = "merchatDataTools@gmail.com";
$mail->Subject = $_SESSION['emailSubject'];
//'Account Change';
$mail->Body = $_SESSION['emailMessage'];
//$address = "marcosmoothy@gmail.com";
$address =  $_SESSION['email'];
//echo 'to address is '.$address;
//try{
    $mail->AddAddress($address, $_SESSION['userName']);
//}
//catch (phpmailerException $e) {
  //  echo $e->errorMessage(); //Pretty error messages from PHPMailer
//} catch (Exception $e) {
  //  echo 'Message: ' .$e->getMessage();
    //$_SESSION['error'] = "mail->AddAddress Error: " . $e->getMessage().$mail->ErrorInfo;
//}
//$_SESSION['success'] = $_SESSION['success'].", Ready to try ";
try{
    //$mail->send();
    if(!$mail->send()) {
    //if(true) {
        //echo 'Message could not be sent.';
        $_SESSION['success'] = false;
        $_SESSION['error'] = 'Mailer Error: ' . $mail->ErrorInfo;
        $_SESSION['success'] = "It was not successful";
        $_SESSION['error'] = "It didn't go thru ".'Mailer Error: ' . $mail->ErrorInfo;
    } else {
    //echo 'Message has been sent';
    //$_SESSION['success'] = true;
    $_SESSION['success'] = "It was successful";
    //$_SESSION['success'] = 'New password assigned for '.$address.'. You may now login.';
    }
    //echo "Message sent to";


    //$_SESSION['emailsuccess'] = true;
// Only if internet is down
} catch (phpmailerException $e) {
  $_SESSION['success'] = false;
  $_SESSION['error'] = "PHPMailer Error: ".$e->errorMessage(); //Pretty error messages from PHPMailer
} catch (Exception $e) {
  $_SESSION['success'] = false;
  //echo $e->getMessage(); //Boring error messages from anything else!
  //echo 'Message: ' .$e->getMessage();
  $_SESSION['error'] = "General mail error or internet not available: ". $e->getMessage().$mail->ErrorInfo;
}
unset($_SESSION['email']);
