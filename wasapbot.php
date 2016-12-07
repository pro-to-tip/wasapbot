<?php

  // --------------------------------------------------------------------------------------------------------------- //
  //                                                                                                                 //
  // --------------------------------------------------------------------------------------------------------------- //
  //                                                                                                                 //
  //                                                                                                                 //
  //                                                                                                                 //
  //                                                                                                                 //
  // --------------------------------------------------------------------------------------------------------------- //
  //                                                                                                                 //
  // Предупреждение! Код очень грязный, не профессиональный, не модульная, а не ООП, продукт новичка. Наверное, багги//
  // тоже как в аду. Вы не найдете здесь ничего особенного, просто поставив вещи работать вместе.                    //
  //                                                                                                                 //
  // --------------------------------------------------------------------------------------------------------------- //
  //  используем чат-АПИ                                                                                             //
  // --------------------------------------------------------------------------------------------------------------- //
  
  error_reporting(E_ERROR | E_WARNING | E_PARSE);
  date_default_timezone_set('Russia/Moscow');
  $debug = false;

  require_once 'whatsapp/src/whatsprot.class.php';

  // bot configuration 
  $nickname = "";                          // bot's nickname
  $username = "79096679572";                      // bot's number
  $password = "YQXoNxJ0KN8yvuTSu/o033vJ49Q=";    // bot's spassword
  // get the password using registerTool.php
  // --> whatsapp/examples/registerTool.php


  // --------------------------------------------------------------------------------------------------------------- //
  // --------------------------------------------------------------------------------------------------------------- //
  // Только править скрипты, если вы понимаете о том, как чат-API работает, сделайте резервную копию, прежде чем редактировать!//
  //                                                                                                                 //
  // --------------------------------------------------------------------------------------------------------------- //
  // --------------------------------------------------------------------------------------------------------------- //
  
  // restart script when error ----------------------------------------------------------------------------------------
  $ke = ++$argv[1]; $_ = $_SERVER['_']; $poll = 0;
  $restartMyself = function () {
      global $_, $argv, $ke;
      echo "\n[$ke][".date('H:i:s')."] - Something wrong, restarting bot..\n";
      sleep(10);
      pcntl_exec($_, $argv);
  };
  set_exception_handler($restartMyself); 
  register_shutdown_function($restartMyself);
  pcntl_signal(SIGTERM, $restartMyself); // kill
  pcntl_signal(SIGHUP,  $restartMyself); // kill -s HUP or kill -1
  // pcntl_signal(SIGINT,  $restartMyself); // Ctrl-C

  echo "[$ke][".date('H:i:s')."] ---------------------------------------------------------\n";
  echo "[$ke][".date('H:i:s')."] - Login as '$nickname' ($username)\n";
  check_connection();
  $w = new WhatsProt($username, $nickname, $debug);
  
  // bind events ------------------------------------------------------------------------------------------------------
  // 
  $w->eventManager()->bind("onConnect", "onConnect");
  $w->eventManager()->bind("onDisconnect", "onDisconnect");
  $w->eventManager()->bind("onClose", "onClose");
  $w->eventManager()->bind("onGetMessage", "onGetMessage");
  $w->eventManager()->bind("onGetGroupMessage", "onGetGroupMessage");

  // connect ----------------------------------------------------------------------------------------------------------
  sleep(3);
  $w->connect();
  $w->loginWithPassword($password);
  $w->sendGetServerProperties();
  $w->sendClientConfig();
  $w->sendGetGroups();
  $w->sendPing();

  // poll message loop ------------------------------------------------------------------------------------------------
  while (1) {

    if ($poll==5) {
      echo "\n[$ke][".date('H:i:s')."] ---------------------------------------------------------\n";
      echo "[$ke][".date('H:i:s')."] --- BOT IS READY!\n";
      echo "[$ke][".date('H:i:s')."] --- \n";
      echo "[$ke][".date('H:i:s')."] --- Now try to send a message to this bot,\n";
      echo "[$ke][".date('H:i:s')."] --- this bot should reply back any text you sent to it.\n";
      echo "[$ke][".date('H:i:s')."] --- \n";
      echo "[$ke][".date('H:i:s')."] --- Change the bot's behaviour by editing the\n";
      echo "[$ke][".date('H:i:s')."] --- onGetMessage() and onGetGroupMessage() function\n";
      echo "[$ke][".date('H:i:s')."] --- on line 126 and line 182.\n";
      echo "[$ke][".date('H:i:s')."] --- \n";
      echo "[$ke][".date('H:i:s')."] --- Good luck!\n";
      echo "[$ke][".date('H:i:s')."] ---------------------------------------------------------\n";
    }

    $w->pollMessage(true); // markAsRead

    // check connection state every 100 loop
    if ($poll % 100 == 0 && $poll != 0) {
      check_connection();
    }

    $poll++; // poll control
  }


  // ------------------------------------------------------------------------------------------------------------------
  // FUNCTIONS 
  // ------------------------------------------------------------------------------------------------------------------

  // on private message
  function onGetMessage($mynumber, $from, $id, $type, $time, $name, $body)
  {
    global $ke, $w, $poll;
    $bodi = str_replace( array("\n", "\r\n", "\r") , " ", $body);       // trim new line, for displaying on console
    $from = str_replace(array("@s.whatsapp.net","@g.us"), "", $from);   // sender's phone number

    echo "\n[$ke][".date('H:i:s')."] [$from]\n";
    echo "--- $name > $bodi\n";

    // display all private messsage parameter
    echo "mynumber  : $mynumber\n";
    echo "from      : $from\n";
    echo "id        : $id\n";
    echo "type      : $type\n";
    echo "time      : $time\n";
    echo "name      : $name\n";
    echo "body      : $body\n";


    // ignore messages on early poll,
    // this will give some time for older message to arrive (messages sent to bot when bot is offline)
    // those messages are ignored so the bot won't spam
    
    if ($poll > 5) {

      // from here, we can write the bot's logic.. for example:
      
      // (1) echo back any messages sent to it
      // --------------------------------------------------------------------------------------------

            // we'll try to mimic human behaviour, so whatsapp won't think that this is a bot
            
            // send 'is typing' signal..
            $w->sendMessageComposing($from);

            // always give some interval time (2, 3 or 5 seconds)
            // don't instantly reply (too unnatural, too bot-ish)
            sleep(3);

            // send back message
            $w->sendMessage($from, $body);

            // after sending anything, always call pollMessage()
            $w->pollMessage();


      // (2) or respond to particular text/command, uncomment to activate
      // --------------------------------------------------------------------------------------------

           // // customize bot's response
           // if ($body == "!ping") {
           //   $respon = "pong! $name";
           // }
           // elseif ($body == "!help") {
           //   $respon = "can I help you $name? 😎";
           // }
         
           // // if $respon is not empty, send it
           // if (!empty($respon)) {

           //   $w->sendMessageComposing($from);
           //   sleep(3);
           //   $w->sendMessage($from, $respon);
           //   $w->pollMessage();
           // }
           
    }

  }


  // on group message
  function onGetGroupMessage($mynumber, $from_group_jid, $from_user_jid, $id, $type, $time, $name, $body)
  {
    global $ke, $w, $poll;
    $from_group = str_replace(array("@s.whatsapp.net","@g.us"), "", $from_group_jid);     // group id
    $from_user = str_replace(array("@s.whatsapp.net","@g.us"), "", $from_user_jid);       // sender's phone number
    $bodi = str_replace( array("\n", "\r\n", "\r") , " ", $body);

    echo "\n[$ke][".date('H:i:s')."] [$from_group_jid]\n";
    echo "--- group: $from_group | user : $from_user\n";
    echo "--- $name > $bodi\n";

    // display all group message parameter
    echo "mynumber        : $mynumber\n";
    echo "from_group_jid  : $from_group_jid\n";
    echo "from_group      : $from_group\n";
    echo "from_user_jid   : $from_user_jid\n";
    echo "from_user       : $from_user\n";
    echo "id              : $id\n";
    echo "type            : $type\n";
    echo "time            : $time\n";
    echo "name            : $name\n";
    echo "body            : $body\n";


    if ($poll > 5) {   // <-- read what's this on 'onGetMessage' function above

      // read detailed explanation on 'onGetMessage' function above
      
      // (1) echo back any messages sent to group, be careful not to spam. Uncomment to activate.
      // --------------------------------------------------------------------------------------------

          // $w->sendMessageComposing($from_group);
          // sleep(3);
          // $w->sendMessage($from_group, $body);
          // $w->pollMessage();


      // (2) or respond to particular text/command
      // --------------------------------------------------------------------------------------

           if ($body == "!ping") {
             $respon = "[group] pong! $name";
           }
           elseif ($body == "!help") {
             $respon = "[group] can I help you $name? 😎";
           }

           if (!empty($respon)) {
             $w->sendMessageComposing($from_group);
             sleep(3);
             $w->sendMessage($from_group, $respon);
             $w->pollMessage();
           }

    }

  }


  function onConnect($mynumber, $socket)
  {
    global $ke;
    echo "[$ke][".date('H:i:s')."] - $mynumber logged in..!\n";
    echo "[$ke][".date('H:i:s')."] - Wait until the bot is ready..!\n";
    echo "[$ke][".date('H:i:s')."] ---------------------------------------------------------\n";
  }

  function onDisconnect($mynumber, $socket)
  {
    global $ke;
    echo "\n[$ke][".date('H:i:s')."] - $mynumber disconnected..!\n";
    exit(1);
  }

  function onClose($mynumber, $error)
  {
    global $ke;
    echo "\n[$ke][".date('H:i:s')."] EVENT: onClose\n";
    echo " mynumber : $mynumber\n";
    echo " error    : $error\n\n";
  }

  function check_connection() { 
    global $ke;
    $connected = @fsockopen("www.google.com", 80); 
    if ($connected){
      fclose($connected);
    } else {
      echo "\n[$ke][".date('H:i:s')."] Can't access internet..! pausing for 15 seconds..\n";
      sleep(15);
      $connected2 = @fsockopen("www.google.com", 80); 
      if ($connected2) {
        fclose($connected2);
      } else {
        echo "\n[$ke][".date('H:i:s')."] EROR: Can't access internet..!\n";
        exit(1);
      }
    }
  }

 ?>
