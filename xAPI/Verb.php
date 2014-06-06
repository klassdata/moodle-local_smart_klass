<?php
namespace Klap\xAPI;

/**
 * xAPI Verb Class
 *
 * @package    local_klap
 * @copyright  Klap <kttp://www.klaptek.com>
 * @author     Oscar <oscar@klaptek.com>
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
			$verbid = 'Klap\\xAPI\\' . $verbid;
            $this->verb = new $verbid();     
        } 
        return $this;
    }
       
    public function expose () {
        return  $this->verb->expose();
    }
}