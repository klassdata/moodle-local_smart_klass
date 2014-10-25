<?php
namespace SmartKlass\xAPI;

/**
 * LogCollector Class
 *
 * @package    local_smart_klass
 * @copyright  KlassData <kttp://www.klassdata.com>
 * @author     Oscar Ruesga <oscar@klassdata.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class LogCollector extends Collector {
    
    const MAX_REGS = 6000;
    
    public function collectData(){
        global $DB;
        
        $data = $this->dataprovider->getLog($this); 
        
        return (empty($data)) ? null : $data;
        
    }
    
    public function prepareStatement(StatementRequest $xAPI_statement, $object){
        //SetActor
        $xAPI_statement->setActor($object->userid);
        
        //SetVerb
        $verb = $this->dataprovider->getVerb($object->modname, $object->action);
        if ($verb == null) {
            $a = new \stdClass();
            $a->module = $object->modname;
            $a->action = $object->action;
            $error = $this->dataprovider->getLanguageString('error_lo_verb_from_log', 'local_smart_klass', $a);
            $this->setLastError($error);
            return null;
        }
        $xAPI_statement->setVerb($verb);
        
        //SetObject
        if ($object->modname == 'course'){
            $activity = new Activity($this->dataprovider->getCourseId($object->course));
        } else {
            $activity = new Activity($this->dataprovider->getActivityId($object->modname, $object->activityid));
            
        }
        $activity->setDefinition( new ActivityDefinition($this->dataprovider->getActivityType($object->modname)) );
        $xAPI_statement->setObject($activity);
        
        //SetResult
        
        //SetContext
        $xAPI_statement->setContext('contextActivities',  array('parent'=>$this->dataprovider->getCourseId($object->course)) );
        if ($object->modname != 'course')
            $xAPI_statement->setContext('contextActivities',  array('grouping'=>$this->dataprovider->getModuleId($object->course, $object->moduleid)) );
       
        $role_extension = new Extension(
                                            'http://xapi.klassdata.com/extensions/role',
                                            $this->dataprovider->getRole($object->userid, $object->course)
                                            );
        $xAPI_statement->setContext('extension',  $role_extension );
        
        $instructors = $this->getInstructors($object->course);
        if ( !empty($instructors) ) $xAPI_statement->setContext('instructor',  $instructors );
           
        //SetTimeStamp
        $xAPI_statement->setTimestamp($object->time);
        
        return $xAPI_statement;
    }
}