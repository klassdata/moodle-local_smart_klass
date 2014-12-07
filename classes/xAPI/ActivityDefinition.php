<?php
namespace SmartKlass\xAPI;

/**
 * xAPI ActivityDefinition Class
 *
 * @package    local_smart_klass
 * @copyright  KlassData <kttp://www.klassdata.com>
 * @author     Oscar Ruesga <oscar@klassdata.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


class ActivityDefinition extends xApiObject {
    
    /**
     * The human readable/visual name of the Activity. Recommended.
     * @access private
     * @var Language MAP 
     */
    private $name = null;

    /**
     * A description of the Activity. Recommended.                                                                       
     * @var Language Map 
     */
    private $description = null;
    
    /**
     * The type of Activity.                                                                             
     * @access private
     * @var URL 
     */
    private $type = null;
    
    /**
     * SHOULD resolve to a document human-readable information about the Activity, which MAY include a way to 'launch' the Activity. Optional.
     * @access private
     * @var URL 
     */
    private $moreInfo = null;
    
   
    /**
     * As in "cmi.interactions.n.type" as defined in the SCORM 2004 4th Edition Run-Time Environment.                                                                           
     * @access private
     * @var string 
     */
    private $interactionType = null;

    /**
     * Corresponds to "cmi.interactions.n.correct_responses.n.pattern" as defined in the SCORM 2004 4th Edition Run-Time Environment, 
     * where the final n is the index of the array.                                                                           
     * @access private
     * @var array of strings 
     */
    private $correctResponsesPattern = null;

    /**
     * Supported by: choice and sequencing interaction type                                                                          
     * @access private
     * @var string 
     */
    private $choices = null;

    /**
     * Supported by: Likert interaction type                                                                          
     * @access private
     * @var string 
     */
    private $scale = null;

    /**
     * Supported by: Matching interaction type                                                                          
     * @access private
     * @var string 
     */
    private $source = null;

    /**
     * Supported by: Matching interaction type                                                                          
     * @access private
     * @var string 
     */
    private $target = null;

    /**
     * Supported by: Performance interaction type                                                                          
     * @access private
     * @var string 
     */
    private $steps = null;
    
    
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
     * For Activities, they should provide additional information that helps define an Activity within some custom application or community.                                                                             
     * @access private
     * @var Object 
     */
    private $extensions = array();
    
    
    private $elementsInteractionsTypes = array (    'true-false' => array(), 
                                                    'choice' => array('choices'), 
                                                    'fill-in' => array(), 
                                                    'likert' => array('scale'), 
                                                    'matching' => array('source','target'), 
                                                    'performance' => array('steps'), 
                                                    'sequencing' => array('choices'), 
                                                    'numeric' => array(),
                                                    'other' => array()
                                                );
    
    

    private $activity = null;
   
    public function __construct($id=null) {
        $this->setId($id);
    }
    
    public function setId ($activityid) {
        $activityid = ucfirst($activityid);
        $verb_definition = dirname(__FILE__) . "/Activities/$activityid.php";
		if(file_exists($verb_definition)){
			$activityid = 'SmartKlass\\xAPI\\' . $activityid;
            $this->activity = new $activityid();     
        } 
        $this->name = $this->getName();
        $this->type = $this->getType();
        //$this->description = $this->getDescription();
        //$this->moreInfo = $this->getMoreInfo();
        return $this;
     }
     
     public function getType () {
        return $this->activity->getType();
    }
    
    public function getName () {
        return $this->activity->getName();
    }
    
    public function getDescription () {
        return $this->activity->getDescription();
    }
    
    public function getMoreInfo () {
        return $this->activity->getMoreInfo();
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
    
    public function addExtension ($extension=null){
        if ( $extension instanceof Extension){
            $extkey = filter_var( $extension->getKey(), FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED) ;
            if ( !empty($extkey) ) 
                $this->extensions[] = $extension;
        }
        return $this;
    }
        
    
    
    //Campos de interacciones
    public function setInteractionType ($interactiontype){
        if ( in_array($interactiontype, array_keys($this->elementsInteractionsTypes) ) === false ) return;       
        $this->interactionType = $interactiontype;
    }
    
    
    public function getInteractionType () {
        return $this->interactionType;
    }
    
    public function addCorrectResponsePattern ($pattern){
        if ($this->correctResponsesPattern == null) $this->correctResponsesPattern=array();
        $this->correctResponsesPattern[] = $pattern;
    }
    
    public function getCorrectResponsePattern (){
        return $this->correctResponsesPattern;
    }
    
    public function setChoices ($choices){
        if ($this->choices == null) $this->choices=array();
        
        $obj = new InteractionComponent($id, $description, $lang);
        $temp = $obj->expose();
        if (HelperXapi::isEmptyObject($obj) == false)
            $this->choices[] = $obj;
    }

    public function getChoices () {
        return $this->choices;
    }

    public function addScale ($scale){
        if ($this->scale == null) $this->scale=array();
        
        $obj = new InteractionComponent($id, $description, $lang);
        $temp = $obj->expose();
        if (HelperXapi::isEmptyObject($obj) == false)
            $this->scale[] = $obj;
    }
    
    public function getScale () {
        return $this->scale;
    }
    
    public function addSource ($source){
        if ($this->source == null) $this->source=array();
        
        $obj = new InteractionComponent($id, $description, $lang);
        $temp = $obj->expose();
        if (HelperXapi::isEmptyObject($obj) == false)
            $this->source[] = $obj;
    }
    
    public function getSource () {
        return $this->source;
    }
    
    public function addTarget ($target){
        if ($this->target == null) $this->target=array();
        
        $obj = new InteractionComponent($id, $description, $lang);
        $temp = $obj->expose();
        if (HelperXapi::isEmptyObject($obj) == false)
            $this->target[] = $obj;
    }
    
    public function getTarget () {
        return $this->target;
    }
    
    public function addStep ($id, $description=null, $lang='es'){
        if ($this->steps == null) $this->steps=array();
        
        $obj = new InteractionComponent($id, $description, $lang);
        $temp = $obj->expose();
        if (HelperXapi::isEmptyObject($obj) == false)
            $this->steps[] = $obj;      
    }
    
    public function getSteps () {
        return $this->steps;
    }
    
    public function expose () {
        $obj = new \stdClass();
        
        if ($this->name !== null )
            $obj->name = $this->name;
        
        if ($this->description !== null )
            $obj->description = $this->description;
       
        if ($this->type !== null)
            $obj->type = $this->type;
        
        if ($this->moreInfo !== null )
            $obj->moreInfo = $this->moreInfo;
        
        if ($this->interactionType !== null )
            $obj->interactionType = $this->interactionType;
        
        if ( count($this->correctResponsesPattern)>0 )
            $obj->correctResponsesPattern = $this->correctResponsesPattern;
        
        if ( count($this->choices)>0 && in_array('choices', $this->elementsInteractionsTypes[$this->interactionType] ) )
            $obj->choices = $this->choices;
        
        if ( count($this->scale)>0 && in_array('scale', $this->elementsInteractionsTypes[$this->interactionType] ))
            $obj->scale = $this->scale;
        
        if ( count($this->source)>0 && in_array('source', $this->elementsInteractionsTypes[$this->interactionType] ))
            $obj->source = $this->source;
        
        if ( count($this->target)>0 && in_array('target', $this->elementsInteractionsTypes[$this->interactionType] ))
            $obj->target = $this->target;
        
        if ( count($this->steps)>0 && in_array('steps', $this->elementsInteractionsTypes[$this->interactionType] ))
            $obj->steps = $this->steps;
        
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