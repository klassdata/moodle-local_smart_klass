<?php
namespace SmartKlass\xAPI;

/**
 * xAPI Result Class
 *
 * @package    local_smart_klass
 * @copyright  KlassData <kttp://www.klassdata.com>
 * @author     Oscar Ruesga <oscar@klassdata.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class Result extends xApiObject {
    
    /**
     * The score of the agent in relation to the success or quality of the experience.
     * Is an optional numeric field that represents the outcome of a graded Activity achieved by an Agent. 
     * @access private
     * @var Object 
     */
    private $score = null;

    /**
     * Indicates whether or not the attempt on the Activity was successful.
     * @access private
     * @var bool 
     */
    private $success = null;

    /**
     * Indicates whether or not the Activity was completed.
     * @access private
     * @var bool 
     */
    private $completion = null;

    /**
     * A response appropriately formatted for the given Activity.
     * @access private
     * @var string 
     */
    private $response = null;
    
    /**
     * Period of time over which the Statement occurred.
     * @access private
     * @var string. Formatted according to ISO 8601 with a precision of 0.01 seconds. 
     */
    private $duration = null;
    
     /**
     * A map of other properties as needed
     * Extensions are defined by a map. The keys of that map MUST be URLs, and the values MAY be any JSON value or data structure.
     * The meaning and structure of extension values under a URL key are defined by the person who coined the URL,
     * who SHOULD be the owner of the URL, or have permission from the owner.
     * The owner of the URL SHOULD make a human-readable description of the intended meaning of the extension supported by the URL accessible at the URL.
     * A learning record store MUST NOT reject an Experience API Statement based on the values of the extensions map.
     * Extensions are available as part of Activity Definitions, as part of statement context, or as part of some statement result. 
     * In each case, they're intended to provide a natural way to extend those elements for some specialized use. 
     * The contents of these extensions might be something valuable to just one application, or it might be a convention used by an entire community of practice.
     * Extensions should logically relate to the part of the statement where they are present. 
     * Extensions in Statement context should provide context to the core experience, while those in the result should provide elements related to some outcome. 
     * @access private
     * @var Object 
     */
    private $extensions = array();
    
    public function __construct() {}
    
    public function setScore (Score $score) {              
         $this->score = $score;
         return $this;
    }
    
    public function getScore () {
        return $this->score;
    }
    
    public function setSuccess ($success=false) {
        $this->success = ($success == true); 
        return $this;
    }
    
    public function getSuccess (){
        return $this->success;
    }
    
    public function setCompletion ($completion=false){
        $this->completion = ($completion == true); 
        return $this;
    }
    
    public function getCompletion (){
        return $this->success;
    }
    
    public function setResponse ($response=null) {
        $this->response = $response;
        return $this;
    }
    
    public function getResponse () {
        return $this->response;
    }
    
    public function setDuration ($duration, $format='unix'){
        switch ($format){
            case 'seconds':
                $this->duration = date("c", $duration);
                break;
            case 'iso-8601':
               $this->duration = $duration;
                break;
            case 'unix':
            default:
                $this->duration = HelperXapi::time_to_iso8601_duration($duration);
                break;
            
        }
        return $this;
    }
    
    public function getDuration () {
        return $this->duration;
    }
    
    public function addExtension ($extension=null){
        if ( $extension instanceof Extension){
            $extkey = filter_var( $extension->getKey(), FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED);
            if ( !empty($extkey) ) 
                $this->extensions[$extension->getKey()] = $extension;
        } 
        return $this;
    }
    
    public function getExtension ($key=null) {
        $returnvalue=null;

        
        while ($extension = current($this->extensions)) {
            $k = key($extension);
            if ( $k == $key) {
                $returnvalue = $extension;
                break;
            }
            next($this->extensions);
        }

        return $returnvalue;
    }
    
    public function getExtensions () {
        return $this->extensions;
    }

    public function expose () {
        $obj = new \stdClass();
        
        if ( $score = $this->score ){
            $temp = $score->expose();
            if (HelperXapi::isEmptyObject($score) == false)
                $obj->score = $temp;
        }
        
        if ($this->success !== null && is_bool($this->success) === true )
            $obj->success = $this->success;
        
        if ($this->completion !== null && is_bool($this->completion) === true )
            $obj->completion = $this->completion;

        if ($this->response !== null && is_string($this->response) === true)
            $obj->response = $this->response;
        
        if ($this->duration !== null && is_string($this->duration) === true)
            $obj->duration = $this->duration;
        
        if ( count($this->extensions)>0 ) {
            $obj->extensions = null;
            foreach ($this->extensions as $extension) {
                if (empty($obj->extensions)) 
                    $obj->extensions = array();
                $temp = $extension->expose();
                if (HelperXapi::isEmptyObject($temp) == false)
                    $obj->extensions[] = $temp;
            }
        }
        return $obj;
    }
}