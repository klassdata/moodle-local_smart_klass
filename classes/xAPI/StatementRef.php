<?php
namespace SmartKlass\xAPI;

/**
 * xAPI StatementRef Class
 *
 * @package    local_smart_klass
 * @copyright  KlassData <kttp://www.klassdata.com>
 * @author     Oscar Ruesga <oscar@klassdata.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class StatementRef extends xApiObject {
    /**
     * @access private
     * @var string 
     */
    private $id = null;
    

    public function __construct($id=null) {
        $this->id = $id;
    }
       
    public function setId ($id=null) {
        $this->id = $id;
        return $this;
    }
    
    public function getId () {
        return $this->id;
    }
    
    
    
     public function expose () {
        $obj = new \stdClass();
        $class = new \ReflectionClass(get_class($this));
        $obj->objectType = $class->getShortName();  
        $obj->id = $this->id;
        return $obj;
    }
}