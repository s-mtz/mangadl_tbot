<?php

$token_path = "https://api.telegram.org/bot956711743:AAFFdGFskUekAmNnyUrbH2gEUu22Fs0w_xc";

$getme = "/getMe";
$getupdates = "/getUpdates";

$update = json_decode(file_get_contents($path . $getupdates), TRUE);

var_dump($update);

// sendfile?chat_id=132465&file=sahfiuhagiuhaihisda