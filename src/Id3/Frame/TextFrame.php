<?php

namespace Id3\Frame;

/**
 * Text Frame
 * @author Xylphid
 */
class TextFrame extends Frame {
    protected string $_text;

    public function __construct($options = array())
    {
        parent::__construct($options);
        if ($this->_reader === null) {
            return;
        }

        /**
         * If the size of the frame is greater than header size,
         * We adjust to the remaining size to read
         */
        if (($options['size'] - ftell($this->_reader)) < $this->_size) {
            $this->_size = $options['size'] - ftell($this->_reader);
        }
        $this->_encoding = ord(fread($this->_reader, 1));
        $this->_text = fread($this->_reader, $this->_size - 1);
        $this->_text = iconv(
            $this->_translateEncoding($this->_encoding),
            'utf-8',
            $this->_text
        );
    }

    /**
     * Get frame size
     * @return int
     */
    public function getSize(): int {
        $this->_size = strlen($this->_encoding) + strlen($this->_text);
        return $this->_size;
    }

    /**
     * Set text value
     * @param string $value
     */
    public function setText($value): string {
        $this->_size = strlen($this->_encoding) + strlen($value);
        $this->_text = $value;
    }

    /**
     * Get packed data for write process
     * @return string
     */
    public function write(): string {
        return pack('NnCa*', $this->getSize(), $this->_flag, chr($this->_encoding), $this->_text);
    }

    public function __toString() {
        return $this->_text ?: '';
    }
}