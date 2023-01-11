<?php
namespace Id3;

use Exception;

/**
 * Abstract class than implements dynamic system
 *  - add[Property]
 *  - get[Property]
 *  - has[Property]
 *  - remove[Property]
 *  - set[Property]
 * @author Xylphid
 */
abstract class GetSet {
    protected $_properties = array();
    
    public function __call($method, $params) {
        try{
            if (preg_match('/^(add|get|has|set|remove|is)([A-Z][\w]*)$/', $method, $aMatches)) {
                if ($aMatches[1] == 'get') {
                    return $this->_get(lcfirst($aMatches[2]));
                } elseif ($aMatches[1] == 'has') {
                    return $this->_has(lcfirst($aMatches[2]));
                } elseif ($aMatches[1] == 'remove') {
                    $this->_remove(lcfirst($aMatches[2]));
                } elseif ($aMatches[1] == 'set') {
                    if (count($params) != 1) {
                        throw new Exception('Method ' . $method . '" is missing 1 parameter.');
                    }
                    return $this->_set(lcfirst($aMatches[2]), $params[0]);
                } elseif ($aMatches[1] == 'add') {
                    if (count($params) != 1) {
                        throw new Exception('Method "' . $method . '" is missing 1 parameter.');
                    }
                    $this->_add(lcfirst($aMatches[2]), $params[0]);
                } elseif ($aMatches[1] == 'is') {
                    return $this->_is(lcfirst($aMatches[2]));
                }
            }
        }
        catch (Exception $e) {
            trigger_error($e->getMessage());
            return false;
        }
    }

    /**
     * Add a value to the property (array)
     * @param $property
     * @param $value
     * @return void
     */
    protected function _add($property, $value): void {
        if (property_exists($this, $property)) {
            $this->{$property}[] = $value;
        } else {
            $this->_properties[$property][] = $value;
        }
    }

    /**
     * Return the property value
     * @param $property
     * @return mixed
     */
    protected function _get($property): mixed {
        $property[0] = strtolower($property[0]);
        if (property_exists($this, $property)) {
            return $this->$property;
        } elseif(array_key_exists($property, $this->_properties)) {
            return $this->_properties[$property];
        } else {
            trigger_error('Method "get' . ucfirst($property) . '()" does not exist in "' . get_class( $this ) . '" class.');
        }
    }

    /**
     * Check if the property is set
     * @param $property
     * @return bool
     */
    protected function _has($property): bool {
        $property[0] = strtolower($property[0]);
        return (property_exists($this, $property) || array_key_exists($property, $this->_properties));
    }

    /**
     * Check boolean value of the property
     * @param $property
     * @return bool
     */
    protected function _is($property): bool {
        $property[0] = strtolower($property[0]);
        if (property_exists($this, $property) && $this->$property === true) {
            return true;
        } elseif (array_key_exists($property, $this->_properties) && $this->_properties[$property] === true) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * Set property value
     * @param $property
     * @param $value
     * @return self
     */
    protected function _set($property, $value): self {
        if (property_exists($this, $property)) {
            $this->$property = $value;
        } else {
            $this->_properties[$property] = $value;
        }
        return $this;
    }

    /**
     * Remove property or its value
     * @param $property
     * @return void
     */
    protected function _remove($property): void {
        $property[0] = strtolower($property[0]);
        if (property_exists($this, $property)) {
            $this->$property = null;
        } elseif(array_key_exists($property, $this->_properties)) {
            unset($this->_properties[$property]);
        } else {
            trigger_error('Method "unset' . ucfirst($property) . '() does not exist');
        }
    }
    
    /**
     * Check if property exists
     * @throws Exception
     * @return boolean
     */
    public function exists(): bool {
        return property_exists($this, 'exists') && $this->exists;
    }
    
    /**
     * Check if the property is empty
     * @param string $property
     * @return boolean
     */
    public function isEmpty(string $property): bool {
        if (property_exists($this, $property)) {
            return !is_numeric($this->$property) && empty($this->$property);
        } else {
            return !array_key_exists($property, $this->_properties)
                || !is_numeric($this->_properties[$property]) && empty($this->_properties[$property]);
        }
    }

    public function __isset(string $name): bool {
        return property_exists($this, $name);
    }
}
