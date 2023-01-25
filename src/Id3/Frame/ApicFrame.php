<?php
namespace Id3\Frame;

class ApicFrame extends BaseFrame {
    public static $types = [
        "\x00" => 'Other',
        "\x01" => '32x32 pixels file icon (PNG only)',
        "\x02" => 'Other file icon',
        "\x03" => 'Cover front',
        "\x04" => 'Cover back',
        "\x05" => 'Leaflet page',
        "\x06" => 'Media (label side of CD)',
        "\x07" => 'Lead artist/lead performer/soloist',
        "\x08" => 'Artist/performer',
        "\x09" => 'Conductor',
        "\x0A" => 'Band/Orchestra',
        "\x0B" => 'Composer',
        "\x0C" => 'Lyricist/text writer',
        "\x0D" => 'Recording Location',
        "\x0E" => 'During recording',
        "\x0F" => 'During performance',
        "\x10" => 'Movie/video screen capture',
        "\x11" => 'A bright coloured fish',
        "\x12" => 'Illustration',
        "\x13" => 'Band/artist logotype',
        "\x14" => 'Publisher/Studio logotype'
    ];
    protected string $encoding;
    protected string $description   = '';
    protected string $data;
    protected string $type;
    protected string $mimeType      = '';

    public function __construct(string $tag, int $size, $flags, &$reader, int $id3Size) {
        parent::__construct($tag, $size, $flags, $reader, $id3Size);

        $this->encoding = ord(fread($reader, 1));
        $this->extractMimeType($reader);
        $this->extractType($reader);
        $this->extractDescription($reader);
        $this->extractData($reader);
    }

    /**
     * Extract mime type
     * @param $reader
     * @return void
     */
    private function extractMimeType(&$reader): void {
        $next = true;
        while ($next) {
            $char = fread($reader, 1);
            $next = ($char != "\x00");
            $this->mimeType .= $next ? $char : '';
        }
    }

    /**
     * Extract image type
     * @param $reader
     * @return void
     */
    private function extractType(&$reader): void {
        $type = fread($reader, 1);
        $this->type = array_key_exists($type, self::$types) ? $type : "\x03";
    }

    /**
     * Extract image description
     * @param $reader
     * @return void
     */
    private function extractDescription(&$reader): void {
        $next = true;
        while ($next) {
            $char = fread($reader, 1);
            $next = ($char != "\x00");
            $this->description .= $next ? $char : '';
        }
    }

    /**
     * Extract image content
     * @param $reader
     * @return void
     */
    private function extractData(&$reader): void {
        $dataLength = $this->size - 1 - (strlen($this->mimeType) + 1) - 1 - (strlen($this->description) + 1);
        $this->data = fread($reader, $dataLength);
    }

    public function getType(): string {
        return self::$types[$this->type];
    }

    /**
     * Display image
     * @return void
     */
    public function display(): void {
        header('Content-Type: ' . $this->mimeType);
        $im = imagecreatefromstring($this->data);
        imagejpeg($im);
    }

    /**
     * Get base64 image representation
     * @return string
     */
    public function __toString(): string {
        return sprintf('%s : %d', $this->tag, $this->size);
    }
}
