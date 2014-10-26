<?php
namespace SmartKlass\xAPI;

/**
 * CourseInitializedCollector Class
 *
 * @package    local_smart_klass
 * @copyright  KlassData <kttp://www.klassdata.com>
 * @author     Oscar Ruesga <oscar@klassdata.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class CourseInitializedCollector extends Collector {
    
    const MAX_REGS = 1000;
    
    public function collectData(){
        global $DB;
        
        $data = $this->dataprovider->getCourseInitDate($this); 
        
        return (empty($data)) ? null : $data;
        
    }
    
    
    public function prepareStatement(StatementRequest $xAPI_statement, $object){
        if (!empty($object->time) && !empty($object->timeenrolled)) {
            //SetActor
            $xAPI_statement->setActor($object->userid);

            //SetVerb
            $xAPI_statement->setVerb('initialized');

            //SetObject
            $activity = new Activity($this->dataprovider->getCourseId($object->course));

            $activity_definition = new ActivityDefinition('course');

           $activity->setDefinition($activity_definition);
           $xAPI_statement->setObject($activity);

            //SetResult

            //SetContext
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
        } else {
            $regid = $this->dataprovider->get_reg_id ($object->id, get_class($this));
            $this->addReproccessIds($regid->table, $object->id); 
            $agent = new Agent($object->userid);
            $actor = json_decode($agent);
            $a = new \stdClass();
            $a->user = $actor->mbox;
            $a->course = $this->dataprovider->getCourseId($object->course);
            $error = $this->dataprovider->getLanguageString('user_no_init_course', 'local_smart_klass', $a);
            $this->setLastError($error);
            return null;
        }
        
    }
}