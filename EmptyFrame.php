<?php
/**
 * Frame lue mais non traitée
 * @author Xylphid
 */
class EmptyFrame extends Frame
{
	public function __construct($options = array())
	{
		parent::__construct($options);
		if ($this->_reader === null)
			return;
		
		fread($this->_reader, $this->_size);
	}
}