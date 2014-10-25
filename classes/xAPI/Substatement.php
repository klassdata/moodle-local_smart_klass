<?php
namespace SmartKlass\xAPI;

/**
 * xAPI SubStatement Class
 *
 * @package    local_smart_klass
 * @copyright  KlassData <kttp://www.klassdata.com>
 * @author     Oscar Ruesga <oscar@klassdata.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class SubStatement extends xApiObject {
    
    /**
     * @access private
     * @var Agent|Group|
     */
    private $actor = null;

    /**
     * @access private
     * @var Verb
     */
    private $verb = null;
    
    /**
     * @access private
     * @var Activity|Agent|Group|StatementRef|SubStatement|null
     */
     private $object = null;
    
     /**
     * @access private
     * @var object
     */
     private $result = null;
     
     /**
     * @access private
     * @var object
     */
     private $context = null;
     
     /**
     * @access private
     * @var string
     */
     private $timestamp = null;


    public function __construct() {}
    
    public function setActor ($actor) {
        $this->actor = $actor;
        return $this;
    }
    
    public function getActor () {
        return $this->actor;
    }
    
    public function setVerb ($verb) {
        $this->verb = new Verb($verb);
        return $this;
    }
    
    public function getVerb () {
        return $this->verb;
    }
    
    public function setObject ($object) {
         $this->object = $object;
         return $this;
     }
     
     public function getObject() {
         return $object;
     }
     
     public function setResult ($param, $value) {
         if ($this->result == null)
             $this->result = new Result();
         $method = 'set' . ucfirst($param);
         
         if (method_exists ($this->result, $method))
           $this->result->$method ($value);
         else {
             $method = 'add' . ucfirst($param);
             if (method_exists ($this->result, $method))
               $this->result->$method ($value);
         }  
         return $this;
     }
     
     public function setResults ($obj){
         $score = ($obj instanceof Result) ? $obj->getScore() : $obj->score;
         if ($score != null) $this->setResult('score', $score);
         
         $success = ($obj instanceof Result) ? $obj->getSuccess() : $obj->success;
         if ($success != null) $this->setResult('success', $success);
         
         $completion = ($obj instanceof Result) ? $obj->getCompletion() : $obj->completion;
         if ($completion != null) $this->setResult('completion', $completion);
         
         $response = ($obj instanceof Result) ? $obj->getResponse() : $obj->response;
         if ($response != null) $this->setResult('response', $response);
         
         $duration = ($obj instanceof Result) ? $obj->getDuration() : $obj->duration;
         if ($duration != null) $this->setResult('response', $duration);
         return $this;
     }
     
     
     public function getResult () {
         return $this->result;
     }
     
     public function setContext ($param, $value) {
         if ($this->context == null)
             $this->context = new Context();
         
         $method = 'set' . ucfirst($param);
         
         if (method_exists ($this->context, $method))
           $this->context->$method ($value);
         else {
             $method = 'add' . ucfirst($param);
             if (method_exists ($this->context, $method))
               $this->context->$method ($value);
         }
         return $this;
     }
     
     public function getContext (){
         return $context;
     }
     
    public function setTimestamp ($t=null) {
        $t = ($t==null) ? time() : $t;
        $this->timestamp = date("c", $t);
        return $this;
    }
    
    public function getTimestamp () {
        return $this->timestamp;
    }
    
    
     public function expose () {
        $obj = new \stdClass();
        $class = new \ReflectionClass(get_class($this));
        $obj->objectType = $class->getShortName();  
        $obj->actor = $this->actor->expose();
        $obj->verb = $this->verb->expose();
        $obj->object = $this->object->expose();
        $result = $this->result->expose();
        if ( !empty($result) )
            $obj->result = $result;
        $context = $this->context->expose();
        if ( !empty($context) )
            $obj->context = $context;
        $timestamp = $this->timestamp;
        if ( !empty($timestamp) )
            $obj->timestamp = $timestamp;    
        return $obj;
    }
}