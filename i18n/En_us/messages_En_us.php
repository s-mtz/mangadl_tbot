<?php

$messages = [];
$messages["start"] =
    "Welcome to Mnanga-dl\n\nPlease use /help command for more information about how to use the bot\nAlso join the bot's channel @Mangadl_tch for the news and updates";
$messages[
    "help"
] = "first things first the only existing source at the momment is mangapanda.com and all the existing manga are accessble in the bot\n\nso for the first message and setting the source send the messange -> mangapanda\n\nthe second thing needed is the requsted manga so go to the mangapanda.com and choose the manga you want and enter it here but in this way:\n`shingeki-no-kyojin` ✅\nand not like this:\n`shingeki no kyojin` ❌
\n⭕️ use - instead of space ⭕️\n\nfot the next step you would be asked for the requsted starting chapter so enter the chapter you want as a single number like -> 1\n\nthen you need to set the finishing chapter as well so the bot gets you all the chapters in between and again enter it as a single number like -> 5\n\nthen you will recive chapters 1 - 2 - 3 - 4 - 5 of the manga shingeki-no-kyojin\n\nPlease note that only VIP memebers would recive all the chapters and as a normal user you would be granted with only the starting chapter you asked for\n\n for changing the language use the desired bottom\n/persian\n/english";

$messages["VIP"] =
    "Please choose one the below plans for yourself for VIP membership\n\n\nfor buying extera 50 chapter as 5000T /V50T\n\nfor buying extera 100 chapter as 9000T /V100T\n\nfor buying extera 200 chapter as 15000T /V200T";

$messages["cancel_success"] = "you stoped the queue secsusfully";
$messages["cancel_fail"] = "there is no queue to stop";

$messages["English"] =
    "you changed the language to English secussfully\nfor returning to hep page please press /help";
$messages["OutOfStock"] =
    "sorry to say but you are out of download limit\nyou can purchase limits to access vip membership for higher priority";
$messages["Wellcome"] = "as a new member you will be able to download manga up to 50 one";

$messages["Crawler_error"] = "please send the source correctly";
$messages["Crawler_success"] = "The source has been set secsusfully";

$messages["Manga_error"] = "Didnt recive the right manga name";
$messages["Manga_success"] = "The Manga has been set secsusfully";

$messages["Starting_chapter_error"] = "Please send the starting chapter correctly";
$messages["Starting_chapter_success"] = "The Starting chapter has been set secsusfully";

$messages["Finishing_chapter_error"] = "Please send the finishing chapter correctly";
$messages["Finishing_chapter_success_VIP"] =
    "as VIP member we will send you all the files you asked for\nPlease wait while we send you the requseted files\nyou also can use command /cancel to cancel the requsted chapters";
$messages["Finishing_chapter_success_NORMAL"] =
    "only the starting chapter would be sent to you\n please wait while we are making the files ready for you";
