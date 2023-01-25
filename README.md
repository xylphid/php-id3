# PHP-ID3

PHP-ID3 is a native php lib for ID3 tags

## Compatibility
PHP-ID3 is tested on the following PHP version :

* PHP 8.2

## Installation
```bash
composer install xylphid/php-id3
```

## Usage

### PHP Script

Step by step extraction :
```php
use Id3\Id3;

$media = '/path/to/media/file.mp3';
$id3 = new Id3Parser();
$id3->setFilename($media);
$id3->processFile();
```
Auto process :
```php
$media = '/path/to/media/file.mp3';
$id3 = new Id3Parser($media);
```

Found tags are registered as object properties and named according to Id3 specifications. You can display tags with :
```php
if ($id3->isCompliant()) {
    printf("Title : %s\n", $id3->getTitle());
    printf("Artist : %s\n", $id3->getArtist());
    printf("Album : %s\n", $id3->getAlbum());
    printf("Track : %s\n", $id3->getTrack());
    printf("PartOfSet : %s\n", $id3->getPartOfSet());
    printf("Genre : %s\n", $id3->getGenre());
    printf("Year : %s\n", $id3->getYear());
    printf("Duration : %s\n", $id3->getDuration())
}
```