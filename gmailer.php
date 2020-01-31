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
//$mail->Port = '465';
//$mail->Port = 587;
//$mail->Port = '467';
$mail->isHTML(true);
$mail->Username = 'merchatDataTools@gmail.com';
$mail->Password = '';
$mail->SetFrom('merchatDataTools@gmail.com','Support Team');
$mail->Subject = $_SESSION['emailSubject'];
//'Account Change';
$mail->Body = $_SESSION['emailMessage'];
$address =  $_SESSION['email'];
$mail->AddAddress($address, $_SESSION['userName']);
try{
    if(!$mail->send()) {
        $_SESSION['success'] = false;
        $_SESSION['error'] = 'Mailer Error: ' . $mail->ErrorInfo;
        $_SESSION['success'] = "It was not successful";
        $_SESSION['error'] = "It didn't go thru ".'Mailer Error: ' . $mail->ErrorInfo;
    } else {
        $_SESSION['success'] = true;
    }
} catch (phpmailerException $e) {
  $_SESSION['success'] = false;
  $_SESSION['error'] = "PHPMailer Error: ".$e->errorMessage(); //Pretty error messages from PHPMailer
} catch (Exception $e) {
  $_SESSION['success'] = false;
  //echo $e->getMessage(); //Boring error messages from anything else!
  $_SESSION['error'] = "General mail error or internet not available: ". $e->getMessage().$mail->ErrorInfo;
}
