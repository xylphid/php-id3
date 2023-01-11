<?php
namespace Id3\Std;

use ArrayAccess;
use Iterator;

/**
 * Collection Object
 * @author Xylphid
 */
class Collection implements Iterator {
    protected array $_collection = [];
    protected int   $_index = 0;

    public function __construct($value = null) {
        if ($value !== null) {
            if (is_array($value)) {
                $this->_collection = $value;
            } else {
                $this->_collection[] = $value;
            }
            $this->_index = $this->length();
        }
    }

    /**
     * Add a new value
     * @param mixed $value
     * @retun void
     */
    public function add(mixed $value): void {
        $this->append($value);
    }

    /**
     * Add a new value
     * @param mixed $value
     * @return void
     */
    public function append(mixed $value): void {
        $this->_collection[] = $value;
    }

    /**
     * Get current element
     * @return mixed
     * @see Iterator::current
     */
    public function current(): mixed {
        return $this->valid() ? $this->_collection[$this->_index] : null;
    }

    /**
     * Get current key
     * @return mixed
     * @see Iterator::key
     */
    public function key(): mixed {
        return $this->_index;
    }

    /**
     * Return collection size
     * @return int
     */
    public function length(): int {
        return count($this->_collection);
    }

    /**
     * Move to the next element
     * @return void
     * @see Iterator::next
     */
    public function next(): void {
        ++$this->_index;
    }

    /**
     * Reset iterator to the first element
     * @return void
     * @see Iterator::rewind
     */
    public function rewind(): void {
        $this->_index = 0;
    }

    /**
     * Check if the current position is valid
     * @return boolean
     * @see Iterator::valid
     */
    public function valid(): bool {
        return array_key_exists($this->_index, $this->_collection) && isset($this->_collection[$this->_index]);
    }
}
