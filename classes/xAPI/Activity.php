<?php
namespace SmartKlass\xAPI;

/**
 * xAPI Activity Class
 *
 * @package    local_smart_klass
 * @copyright  KlassData <kttp://www.klassdata.com>
 * @author     Oscar Ruesga <oscar@klassdata.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


class Activity extends xApiObject {
    
    /**
     * An identifier for a single unique Activity. Required.
     * @access private
     * @var URI 
     */
    private $id = null;

    /**
     * Optional Metadata                                                                             º
     * @access private
     * @var object 
     */
    private $definition = null;

   

    public function __construct($id=null) {
        $this->id = $id;
    }
    
    public function setId ($id) {
        
        $this->id = $id;
        return $this;
    }
    
    public function getId () {
        return $this->id;
    }
    
    public function setDefinition (ActivityDefinition $definition) {
        
        $this->definition = $definition;
        return $this;
    }
    
    public function getDefinition () {
        if ( !($this->definition instanceof ActivityDefinition) ) $this->definition = new ActivityDefinition;
        return $this->definition;
    }
    
    
    public function expose () {
        $obj = new \stdClass();
        
        $class = new \ReflectionClass(get_class($this));
        $obj->objectType = $class->getShortName();  
        
        if ($this->id !== null  )
            $obj->id = $this->id;
        
        if ( $definition = $this->definition ){
            $temp = $definition->expose();
            if (HelperXapi::isEmptyObject($definition) == false)
                $obj->definition = $temp;
        }
       
        
        return $obj;
    }
}