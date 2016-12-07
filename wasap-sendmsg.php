<?php

  echo "+\n+ [wasapbot]\n+\n";
  // error_reporting(E_ERROR | E_WARNING | E_PARSE);
  date_default_timezone_set('Asia/Jakarta');
  $debug = false;

  require_once 'whatsapp/src/whatsprot.class.php';

  $nickname = "nickname";                          // bot nickname
  $username = "79670419666";                     // bot number
  $password = "SwimxCjZQxFz6gVanO1PXvMKjKs=";     // password

  // подключение ------------------------------------------------------------------------------------------------------------
  echo "+ Login sebagai $nickname ($username)\n+\n";
  $w = new WhatsProt($username, $nickname, $debug);
  sleep(3);
  $w->connect();
  $w->loginWithPassword($password);
  $w->sendGetServerProperties();
  $w->sendClientConfig();
  $w->sendGetGroups();
  $w->sendPing();

  //отправьте сообщение на номер получателя--------------------------------------------------------------------------------------

  $target="62xxxxxxxxxxx";
  $pesan="Halo Dunia!";

  echo "+ Mengirim pesan\n";
  echo "+  target : $target\n";
  echo "+  pesan  : $pesan\n+\n";
  $w->sendMessageComposing($target);    // typing..
  sleep(3);
  $w->sendMessagePaused($target);       // selesai typing
  sleep(1);
  $w->sendMessage($target, $pesan);    // kirim pesan
  $w->pollMessage();
  sleep(1);
  $w->pollMessage();
  sleep(1);
  $w->pollMessage();
  echo "+ Selesai\n+\n";

 ?>
