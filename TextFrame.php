<?php
/**
 * Frame générique de type texte
 * @author Xylphid
 */
class TextFrame extends Frame
{
	protected $_text;
	
	public function __construct($options = array())
	{
		parent::__construct($options);
		if ($this->_reader === null)
			return;
		
		/**
		 * Si la taille de la frame dépasse la taille des header,
		 * On ajuste à la taille restante à lire
		 */
		if (($options['size'] - ftell($this->_reader)) < $this->_size)
			$this->_size = $options['size'] - ftell($this->_reader);
		$this->_encoding = ord(fread($this->_reader, 1));
		$this->_text = fread($this->_reader, $this->_size - 1);
		//iconv(mb_detect_encoding($text, mb_detect_order(), true), "UTF-8", $text);
		//if (($this->_translateEncoding($this->_encoding) != 'iso-8859-1'))
		$this->_text =  iconv(
				$this->_translateEncoding($this->_encoding),
				'utf-8',
				$this->_text
			);
	}
	
	public function getSize()
	{
		$this->_size = strlen($this->_encoding) + strlen($this->_text);
		return $this->_size;
	}
	
	/**
	 * Définit la valeur text de l'objet
	 * @param string $value
	 */
	public function setText($value)
	{
		$this->_size = strlen($this->_encoding) + strlen($value);
		$this->_text = $value;
	}
	
	public function write()
	{
		//var_dump('text', strlen($this->_encoding), strlen($this->_text));
		$buffer = pack('NnCa*', $this->getSize(), $this->_flag, chr($this->_encoding), $this->_text);
		return $buffer;
	}
	
	public function __toString()
	{
		return $this->_text;
	}
}
?>