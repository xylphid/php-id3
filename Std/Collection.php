<?php
/**
 * Collection d'objets
 * @todo : A compléter
 * @author Xylphid
 */
class Collection implements Iterator
{
	protected $_collection = array();
	protected $_index = 0;
	
	public function __construct($value = null)
	{
		if ($value !== null)
		{
			if (is_array($value))
				$this->_collection = $value;
			else
				$this->_collection[] = $value;
		}
	}
	
	/**
	 * Ajoute une valeur à la collection
	 * @param mixed $value
	 * @retun void
	 */
	public function add($value)
	{
		$this->append($value);
	}
	
	/**
	 * Ajoute une valeur à la collection
	 * @param mixed $value
	 * @return void
	 */
	public function append($value)
	{
		$this->_collection[] = $value;
	}
	/**
	 * Retourne l'élément courant
	 * @see Iterator::current
	 * @return mixed
	 */
	public function current()
	{
		return $this->_collection[$this->_index];
	}
	
	/**
	 * Retourne la clé de l'élément courant
	 * @see Iterator::key
	 * @return scalar
	 */
	public function key()
	{
		return $this->_index;
	}
	
	/**
	 * Retourne la taille de la collection
	 * @return int
	 */
	public function length()
	{
		return count($this->_collection);
	}
	
	/**
	 * Se déplace sur l'élément suivant
	 * @see Iterator::next
	 * @return void
	 */
	public function next()
	{
		++$this->_index;
	}
	
	/**
	 * Replace l'itérateur au premier élément
	 * @see Iterator::rewind
	 * @return void
	 */
	public function rewind()
	{
		$this->_index = 0;
	}
	
	/**
	 * Vérifie si la position courante est valide
	 * @see Iterator::valid
	 * @return boolean
	 */
	public function valid()
	{
		return array_key_exists($this->_index, $this->_collection);
	}
}
?>