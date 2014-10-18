<?php
namespace Klap\xAPI;

/**
 * xAPI StatementRef Class
 *
 * @package    local_klap
 * @copyright  Klap <kttp://www.klaptek.com>
 * @author     Oscar <oscar@klaptek.com>
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