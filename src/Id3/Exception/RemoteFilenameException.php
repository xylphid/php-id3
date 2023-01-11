<?php
namespace Id3\Exception;

use Exception;
use Throwable;

class RemoteFilenameException extends Exception {
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null){
        $message = sprintf("Remote files are not supported - please copy the file locally first. %s", $message);
        parent::__construct($message, $code, $previous);
    }
}
