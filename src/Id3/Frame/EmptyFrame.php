<?php

namespace Id3\Frame;

/**
 * Frame read but not processed
 * @author Xylphid
 */
class EmptyFrame extends Frame {
    public function __construct($options = array()) {
        parent::__construct($options);
        if ($this->_reader === null) {
            return;
        }

        fread($this->_reader, $this->_size);
    }
}
