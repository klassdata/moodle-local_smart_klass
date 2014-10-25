<?php
namespace SmartKlass\xAPI;

/**
 * xAPI InteractionComponent Class
 *
 * @package    local_smart_klass
 * @copyright  KlassData <kttp://www.klassdata.com>
 * @author     Oscar Ruesga <oscar@klassdata.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class InteractionComponent extends xApiObject {
    
    /**
     * @access private
     * @var string 
     */
    private $id = null;
    
    /**
     * @access private
     * @var string 
     */
    private $description = null;
    
    

    public function __construct($id, $description=null, $lang='es') {
        $this->id = $this->setId($id);
        if ($description != null)
             $this->description = $this->description ($description, $lang);
    }
    
    public function setId ($id) {
        $this->id = $id;
        return $this;
    }
    
    public function getId () {
        return $this->id;
    }
    
    public function setDescription ($description=null, $lang='es') {
        if ($description != null)
             $this->description = array ($lang=>$description);
        return $this;
    }
    
    public function getDescription () {
        return $this->description;
    }
    
    public function expose () {
        $obj = new \stdClass();
        
        if ($this->id == null ) return null;
        $obj->id = $this->id;
        if ($this->description !== null ) $obj->description = $this->description;
 
        return  $obj;
    }
}