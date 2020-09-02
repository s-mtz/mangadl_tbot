<?php

use GuzzleHttp\Client;

function get_inner_string($string, $begin, $end)
{
    if ($begin === 0)
        return substr($string, 0, strpos($string, $end));
    if ($end === 0)
        return substr($string, strpos($string, $begin) + strlen($begin));

    $string = ' ' . $string;
    $init = strpos($string, $begin);

    if ($init == 0)
        return '';

    $init += strlen($begin);
    $len = strpos($string, $end, $init) - $init;

    return substr($string, $init, $len);
}

function file_get_contents_curl($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        '[__cfduid]: d9c6aa4cca18b7a0fc83897ff700dc5171598882414'
    ));
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Set curl to return the data instead of printing it to the browser.
    curl_setopt($ch, CURLOPT_URL, $url);

    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}


$url = "https://w11.mangafreak.net/Manga/Shingeki_No_Kyojin";


$html = file_get_contents_curl($url);

var_dump($html);

// $image_url = get_inner_string($html, 'id="img"', 'alt=');
// $image_url = substr($image_url, strpos($image_url, 'https://'));
// $image_url = substr($image_url, 0, strpos($image_url, '"'));
