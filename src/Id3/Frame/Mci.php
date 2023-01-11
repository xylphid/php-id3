<?php

namespace Id3\Frame;

class Mci extends Frame {
    public function __construct($options = array()) {
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

        $this->_text = hexdec(bin2hex(fread($this->_reader, $this->_size)));
    }
}
