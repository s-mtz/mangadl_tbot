<?php

require_once('config.php');
// require_once('bot_api.php');

// Get into the next page
function get_next_page($job)
{
    $job['page'] += 1;
    return $job;
}

// Get into the next chapter
function get_next_chapter($job)
{
    $job['chapter_current'] += 1;
    $job['page'] = 1;
    return $job;
}

// Create manga file directory
function make_manga_dir($job)
{
    $file_crawler_path = FILES_DIR . $job['crawler'] . '/';
    if (!file_exists($file_crawler_path)) {
        mkdir($file_crawler_path);
    }
    $file_manga_path = $file_crawler_path . $job['name'] . '/';
    if (!file_exists($file_manga_path)) {
        mkdir($file_manga_path);
    }
    $file_chapter_path = $file_manga_path . $job['chapter_current'] . '/';
    if (!file_exists($file_chapter_path)) {
        mkdir($file_chapter_path);
    }
    return $file_chapter_path;
}

// creat a pdf out of an image list
function make_pdf($_image_list, $_file_path, $_pdf_name, $_pdf_chapter)
{
    $im = new Imagick($_image_list);
    $im->setImageFormat('pdf');
    $im->writeImages($_file_path . $_pdf_name . " " . $_pdf_chapter . ".pdf", true);
}
