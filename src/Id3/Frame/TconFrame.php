<?php
namespace Id3\Frame;

use Id3\Genre;

class TconFrame extends TBaseFrame {
    public function __toString(): string {
        $key = sprintf('GENRE_%s', preg_replace('/[()]/', '', $this->value));
        return Genre::find($key) ?: $this->value;
    }
}
