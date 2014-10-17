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
        
        
       $courseinfo->modules = $object->modules;

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