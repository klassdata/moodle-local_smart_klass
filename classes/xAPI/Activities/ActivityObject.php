<?php
namespace SmartKlass\xAPI;

/**
 * xAPI answered Verb
 *
 * @package    local_smart_klass
 * @copyright  KlassData <kttp://www.klassdata.com>
 * @author     Oscar Ruesga <oscar@klassdata.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class ActivityObject extends xApiObject {
    
    /**
     * @access private
     * @var string 
     */
    protected $type = null;

    /**
     * @access protected
     * @var array  
     */
    protected $name = array();
    
    
    /**
     * @access private
     * @var string 
     */
    protected $description = array();
    
    /**
     * @access private
     * @var string 
     */
    protected $moreInfo = array();
    

    public function __construct() {}
    
    public function getType () {
        return $this->type;
    }
    
    public function getName () {
        return $this->name;
    }
    
    public function getDescription () {
        return $this->description;
    }
    
    public function getMoreInfo () {
        return $this->moreInfo;
    }
    
    public function expose () {
        $obj = new \stdClass();
        $obj->name = $this->name;
        $obj->type = $this->type;
        //$obj->description = $this->description;
        //$obj->moreInfo  = $this->moreInfo ;
        return $obj;
    }
}
