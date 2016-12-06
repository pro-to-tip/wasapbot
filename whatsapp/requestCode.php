<?php

require_once('src/Registration.php');

$debug = false;
$username = "79670419666";   

if(empty($username)){ 
echo "Mobile Number can't be Empty"; 
exit(0);
}
if (!preg_match('!^\d+$!', $username))
{
  echo "Wrong number. Do NOT use '+' or '00' before your number\n";
  exit(0);
}
$w = new Registration($username, $debug);
  try {
    $w->codeRequest("sms");
	echo "Verification Code Sent via SMS";
  } catch(Exception $e) {
    echo $e->getMessage();
    exit(0);
  }

