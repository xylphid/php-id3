<?php
/**
 * Frame générique
 * @author Xylphid
 */
class Frame extends GetSet
{
	const TAG_DISCARD_CHANGE = 0x20000;//16384;
	const FILE_DISCARD_CHANGE = 0x10000;//8192;
	const READ_ONLY = 0x8000;//4096;
	const GROUP_IDENTITY = 0x400;//64;
	const COMPRESSION = 0x08;//8;
	const ENCRYPTION = 0x04;//4;
	const UNSYNCHRONISATION = 0x02;//2;
	const LENGTH_INDICATOR = 0x01;//1;
	
	const ISO88591 = 0x00;
	const UTF16 = 0x01;
	const UTF16BE = 0x02;
	const UTF16LE = 0x04;
	const UTF8 = 0x03;
	
	protected $_encoding = 0x00;
	protected $_flag= 0;
	protected $_reader;
	protected $_size;
	protected $_tagname;
	
	public function __construct($options = array())
	{
		if (!array_key_exists('reader', $options))
			return;
		if (array_key_exists('tagname', $options))
			$this->_tagname = $options['tagname'];
			
		if ($options['version'] == 2) {
			$this->_reader = $options['reader'];
			$this->_size = hexdec(bin2hex(fread($this->_reader, 3)));
		}
		else {
			$this->_reader = $options['reader'];
			$size = unpack('N', fread($this->_reader, 4));
			$this->_size = $size[1] > 1 ? $size[1] : 2;
			$this->_flag = unpack('n*', fread($this->_reader, 2));
			$this->_flag = $this->_flag[1];
		}
	}
	
	/**
	 * Retourne la taille de la frame
	 * @return int
	 */
	public function getSize()
	{
		return $this->_size;
	}
	
	protected function _translateEncoding($encoding = 'utf-8')
	{
		if (is_string($encoding))
			return $encoding;
			
		if (is_integer($encoding)) {
			switch ($encoding) {
				case self::UTF16 :
					return 'utf-16';
				case self::UTF16BE :
					return 'utf-16be';
				case self::UTF16LE :
					return 'utf-16le';
				case self::ISO88591 :
					return 'iso-8859-1';
				default :
					return 'utf-8';
			}
		}
		
		return 'utf-8';
	}
}
?>