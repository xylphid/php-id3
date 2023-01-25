<?php
// Edit include path
use Id3\Id3;
use Id3\Id3Parser;

$include_paths = array(
        get_include_path(),
        dirname(__FILE__) . '/src'
        );
set_include_path(implode(PATH_SEPARATOR, $include_paths));

// Define class autoload
function dynamicLoad($class) {
    $class = preg_replace('/[\\\\]/', '/', $class);
    $paths = explode(PATH_SEPARATOR, get_include_path());
    foreach ($paths as $path) {
        if (file_exists($path. DIRECTORY_SEPARATOR . $class. '.php')) {
            require_once $path. DIRECTORY_SEPARATOR . $class. '.php';
        }
    }
}
// Register autoload
spl_autoload_register('dynamicLoad');

// Auto Process flag
$autoProcess = false;
// Media file
// $media = '/path/to/media/file.mp3';
$media = '/tmp/media.mp3';

if ($autoProcess) {
    $id3 = new Id3Parser($media);
} else {
    $id3 = new Id3Parser();
    $id3->setFilename($media);
    $id3->processFile();
}

if ($id3->isCompliant()) {
    // Display using tags getter
    printf("Artist : %s\n", $id3->getTpe1());
    printf("Title : %s\n", $id3->getTit2());
    printf("Track : %s\n", $id3->getTrck());
    printf("Genre : %s\n", $id3->getTcon());
    printf("\n");

    // Display using understandable getter
    printf("Title : %s\n", $id3->getTitle());
    printf("Artist : %s\n", $id3->getArtist());
    printf("Album : %s\n", $id3->getAlbum());
    printf("AlbumImage : %s\n", $id3->getAlbumImage()?->getType());
    printf("Track : %s\n", $id3->getTrack());
    printf("PartOfSet : %s\n", $id3->getPartOfSet());
    printf("Genre : %s\n", $id3->getGenre());
    printf("Year : %s\n", $id3->getYear());
    printf("Duration : %s\n", $id3->getDuration());
//    print_r($id3);
}
