<?php

namespace Id3\Frame;

use Id3\GetSet;

/**
 * Generic Frame
 * @author Xylphid
 */
class Frame extends GetSet {
    const TAG_DISCARD_CHANGE = 0x20000;
    const FILE_DISCARD_CHANGE = 0x10000;
    const READ_ONLY = 0x8000;
    const GROUP_IDENTITY = 0x400;
    const COMPRESSION = 0x08;
    const ENCRYPTION = 0x04;
    const UNSYNCHRONIZATION = 0x02;
    const LENGTH_INDICATOR = 0x01;

    const ISO88591 = 0x00;
    const UTF16 = 0x01;
    const UTF16BE = 0x02;
    const UTF16LE = 0x04;
    const UTF8 = 0x03;

    protected int       $_encoding    = 0x00;
    protected mixed     $_flag        = 0;
    protected           $_reader;
    protected int       $_size;
    protected string    $_tagname;

    public function __construct($options = array()) {
        if (!array_key_exists('reader', $options)) {
            return;
        }
        if (array_key_exists('tagname', $options)) {
            $this->_tagname = $options['tagname'];
        }

        $this->_reader = $options['reader'];
        if ($options['version'] == 2) {
            $this->_size = hexdec(bin2hex(fread($this->_reader, 3)));
        } else {
            $size = unpack('N', fread($this->_reader, 4));
            $this->_size = $size[1] > 1 ? $size[1] : 2;
            $this->_flag = unpack('n*', fread($this->_reader, 2));
            $this->_flag = $this->_flag[1];
        }
    }

    /**
     * Get frame size
     * @return int
     */
    public function getSize(): int {
        return $this->_size;
    }

    protected function _translateEncoding($encoding = 'utf-8'): string {
        if (is_string($encoding)) {
            return $encoding;
        }

        if (is_integer($encoding)) {
            return match ($encoding) {
                self::UTF16 => 'utf-16',
                self::UTF16BE => 'utf-16be',
                self::UTF16LE => 'utf-16le',
                self::ISO88591 => 'iso-8859-1',
                default => 'utf-8',
            };
        }

        return 'utf-8';
    }
}
