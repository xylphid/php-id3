<?php
namespace Id3;

use Exception;
use Id3\Exception\FileNotFoundException;
use Id3\Exception\FileReadException;
use Id3\Exception\NotCompliantException;
use Id3\Exception\RemoteFilenameException;
use Id3\Frame\BaseFrame;

class Id3Parser extends Id3Accessor {
    public bool $compliant = false;
    private ?string $source;
    protected string $version;
    private Id3Flag $flags;
    private int $size;

    /**
     * @param string|null $file
     * @throws FileNotFoundException
     * @throws FileReadException
     * @throws NotCompliantException
     * @throws RemoteFilenameException
     */
    public function __construct(string $file = null) {
        if ($file !== null) {
            $this->setFilename($file);
            $this->processFile();
        }
    }

    /**
     * Set source file
     * @param string $file
     * @return void
     * @throws FileNotFoundException
     * @throws RemoteFilenameException
     */
    public function setFilename(string $file): void {
        if (preg_match('/^(ht|f)tp:\/\//', $file)) {
            throw new RemoteFilenameException();
        } elseif (!file_exists($file)) {
            throw new FileNotFoundException(__METHOD__ . ' : File not found.');
        }

        $this->source = $file;
    }

    /**
     * Process file, extract all available informations
     * @return void
     * @throws FileReadException
     * @throws NotCompliantException
     */
    public function processFile() {
        if (($reader = fopen($this->source, 'r')) === false) {
            throw new FileReadException(__METHOD__ . ' : Unable to open file');
        }

        try {
            $this->extractHeaders($reader);
            if ($this->version) {
                $this->compliant = true;
                $this->extractTags($reader);
            }
        } catch (NotCompliantException $e) {
            if (is_resource($reader)) {
                fclose($reader);
            }

            throw $e;
        }
    }

    /**
     * Read ID3 header
     * @param $reader
     * @return void
     * @throws NotCompliantException
     */
    public function extractHeaders(&$reader) {
        $headers = fread($reader, 10);
        $headers = unpack('a3signature/c1version_major/c1version_revision/c1flags/Nsize', $headers);

        if (!$headers || $headers['signature'] !== 'ID3') {
            throw new NotCompliantException(__METHOD__ . ' : This file does not contain ID3 tag');
        }

        $this->version = sprintf('%d.%d', $headers['version_major'], $headers['version_revision']);
        $this->size = $headers['size'];
        $this->readFlags($headers['flags']);
    }

    /**
     * Read ID3 header flags
     * @param $flags
     * @return void
     */
    private function readFlags($flags): void {
        if (($flags & Id3Flag::UNSYNCHRONIZE->value) == Id3Flag::UNSYNCHRONIZE->value) {
            error_log('ID3 header flag contains UNSYNCHROSINE');
        }
        if (($flags & Id3Flag::EXTENDED_HEADER->value) == Id3Flag::EXTENDED_HEADER->value) {
            error_log('ID3 header flag contains EXTENDED_HEADER');
        }
        if (($flags & Id3Flag::EXPERIMENTAL_INDICATOR->value) == Id3Flag::EXPERIMENTAL_INDICATOR->value) {
            error_log('ID3 header flag contains EXPERIMENTAL_INDICATOR');
        }
        if (($flags & Id3Flag::FOOTER->value) == Id3Flag::FOOTER->value) {
            error_log('ID3 header flag contains FOOTER');
        }
    }

    /**
     * Read tags
     * @param $reader
     * @return void
     */
    private function extractTags(&$reader): void {
        try {
            $frameSize = $this->version[2] == 2 ? 3 : 4;
            while(!feof($reader) && ftell($reader) < $this->size) {
                $frame = BaseFrame::createFrame($reader, $this->size, $frameSize);
                if (!$frame) {
                    continue;
                }

                $setter = 'set' . ucfirst($frame->getTag());
                $getter = 'get' . ucfirst($frame->getTag());
                /**
                 * Add frame if ...
                 * - Not yet referenced
                 * - Referenced and multiple instance allowed (CLASS::MULTIPLE)
                 */
                if (!array_key_exists($frame->getTag(), $this->_properties)) {
                    $this->$setter($frame);
                } elseif ($frame::MULTIPLE) {
                    $this->$getter->add($frame);
                }
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
        }
    }

    public function isCompliant(): bool {
        return $this->compliant;
    }

    protected function guessDuration(): int {
        $duration = 0;

        $command = sprintf('ffmpeg -i "%s" 2>&1 | grep -o "Duration: [0-9:.]*"', $this->source);
        $output = shell_exec($command);
        if (!empty($output)) {
            $output = str_replace('Duration: ', '', $output);

            // Get the duration in seconds
            $timeArr = array_reverse(explode(':', $output));
            for ($i = 0; $i < sizeof($timeArr); $i++) {
                $duration += $timeArr[$i] * pow(60, $i);
            }
        }

        return intval($duration * 1000);
    }
}
