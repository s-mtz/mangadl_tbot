<?php

require_once('functions.php');

if (isset($_SERVER['REQUEST_METHOD']) && !ALLOW_GET_REQUEST_DOWNLOADER) {
    header('HTTP/1.1 400 BAD REQUEST');
    exit();
}

// Get manga and set the arguments to the job
if (isset($argv)) {
    if (count($argv) >= 3) {
        $job['crawler'] = $argv[1];
        $job['name'] = $argv[2];
    }
    if (count($argv) >= 4) {
        $job['chapter_start'] = intval($argv[3]);
        $job['chapter_current'] = intval($argv[3]);
    }
    if (count($argv) >= 5) {
        $job['chapter_end'] = intval($argv[4]);
    }
    if (count($argv) >= 6) {
        if ($argv[5] == "zip")
            $job['pdf'] = false;
    }
}

// Include crawler
$crawler_path = CRAWLER_DIR . $job['crawler'] . '.php';
if (!file_exists($crawler_path)) {
    exit('Crawler ' . $job['crawler'] . ' does not exist');
}

require_once($crawler_path);

// Include more option to $job
$meta = [
    'chapter_start' => 0,
    'chapter_current' => 0,
    'chapter_end' => 0,
    'pdf' => true,
    'page' => 1,
    'last_page' => 0,
    'time' => 0,
];

$job = array_merge($meta, $job);

// for different chapters
for ($i = $job['chapter_current']; $i < $job['chapter_end']; $i++) {

    // find the last page of a chapter
    $job['last_page'] = last_page_finder($job['name'], $job['chapter_current']);

    // make a new array to insert image to make pdf or zip file
    if ($job['pdf'] == true) {
        $image_list = [];
    } else {
        $zip = new ZipArchive;
    }

    // for different pages
    for ($j = $job['page']; $j <= $job['last_page']; $j++) {

        // make foulder for manga
        $file_chapter_path = make_manga_dir($job);

        // make the image path
        $save_image_path = $file_chapter_path . $job['page'] . '.jpg';

        // check if the page image exists to prevent duplicate download
        if (!file_exists($save_image_path)) {

            // download the page
            copy(image_finder($job['name'], $job['chapter_current'], $job['page']), $save_image_path);

            // echo
            echo $job['name'] . " manga - chapter " . $job['chapter_current'] . " - page " . $job['page'] . " is downloaded\n";
        } else echo $job['name'] . " manga - chapter " . $job['chapter_current'] . " - page " . $job['page'] . " already exists \n";


        // push the pages into array to make pdf or Add files to the zip file
        if ($job['pdf'] == true) {
            array_push($image_list, $save_image_path);
        } else {
            $zip->open('test_new.zip', ZipArchive::CREATE);
            $zip->addFile($save_image_path);
        }

        // get the next page
        $job = get_next_page($job);
    }

    // check if its pdf or zip
    if ($job['pdf'] == true) {

        // turn the array to pdf
        make_pdf($image_list, $file_chapter_path, $job['name'], $job['chapter_current']);

        // echo
        echo $job['name'] . "manga - chapter " . $job['chapter_current'] . " PDF is ready\n";
    } else {

        // close the zip file
        $zip->close();
        echo $job['name'] . "manga - chapter " . $job['chapter_current'] . " ZIP is ready\n";
    }

    // get the next chapter
    $job = get_next_chapter($job);
}
