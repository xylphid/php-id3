<?php
namespace Id3;

enum Id3Flag: int {
    case UNSYNCHRONIZE = 0x800;
    case EXTENDED_HEADER = 0x400;
    case EXPERIMENTAL_INDICATOR = 0x200;
    case FOOTER = 0x100;
}
