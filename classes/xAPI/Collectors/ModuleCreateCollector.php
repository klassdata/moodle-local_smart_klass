<?php
namespace SmartKlass\xAPI;

/**
 * ModuleCreateCollector Class
 *
 * @package    local_smart_klass
 * @copyright  KlassData <kttp://www.klassdata.com>
 * @author     Oscar Ruesga <oscar@klassdata.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class ModuleCreateCollector extends Collector {
    
    const MAX_REGS = 1000;
    
    public function collectData(){
       global $DB;
        
       $data = $this->dataprovider->getModules($this); 
       return (empty($data)) ? null : $data;    
    }
    
    
    public function prepareStatement(StatementRequest $xAPI_statement, $object){
        //SetActor
        $manager = $this->dataprovider->getManager();
        $xAPI_statement->setActor($manager->id);
        
        //SetVerb
        $xAPI_statement->setVerb('created');
        
        //SetObject
        $moduleinfo = new \stdClass();
        $moduleinfo->id = $this->dataprovider->getModuleId($object->course, $object->id);
        if ( isset($object->name) )
            $moduleinfo->name = $object->name;
        if ( isset($object->summary) )
            $moduleinfo->summary = strip_tags($object->summary);
        if ( isset($object->timemodified) )
            $moduleinfo->modified = date("c", $object->timemodified);

        if ( isset($object->activities) ){
            foreach ($object->activities as $activity){
                    $act = new \stdClass();
                    $act->id = $this->dataprovider->getActivityId($activity->mod, $activity->id);
                    if ( isset($activity->name) )
                        $act->name = $activity->name;
                    if ( isset($activity->added) )
                        $act->creationdate = $activity->added;

                    $moduleinfo->activities[] = $act;
            }
        }

       $activity = new Activity($this->dataprovider->getModuleId($object->course, $object->id));
       
       $activity_definition = new ActivityDefinition('module');
       $activity_definition->addExtension( new Extension(
                                                            'http://xapi.klassdata.com/extensions/module-info',
                                                            $moduleinfo  
                                                          ));
       $activity->setDefinition($activity_definition);
       $xAPI_statement->setObject($activity);
        
        //SetResult
        
        //SetContext
        $xAPI_statement->setContext('contextActivities',  array('parent'=>$this->dataprovider->getCourseId($object->course)) );
        
        $instructors = $this->getInstructors($object->course);
        if ( !empty($instructors) ) $xAPI_statement->setContext('instructor',  $instructors );
        
        //SetTimeStamp
        $xAPI_statement->setTimestamp($object->timecreated);
        
        return $xAPI_statement;
    }
}