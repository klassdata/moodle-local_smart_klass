<?php
namespace SmartKlass\xAPI;

/**
 * xAPI Statement Class
 *
 * @package    local_smart_klass
 * @copyright  KlassData <kttp://www.klassdata.com>
 * @author     Oscar Ruesga <oscar@klassdata.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class Statement extends xApiObject {
    /**
     * @access private
     * @var string 
     */
    private $id = null;
    
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

     /**
     * @access private
     * @var string
     */
     private $stored = null;

     /**
     * @access private
     * @var Agent|null
     */
     private $authority = null;

     /**
     * @access private
     * @var string
     */
     private $version = null;

    public function __construct($id=null) {
        //Si existe userid trata de obtener la información de usuario de la base de datos de moodle
        $this->id = ($id == null) ? UUID::v4() : $id;
    }
       
    public function setId () {
        $this->id = UUID.v4;
        return $this;
    }
    
    public function getId () {
        return $this->id;
    }
    
    public function setActor ($actor) {
        $this->actor = $actor;
        return $this;
    }
    
    public function getActor () {
        return $this->actor;
    }
    
    public function setVerb ($verb) {
        $this->verb = $verb;
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
    
    public function setAuthority () {
         $this->authority = $authority;
         return $this;
     }
     
     public function getAuthority (){
         return $this->authority;
     }
    
     public function expose () {
        $obj = new \stdClass();
        if ($this->id != null) 
            $obj->id = $this->id;
        
        if ( $actor = $this->actor )
            $obj->actor = $actor->expose(); 
        if (HelperXapi::isEmptyObject($obj->actor)) return null;
        
        if ( $verb = $this->verb )
            $obj->verb = $verb->expose(); 
        if (HelperXapi::isEmptyObject($obj->verb)) return null;
        
        if ( $object = $this->object )
            $obj->object = $object->expose(); 
        if (HelperXapi::isEmptyObject($obj->object)) return null;
        
        if ( $result = $this->result ){
            $temp = $result->expose();
            if (HelperXapi::isEmptyObject($result) == false)
                $obj->result = $temp;
        }
        
        if ( $context = $this->context ){
            $temp = $context->expose();
            if (HelperXapi::isEmptyObject($context) == false)
                $obj->context = $temp;
        }
        
        
        if ( $timestamp = $this->timestamp )
            $obj->timestamp = $timestamp;
        
        if ( $authority = $this->authority ){
            $temp = $authority->expose();
            if (HelperXapi::isEmptyObject($authority) == false)
                $obj->authority = $temp;
        }
        
        $obj->version = '1.0.0';
        return $obj;
    }
}