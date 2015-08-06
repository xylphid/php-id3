# PHP-ID3

PHP-ID3 is a native php lib for ID3 tags

## Compatibility
PHP-ID3 is tested on the following PHP version :

* PHP 5.3
* PHP 5.4
* PHP 5.5

## Usage

### PHP Script

Step by step extraction :

    media = '/path/to/media/file.mp3';
    $id3 = new Id3();
    $id3->setFilename($media);
    $id3->processFile();

Auto process :

    $media = '/path/to/media/file.mp3';
    $id3 = new Id3($media);

Found tags are registered as object properties and named according to Id3 specifications. You can display tags with :

    if ($id3->isCompliant()) {
        printf("Artist : %s\n", $id3->getTpe1());
        printf("Album : %s\n", $id3->getTalb());
        printf("Title : %s\n", $id3->getTit2());
        printf("Track : %s\n", $id3->getTrck());
    }
