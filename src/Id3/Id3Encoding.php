<?php
namespace Id3;

enum Id3Encoding: string {
    case ISO_8859_1 = '00';
    case UCS_2 = '01';
    case UTF_16BE = '02';
    case UTF_8 = '03';

    public function toString() {
        return str_replace('_', '-', $this->name);
    }
}
