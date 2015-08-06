<?php
// Edit include path
$include_paths = array(
        get_include_path(),
        'Frame',
        'Std'
        );
set_include_path(implode(PATH_SEPARATOR, $include_paths));

// Define class autoload
function dynamicLoad($class) {
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
$media = '/path/to/media/file.mp3';

if ($autoProcess)
    $id3 = new Id3($media);
else {
    $id3 = new Id3();
    $id3->setFilename($media);
    $id3->processFile();
}

if ($id3->isCompliant()) {
    printf("Artist : %s\n", $id3->getTpe1());
    printf("Album : %s\n", $id3->getTalb());
    printf("Title : %s\n", $id3->getTit2());
    printf("Track : %s\n", $id3->getTrck());
}
?>