<?php
namespace Id3\Frame;

class TBaseFrame extends BaseFrame {
    public const MULTIPLE = false;
    protected string $encoding;
    protected string $value;

    public function __construct(string $tag, int $size, $flags, &$reader, int $id3Size) {
        parent::__construct($tag, $size, $flags, $reader, $id3Size);

        $this->encoding = ord(fread($reader, 1));
        $this->value = preg_replace('/[[:^print:]]/', '', fread($reader, $this->size - 1));
        $this->value = iconv($this->translateEncoding(), 'UTF-8', $this->value);
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

    private function translateEncoding(): string {
        return match ($this->encoding) {
            0x00    => 'ISO-8859-1',
            0x01    => 'UTF-16',
            0x02    => 'UTF-16BE',
            0x03    => 'UTF-8',
            0x04    => 'UTF-16LE',
            default => 'UTF-8'
        };
    }

    public function pack(): string {
        return pack('NnCa*', $this->size, $this->flags, chr($this->encoding), $this->value);
    }

    public function __toString(): string {
        return $this->value ?: '';
    }
}
