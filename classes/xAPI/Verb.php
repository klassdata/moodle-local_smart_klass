<?php
namespace SmartKlass\xAPI;

/**
 * xAPI Verb Class
 *
 * @package    local_smart_klass
 * @copyright  KlassData <kttp://www.klassdata.com>
 * @author     Oscar Ruesga <oscar@klassdata.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class Verb extends xApiObject {
    
    /**
     * @access private
     * @var string 
     */
    private $verb = null;
    

    public function __construct($verbid=null) {
        $verbid = ($verbid==null) ? 'voided' : $verbid;
        $this->setId($verbid);
    }
    
    public function setId ($verbid) {
        $verb_definition = dirname(__FILE__) . "/Verbs/$verbid.php";
		if(file_exists($verb_definition)){
			$verbid = 'SmartKlass\\xAPI\\' . $verbid;
            $this->verb = new $verbid();     
        } 
        return $this;
    }
       
    public function expose () {
        return  $this->verb->expose();
    }
}