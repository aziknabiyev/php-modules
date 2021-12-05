<?php

require('site/modules/MailSend/swift_required.php');

error_reporting(~0);
ini_set('display_errors', 1);

$emails=['test@gmail.com']; 
$adminEmail = array("test@gmail.com" => 'Basu');
$transport = Swift_SmtpTransport::newInstance('smtp.gmail.com', 587, 'tls');

$transport->setUsername("");
$transport->setPassword('');


$mailer = Swift_Mailer::newInstance($transport);
$mailLogger = new Swift_Plugins_Loggers_ArrayLogger();
$mailer->registerPlugin(new \Swift_Plugins_LoggerPlugin($mailLogger));

$message = Swift_Message::newInstance('Basu');
$message->setFrom($adminEmail);
$message->setTo($emails);
$message->setBody('Test');
$message->setContentType("text/html");
//$mailer->send($message);
if ($mailer->send($message)) {  
    echo '[SWIFTMAILER] sent email to ' ;
} else {
    echo '[SWIFTMAILER] not sending email: ' . $mailLogger->dump();
} 

?>
