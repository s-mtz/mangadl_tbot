<?php

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

function last_page_finder($_name, $_chapter)
{
  $url = "http://www.mangapanda.com/$_name/$_chapter";

  $html = file_get_contents($url);
  $last_page = intval(get_inner_string($html, '</select> of ', '</div>'));
  if ($last_page > 0)
    return $last_page;
}

function image_finder($_name, $_chapter, $_page)
{
  $url = "http://www.mangapanda.com/$_name/$_chapter/$_page";

  $html = file_get_contents($url);

  $image_url = get_inner_string($html, 'id="img"', 'alt=');
  $image_url = substr($image_url, strpos($image_url, 'https://'));
  $image_url = substr($image_url, 0, strpos($image_url, '"'));

  if (strlen($image_url) > 10)
    return $image_url;
}
