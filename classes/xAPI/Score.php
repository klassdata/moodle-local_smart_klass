<?php
namespace SmartKlass\xAPI;

/**
 * xAPI Score Class
 *
 * @package    local_smart_klass
 * @copyright  KlassData <kttp://www.klassdata.com>
 * @author     Oscar Ruesga <oscar@klassdata.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class Score extends xApiObject {
    
    /**
     * @access private
     * @var string 
     */
    private $scaled = null;
    
    /**
     * @access private
     * @var string 
     */
    private $raw = null;
    
    /**
     * @access private
     * @var string 
     */
    private $min = null;
    
    /**
     * @access private
     * @var string 
     */
    private $max = null;
    

    public function __construct($raw=null, $min=0, $max=100) {
        if ($raw !== null)
            $this->setRaw($raw);
        if ($min !== null)
            $this->setMin($min);
        if ($max !== null)
            $this->setMax($max);
        
    }
    
    public function getScaled () {
        if ($this->raw != null && $this->min>=0 && $this->max>$this->min)
            $this->scaled = round($this->raw / ($this->max - $this->min), 2);
        
        return $this->scaled;
    }
    
    public function setRaw ($rawscore) {
        $this->raw = $rawscore;
        return $this;
    } 
    
    public function getRaw () {
        return $this->raw;
    }
    
    public function setMin ($minscore) {
        $this->min = $minscore;
        return $this;
    } 
    
    public function getMin () {
        return $this->min;
    }
    
    public function setMax ($maxscore) {
        $this->max = $maxscore;
        return $this;
    } 
    
    public function getMax () {
        return $this->max;
    }
    
    public function expose () {
        $obj = new \stdClass();
        
        if ($this->min !== null && is_float($this->min) ) $obj->min = $this->min;
        if ($this->max !== null && is_float($this->max) ) $obj->max = $this->max;
        if ($this->raw !== null && is_float($this->raw) ) $obj->raw = $this->raw;
        $scaled = $this->getScaled();
        if ($scaled !== null && is_float($scaled) ) $obj->scaled = $scaled;
            
        return  $obj;
    }
}