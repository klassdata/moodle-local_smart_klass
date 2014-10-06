<?php
namespace Klap\xAPI;

/**
 * CourseCreateCollector Class
 *
 * @package    local_klap
 * @copyright  Klap <kttp://www.klaptek.com>
 * @author     Oscar <oscar@klaptek.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class CourseCreateCollector extends Collector {
    
    const MAX_REGS = 1000;
    
    public function collectData(){
        global $DB;
        
        $data = $this->dataprovider->getCourses($this); 
      
        return (empty($data)) ? null : $data;
        
    }
    
    
    public function prepareStatement(StatementRequest $xAPI_statement, $object){
        //SetActor
        $manager = $this->dataprovider->getManager();
        $xAPI_statement->setActor($manager->id);
        
        //SetVerb
        $xAPI_statement->setVerb('created');
        
        //SetObject
        $courseinfo = new \stdClass();
        $courseinfo->id = $this->dataprovider->getCourseId($object->id);
        $courseinfo->name = $object->fullname;
        $courseinfo->modified = date("c", $object->timemodified);
        
        if (!empty($object->startdate))
            $courseinfo->startdate = date("c", $object->startdate);
        
        
       $modules = unserialize($object->sectioncache);
       $activities = unserialize($object->modinfo);
       
       
       $courseinfo->modules = array();
       
       foreach ( $modules as $module){
           $obj = new \stdClass();
           $obj->id = $this->dataprovider->getModuleId($object->id, $module->id);
           if ( isset($module->name) )
                $obj->name = $module->name;
           if ( isset($module->summary) )
                $obj->summary = strip_tags($module->summary);
           
           $obj->activities = array();
           if ( count($activities) > 0 ){
                foreach ($activities as $activity){
                    if ($activity->sectionid == $module->id){
                        $act = new \stdClass();
                        $act->id = $this->dataprovider->getActivityId($activity->mod, $activity->id);
                        if ( isset($activity->name) )
                             $act->name = $activity->name;
                        if ( isset($activity->added) )
                             $act->creationdate = $activity->added;

                        $obj->activities[$activity->id] = $act;
                    }
                }
           }
           
           $courseinfo->modules[$module->id] = $obj;
           
       }
       
       
       

       $activity = new Activity($this->dataprovider->getCourseId($object->id));
       
       $activity_definition = new ActivityDefinition('course');
       $activity_definition->addExtension( new Extension(
                                                            'http://l-miner.klaptek.com/xapi/extensions/course-info',
                                                            $courseinfo  
                                                          ));
       $activity->setDefinition($activity_definition);
       $xAPI_statement->setObject($activity);
        
        //SetResult
        
        //SetContext
        $instructors = $this->getInstructors($object->id);
        if ( !empty($instructors) ) $xAPI_statement->setContext('instructor',  $instructors );
        
        //SetTimeStamp
        $xAPI_statement->setTimestamp($object->timecreated);
        
        return $xAPI_statement;
    }
    
    public function getMaxId() {
        return $this->dataprovider->getMaxId('course'); 
    }
}