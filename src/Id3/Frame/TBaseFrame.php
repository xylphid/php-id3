<?php
namespace Id3\Frame;

use Id3\Id3Encoding;

class TBaseFrame extends BaseFrame {
    public const MULTIPLE = false;
    protected string $encoding;
    protected string $value;

    public function __construct(string $tag, int $size, $flags, &$reader, int $id3Size) {
        parent::__construct($tag, $size, $flags, $reader, $id3Size);

        $this->encoding = fread($reader, 1);
        $this->value = fread($reader, $this->size - 1);
        $this->value = mb_convert_encoding(
            $this->value,
            'UTF-8',
            Id3Encoding::from(bin2hex($this->encoding))->toString()
        );
    }

    /**
     * Get frame size
     * @return int
     */
    public function getSize(): int {
        $this->size = strlen($this->encoding) + strlen($this->value);
        return $this->size;
    }

    public function setValue(string $value): self {
        $this->size = strlen($this->encoding) + strlen($this->value);
        $this->value = $value;

        return $this;
    }

    public function pack(): string {
        return pack('NnCa*', $this->size, $this->flags, chr($this->encoding), $this->value);
    }

    public function __toString(): string {
        return $this->value ?: '';
    }
}
