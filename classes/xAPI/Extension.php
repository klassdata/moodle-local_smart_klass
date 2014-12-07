<?php
namespace SmartKlass\xAPI;

/**
 * xAPI Extension Class
 *
 * @package    local_smart_klass
 * @copyright  KlassData <kttp://www.klassdata.com>
 * @author     Oscar Ruesga <oscar@klassdata.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class Extension extends xApiObject {
    
    /**
     * @access private
     * @var string 
     */
    private $key = null;
    
    /**
     * @access private
     * @var string 
     */
    private $value = null;
    

    public function __construct($key=null, $value=null) {
        $this->setKey($key);
        $this->setValue($value);
    }
    
    public function setKey ($key) {
        $this->key = filter_var($key, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED);
        $this->key = ($this->key==false) ? null : $key;
        return $this;
    } 
    
    public function getKey () {
        return $this->key;
    }
    
    public function setValue ($value) {
        $this->value = $value;
        return $this;
    }
    
    public function getValue () {
        return $this->value;
    }
    
    public function expose () {
        if ( $this->key == null || $this->value == null ) return null;
        $obj = new \stdClass;
        $obj->key = $this->key;
        $obj->value = $this->value;
        return $obj;
    }
}