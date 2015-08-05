<?php
/**
 * Frame de type Attached Picture
 * @author Xylphid
 */
class Apic extends Frame
{
	protected $_description = '';
	protected $_imageData;
	protected $_imageType;
	protected $_mimeType = '';
	
	public $types = array(
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
					);
	
	public function __construct($options = array())
	{
		// TODO : Constructeur avec un source en paramètre
		parent::__construct($options);
		if ($this->_reader === null)
			return;
		
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
	private function extractMimeType()
	{
		$i = 0;
		$next = true;
		while ($next) {
			$char = fread($this->_reader, 1);
			$next = ($char == "\x00" ? false : true);
			$this->_mimeType .= $next ? $char : '';
			$i++;
		}
		$this->_size -= $i;
	}
	
	/**
	 * Retourne le type mime de l'image
	 * @return string
	 */
	public function getMimeType()
	{
		return $this->_mimeType;
	}
	
	/**
	 * Définit le type mime de l'image
	 * @param string $mime
	 * @return void
	 */
	public function setMimeType($mime)
	{
		$this->_mimeType = $mime;
	}
	
	/**
	 * Extrait le type d'image
	 * @return void
	 */
	private function extractType()
	{
		$type = fread($this->_reader, 1);
		$this->_imageType = array_key_exists($type, $this->types) ? $type : "\x03";
		$this->_size--;
	}
	
	/**
	 * Retourne le type de l'image (cover front/back ...)
	 * @return string
	 */
	public function getType()
	{
		return $this->types[$this->_imageType];
	}
	
	/**
	 * Définit le type de l'image (cover front/back ...)
	 * @param string $type
	 * @return void
	 */
	public function setType($type)
	{
		$this->_imageType = $type;
	}
	
	/**
	 * Extrait la description de l'image
	 * @return void
	 */
	private function extractDescription()
	{
		$i = 0;
		$next = true;
		while ($next) {
			$char = fread($this->_reader, 1);
			$next = ($char == "\x00" ? false : true);
			$this->_description .= $next ? $char : '';
			$i++;
		}
		$this->_size -= $i;
	}
	
	/**
	 * Retourne la description de l'image
	 * @return string
	 */
	public function getDescription()
	{
		return $this->_description;
	}
	
	/**
	 * Définit la description de l'image
	 * @param string $description
	 * @return void
	 */
	public function setDescription($description)
	{
		$this->_description = $description;
	}
	
	/**
	 * Extrait le contenu de l'image
	 * @return void
	 */
	private function extractData()
	{
		$this->_imageData = fread($this->_reader, $this->_size);
	}
	
	public function getData()
	{
		return $this->_imageData;
	}
	
	/**
	 * Défini le contenu de l'image (binaire)
	 * ainsi que la taille des données
	 * @return void
	 * @param string $data
	 */
	public function setData($data)
	{
		$this->_imageData = $data;
		$this->_size = strlen($this->_imageData);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Frame::getSize()
	 */
	public function getSize()
	{
		//$this->_size = strlen($this->_encoding) + strlen($this->_mimeType) + 1 + strlen($this->_imageType) + strlen($this->_description) + 1 + strlen($this->_imageData);
		return $this->_size;
	}
	
	public function write()
	{
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
	 * Affiche l'image
	 * @return void
	 */
	public function display()
	{
		header('Content-Type: ' . $this->_mimeType);
		$im = imagecreatefromstring($this->_imageData);
		imagejpeg($im);
	}
	
	/**
	 * Retourne une représentation de l'objet sous forme de chaine
	 * Ici, le contenu de l'image en base64
	 * @return string
	 */
	public function __toString()
	{
		return base64_encode($this->getData());
	}
}
?>