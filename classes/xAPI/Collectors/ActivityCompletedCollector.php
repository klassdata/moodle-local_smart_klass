<?php
namespace SmartKlass\xAPI;

/**
 * ActivityCompletedCollector Class
 *
 * @package    local_smart_klass
 * @copyright  KlassData <kttp://www.klassdata.com>
 * @author     Oscar Ruesga <oscar@klassdata.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class ActivityCompletedCollector extends Collector {
    
    const MAX_REGS = 2000;
    
    public function collectData(){
        global $DB;
        
        $data = $this->dataprovider->getActivitiesCompletion($this); 
        
        return (empty($data)) ? null : $data;
        
    }
    
    
    public function prepareStatement(StatementRequest $xAPI_statement, $object){
        if ($object->completionstate == '1'  && !empty($object->timemodified)) {
            //SetActor
            $xAPI_statement->setActor($object->userid);

            //SetVerb
            $xAPI_statement->setVerb('completed');

            //SetObject
            $activity = new Activity($this->dataprovider->getActivityId($object->mod, $object->activityid));

            $activity_definition = new ActivityDefinition($this->dataprovider->getActivityType($object->mod));

           $activity->setDefinition($activity_definition);
           $xAPI_statement->setObject($activity);

            //SetResult    
           $xAPI_statement->setResult('completion', true);  
           
           //SetContext
           $xAPI_statement->setContext('contextActivities',  array('parent'=>$this->dataprovider->getCourseId($object->course)) );
           $xAPI_statement->setContext('contextActivities',  array('grouping'=>$this->dataprovider->getModuleId($object->course, $object->section)) );
        
           $role_extension = new Extension(
                                            'http://xapi.klassdata.com/extensions/role',
                                            $this->dataprovider->getRole($object->userid, $object->course)
                                            );
           $xAPI_statement->setContext('extension',  $role_extension );
           
           $instructors = $this->getInstructors($object->course);
           if ( !empty($instructors) ) $xAPI_statement->setContext('instructor',  $instructors );
        
           //SetTimeStamp
           $xAPI_statement->setTimestamp($object->timemodified);

           return $xAPI_statement;
        } else {
            $regid = $this->dataprovider->get_reg_id ($object->id, get_class($this));
            $this->addReproccessIds($regid->table, $object->id);   

            $agent = new Agent($object->userid);
            $actor = json_decode($agent);
            $a = new \stdClass();
            $a->user = $actor->mbox;
            $a->activity = $this->dataprovider->getActivityId($object->mod, $object->activityid);
            $error = $this->dataprovider->getLanguageString('user_no_completed_activity', 'local_smart_klass', $a);
            $this->setLastError($error);
            
            return null;
        }
    }
 
}