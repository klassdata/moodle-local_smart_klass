<?php
namespace Klap\xAPI;

/**
 * xAPI answered Verb
 *
 * @package    local_klap
 * @copyright  Klap <kttp://www.klaptek.com>
 * @author     Oscar <oscar@klaptek.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class VerbObject extends xApiObject {
    
    /**
     * @access private
     * @var string 
     */
    protected $id = null;

    /**
     * @access private
     * @var string 
     */
    protected $display = array();
    
    
    /**
     * @access private
     * @var string 
     */
    protected $description = array();
    

    public function __construct() {return $this;}
    
    public function expose () {
        $obj = new \stdClass();    
        $obj->id = $this->id;
        $obj->display = $this->display;
        return get_object_vars($obj);
    }
}
