<?php
/**
 * Abstract class than implements dynamic system
 *  - add[Property]
 *  - get[Property]
 *  - has[Property]
 *  - remove[Property]
 *  - set[Property]
 * @author Xylphid
 */
abstract class GetSet
{
    protected $_properties = array();
    
    public function __call($method, $params)
    {
        try{
            if (preg_match('/^(add|get|has|set|remove|is)([A-Z][a-zA-Z0-9_]*)$/', $method, $aMatches))
            {
                if ($aMatches[1] == 'get') {
                    return $this->_get(lcfirst($aMatches[2]));
                }
                elseif ($aMatches[1] == 'has') {
                    return $this->_has(lcfirst($aMatches[2]));
                }
                elseif ($aMatches[1] == 'remove') {
                    return $this->_remove(lcfirst($aMatches[2]));
                }
                elseif ($aMatches[1] == 'set') {
                    if (count($params) != 1)
                        throw new Exception('La méthode "' . $method . '" requiert 1 paramètre.');
                    return $this->_set(lcfirst($aMatches[2]), $params[0]);
                }
                elseif ($aMatches[1] == 'add') {
                    if (count($params) != 1)
                        throw new Exception('La méthode "' . $method . '" requiert 1 paramètre.');
                    return $this->_add(lcfirst($aMatches[2]), $params[0]);
                }
                elseif ($aMatches[1] == 'is') {
                    return $this->_is(lcfirst($aMatches[2]));
                }
            }
        }
        catch (Exception $e) {
            trigger_error($e->getMessage());
            return false;
        }
    }
    
    protected function _add($property, $value)
    {
        if (property_exists($this, $property))
            $this->{$property}[] = $value;
        else
            $this->_properties[$property][] = $value;
        return true;
    }
    
    protected function _get($property)
    {
        $method = $property;
        $property{0} = strtolower($property{0});
        if (property_exists($this, $property)) {
            return $this->$property;
        }
        elseif(array_key_exists($property, $this->_properties)) {
            return $this->_properties[$property];
        }
        else {
            trigger_error('Method "get' . ucfirst($method) . '()" does not exist in "' . get_class( $this ) . '" class.');
        }
    }
    
    protected function _has($property)
    {
        $method = $property;
        $property{0} = strtolower($property{0});
        if (property_exists($this, $property) || array_key_exists($property, $this->_properties)) {
            return true;
        }
        else {
            return false;
        }
    }
    
    protected function _is($property)
    {
        $method = $property;
        $property{0} = strtolower($property{0});
        if (property_exists($this, $property) && $this->$property === true)
            return true;
        elseif (array_key_exists($property, $this->_properties) && $this->_properties[$property] === true)
            return true;
        else {
            return false;
        }
    }
    
    protected function _set($property, $value)
    {
        if (property_exists($this, $property))
            $this->$property = $value;
        else
            $this->_properties[$property] = $value;
        return true;
    }
    
    protected function _remove($property)
    {
        $method = $property;
        $property{0} = strtolower($property{0});
        if (property_exists($this, $property)) {
            $this->$property = null;
        }
        elseif(array_key_exists($property, $this->_properties)) {
            unset($this->_properties[$property]);
        }
        else {
            trigger_error('Method "unset' . $method . '() does not exist');
        }
    }
    
    /**
     * Retourne l'existence de l'objet
     * @throws Exception
     * @return boolean
     */
    public function exists()
    {
        return (property_exists($this, 'exists') && $this->exists) ? true : false;
    }
    
    /**
     * Vérifie si une propriété est vide
     * @param string $property
     * @return boolean
     */
    public function isEmpty($property)
    {
        if (property_exists($this, $property)) {
            return !is_numeric($this->$property) && empty($this->$property);
        }
        else
            return !array_key_exists($property, $this->_properties) ? true : !is_numeric($this->_properties[$property]) && empty($this->_properties[$property]);
        
    }
}
?>