<?php
class Mci extends Frame {
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
			
		$this->_text = hexdec(bin2hex(fread($this->_reader, $this->_size)));
	}
}