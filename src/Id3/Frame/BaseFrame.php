<?php
namespace Id3\Frame;

use Id3\GetSet;

class BaseFrame extends GetSet {
    public const MULTIPLE = true;
    protected string $tag;
    protected int $size;
    protected $flags;

    protected function __construct(string $tag, int $size, $flags, $reader, int $id3Size) {
        $this->tag = $tag;
        $this->size = $size;
        $this->flags = $flags;
        /**
         * If the size of the frame is greater than header size,
         * We adjust to the remaining size to read
         */
        if ($id3Size - ftell($reader) < $this->size) {
            $this->size = $id3Size - ftell($reader);
        }
    }

    public static function createFrame(&$reader, int $id3Size, int $frameSize): ?BaseFrame {
        // Get frame headers
        $tag = mb_convert_case(unpack('Z4', fread($reader, $frameSize))[1], MB_CASE_TITLE);
        $size = unpack('N', fread($reader, $frameSize))[1] ?: 2;
        $flags = unpack('n*', fread($reader, 2))[1];

        // Ignore frame and move pointer to ID3 end
        if (!preg_match('/^[[:alnum:]]+$/', $tag)) {
            fseek($reader, $id3Size);
            return null;
        }

        $wantedClass = sprintf('%s\%sFrame', __NAMESPACE__, $tag);
        $baseClass = sprintf('%s\%sBaseFrame', __NAMESPACE__, $tag[0]);

        // Invoke class (frame / base) or continue
        if (class_exists($wantedClass)) {
            $frame = new $wantedClass($tag, $size, $flags, $reader, $id3Size);
        } elseif (class_exists($baseClass)){
            $frame = new $baseClass($tag, $size, $flags, $reader, $id3Size);
        } else {
            // Bypass current frame
            $size = (ftell($reader) + $size) > $id3Size ? $id3Size - ftell($reader) + 3 : $size;
            if ($size > 0 && (ftell($reader) + $size) <= $id3Size) {
                fread($reader, $size);
            }
            $frame = null;
        }

        return $frame;
    }
}
