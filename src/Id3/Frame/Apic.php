<?php
namespace Id3\Frame;

/**
 * Attached Picture frame
 * @author Xylphid
 */
class Apic extends Frame {
    protected $_description = '';
    protected $_imageData;
    protected $_imageType;
    protected $_mimeType = '';

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

    public function __construct($options = array()) {
        // TODO : Constructor with source
        parent::__construct($options);
        if ($this->_reader === null) {
            return;
        }

        $this->_encoding = ord(fread($this->_reader, 1));
        $this->_size--;
        $this->extractMimeType();
        $this->extractType();
        $this->extractDescription();
        $this->extractData();
    }

    /**
     * Extrait le type mime de l'image
     * @return void
     */
    private function extractMimeType(): void {
        $i = 0;
        $next = true;
        while ($next) {
            $char = fread($this->_reader, 1);
            $next = ($char != "\x00");
            $this->_mimeType .= $next ? $char : '';
            $i++;
        }
        $this->_size -= $i;
    }

    /**
     * Get mime type
     * @return string
     */
    public function getMimeType(): string {
        return $this->_mimeType;
    }

    /**
     * Set mime type
     * @param string $mime
     * @return void
     */
    public function setMimeType(string $mime): void {
        $this->_mimeType = $mime;
    }

    /**
     * Extract image type
     * @return void
     */
    private function extractType(): void {
        $type = fread($this->_reader, 1);
        $this->_imageType = array_key_exists($type, self::$types) ? $type : "\x03";
        $this->_size--;
    }

    /**
     * Get image type (cover front/back ...)
     * @return string
     */
    public function getType(): string {
        return self::$types[$this->_imageType];
    }

    /**
     * Set image type (cover front/back ...)
     * @param mixed $type
     * @return void
     */
    public function setType(mixed $type): void {
        $this->_imageType = $type;
    }

    /**
     * Extract image description
     * @return void
     */
    private function extractDescription(): void {
        $i = 0;
        $next = true;
        while ($next) {
            $char = fread($this->_reader, 1);
            $next = ($char != "\x00");
            $this->_description .= $next ? $char : '';
            $i++;
        }
        $this->_size -= $i;
    }

    /**
     * Get image description
     * @return string
     */
    public function getDescription(): string {
        return $this->_description;
    }

    /**
     * Set description
     * @param string $description
     * @return void
     */
    public function setDescription(string $description): void {
        $this->_description = $description;
    }

    /**
     * Extrait le contenu de l'image
     * @return void
     */
    private function extractData(): void {
        $this->_imageData = fread($this->_reader, $this->_size);
    }

    /**
     * Get image raw content
     * @return mixed
     */
    public function getData(): mixed {
        return $this->_imageData;
    }

    /**
     * Set image content (binary) and size
     * @param string $data
     * @return void
     */
    public function setData($data): void {
        $this->_imageData = $data;
        $this->_size = strlen($this->_imageData);
    }

    /**
     * (non-PHPdoc)
     * @see Frame::getSize()
     */
    public function getSize(): int {
        return $this->_size;
    }

    /**
     * Get packed data for write process
     * @return string
     */
    public function write(): string {
        $buffer = pack('C*', $this->_encoding);
        $buffer .= $this->_mimeType . "\0";
        $buffer .= pack('C*', $this->_imageType);
        $buffer .= $this->_description . "\0";
        $buffer .= pack('a*', $this->_imageData);
        $this->_size = strlen($buffer);
        $buffer = pack('n*', $this->_flag) . $buffer;
        $buffer = pack('N*', $this->_size) . $buffer;

        return $buffer;
    }

    /**
     * Display image
     * @return void
     */
    public function display(): void {
        header('Content-Type: ' . $this->_mimeType);
        $im = imagecreatefromstring($this->_imageData);
        imagejpeg($im);
    }

    /**
     * Get base64 image representation
     * @return string
     */
    public function __toString(): string {
        return base64_encode($this->getData());
    }
}
