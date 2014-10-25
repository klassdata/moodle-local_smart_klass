<?php
namespace SmartKlass\xAPI;

/**
 * xAPI Context Class
 *
 * @package    local_smart_klass
 * @copyright  KlassData <kttp://www.klassdata.com>
 * @author     Oscar Ruesga <oscar@klassdata.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class Context extends xApiObject{
    
    /**
     * The registration that the Statement is associated with.
     * @access private
     * @var UUID 
     */
    private $registration = null;
       
    /**
     * Instructor that the Statement relates to, if not included as the Actor of the statement.
     * @access private
     * @var Agent or Group 
     */
    private $instructor = null;
    
    /**
     * Team that this Statement relates to, if not included as the Actor of the statement.
     * @access private
     * @var Group 
     */
    private $team = null;
    
    /**
     * A map of the types of learning activity context that this Statement is related to. 
     * Valid context types are: "parent", "grouping", "category" and "other".
     * @access private
     * @var contextActivities Object 
     */
    private $contextActivities = null;
    
    /**
     * Revision of the learning activity associated with this Statement. Format is free.
     * @access private
     * @var string 
     */
    private $revision = null;
    
    /**
     * Platform used in the experience of this learning activity.
     * @access private
     * @var string 
     */
    private $platform = null;
    
    
    /**
     * Code representing the language in which the experience being recorded in this Statement (mainly) occurred in, 
     * if applicable and known. (String as defined in RFC 5646 )
     * @access private
     * @var string 
     */
    private $language = null;
   
    /**
     * Another Statement which should be considered as context for this Statement.
     * @access private. Statement Reference (#stmtref)
     * @var string 
     */
    private $statement = null;
    
    /**
     * A map of any other domain-specific context relevant to this Statement. 
     * For example, in a flight simulator altitude, airspeed, wind, attitude, 
     * GPS coordinates might all be relevant
     * @access private
     * @var array 
     */
    private $extensions = null;


    public function __construct() {
        $this->contextActivities = new ContextActivities();
    }
    
    public function setRegistration ($registration) {
        if ( UUID::is_valid($registration) )
            $this->registration = $registration;
        return $this;
    }
    
    public function getRegistration () {
        return $this->registration;
    }
    
    public function setInstructor ($instructor) {
        if ( $instructor instanceof Agent || $instructor instanceof Group )
            $this->instructor = $instructor;
        return $this;
    }
    
    public function getInstructor () {
        return $this->instructor;
    }
    
    public function setTeam ($team) {
        if ( $team instanceof Group )
            $this->team = $team;
        return $this;
    }
    
    public function getTeam () {
        return $this->team;
    }
    
    public function setPlatform ($platform) {
        $this->platform = $platform;
        return $this;
    }
    
    public function getPlatform () {
        return $this->platform;
    }
    
    public function addContextActivities ( $value ) {
        foreach ($value as $k=>$v) {
            $this->contextActivities->addElement($k, $v); 
        }
        return $this;
    }
    
    public function getContextActivities ( $id ) {
        return $this->contextActivities[$id];
    }
    
    public function setRevision ($revision) {
        $this->revision = $revision;
        return $this;
    }
    
    public function getRevision () {
        return $this->revision;
    }
    
    public function setStatement ($statement) {
        $this->statement = $statement;
        return $this;
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

    
    public function expose () {
        $obj = new \stdClass();
        
        if ( $registration = $this->registration ) $obj->registration = $registration;

        if ( $instructor = $this->instructor ){
            $temp = $instructor->expose();
            if (HelperXapi::isEmptyObject($instructor) == false)
                $obj->instructor = $temp;
        }
        
        if ( $team = $this->team ){
            $temp = $team->expose();
            if (HelperXapi::isEmptyObject($team) == false)
                $obj->team = $temp;
        }
          
        if ( $contextActivities = $this->contextActivities ){
            $temp = $contextActivities->expose();
            if (HelperXapi::isEmptyObject($temp) == false)
                $obj->contextActivities = $temp;
        }
        
        
        if ( $revision = $this->revision ) $obj->revision = $revision;
        
        if ( $platform = $this->platform ) $obj->platform = $platform;
        
        if ( $language = $this->language ) $obj->language = $language;
        
        if ( $statement = $this->statement ) $obj->statement = $statement;
        
        if ( count($this->extensions)>0 ) {
            $obj->extensions = null;
            foreach ($this->extensions as $extension) {
                if (empty($obj->extensions)) 
                    $obj->extensions = array();
                $temp = $extension->expose();
                if (HelperXapi::isEmptyObject($extension) == false)
                    $obj->extensions[] = $temp;
            }
        }
         return $obj;
    }
}


/**
 * xAPI Parent Context Activity Class
 * A map of the types of learning activity context that this Statement is related to.
 * Many Statements do not just involve one Object Activity that is the focus, but relate to other contextually relevant Activities. 
 * "Context activities" allow for these related Activities to be represented in a structured manner.
 *
 * @package    local_smart_klass
 * @copyright  KlassData <kttp://www.klassdata.com>
 * @author     Oscar Ruesga <oscar@klassdata.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class ContextActivities extends xApiObject{
    
    /**
     * An Activity with a direct relation to the activity which is the Object of the Statement. In almost all cases there is only one sensible parent or none, not multiple.
     * For example: a Statement about a quiz question would have the quiz as its parent Activity.
     * @access private
     * @var string 
     */
    private $parent = array();
    
    /**
     * An Activity with an indirect relation to the activity which is the Object of the Statement. For example: a course that is part of a qualification. 
     * The course has several classes. The course relates to a class as the parent, the qualification relates to the class as the grouping.
     * @access private
     * @var string 
     */
    private $grouping = array();
    
    /**
     * An Activity used to categorize the Statement. "Tags" would be a synonym. Category SHOULD be used to indicate a "profile" of xAPI behaviors, as well as other categorizations.
     * For example: Anna attempts a biology exam, and the Statement is tracked using the CMI-5 profile. 
     * The Statement's Activity refers to the exam, and the category is the CMI-5 profile.
     * @access private
     * @var string 
     */
    private $category = array();
    
    /**
     * A context Activity that doesn't fit one of the other fields. For example: Anna studies a textbook for a biology exam. 
     * The Statement's Activity refers to the textbook, and the exam is a context Activity of type "other".
     * @access private
     * @var string 
     */
    private $other = array();
    
    
    
    public function _constructor () {
        
    }

    public function addElement ($type, $id) {
        $contextactivitiestypes = array ('parent', 'grouping', 'category', 'other');
        if ( in_array($type, $contextactivitiestypes) ) {
            $obj = new \stdClass();
            $obj->id = $id;
            eval('$this->'.$type.'[] = $obj;');
            
        }
    }
    
    public function removeElement ( $type, $id ) {
        $contextactivitiestypes = array ('parent', 'grouping', 'category', 'other');
        if ( in_array($type, $contextactivitiestypes) ) {
            $obj = new \stdClass();
            $obj->id = $id;
            $arr = $this->$type;
            if (($key = array_search($obj, $arr)) !== false)
                unset($arr[$key]);
        }
    }
    
    public function expose () {
        $obj = new \stdClass();
        
        if ( count($this->parent)>0 ) {
            $obj->parent = null;
            foreach ($this->parent as $parent) {
                if (empty($obj->parent)) 
                    $obj->parent = array();
                if (HelperXapi::isEmptyObject($parent) == false)
                    $obj->parent[] = $parent;
            }
        }
        
        if ( count($this->grouping)>0 ) {
            $obj->grouping = null;
            foreach ($this->grouping as $grouping) {
                if (empty($obj->grouping)) 
                    $obj->grouping = array();
                if (HelperXapi::isEmptyObject($grouping) == false)
                    $obj->grouping[] = $grouping;
            }
        }
        
        if ( count($this->category)>0 ) {
            $obj->category = null;
            foreach ($this->category as $category) {
                if (empty($obj->category)) 
                    $obj->category = array();
                if (HelperXapi::isEmptyObject($category) == false)
                    $obj->category[] = $category;
            }
        }
        
        if ( count($this->other)>0 ) {
            $obj->other = null;
            foreach ($this->other as $other) {
                if (empty($obj->other)) 
                    $obj->other = array();
                if (HelperXapi::isEmptyObject($other) == false)
                    $obj->other[] = $other;
            }
        }
        
        return (HelperXapi::isEmptyObject($obj)) ? null : $obj;
 
    }
}