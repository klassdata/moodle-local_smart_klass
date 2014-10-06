<?php
namespace Klap\xAPI;

/**
 * GradeCollector Class
 *
 * @package    local_klap
 * @copyright  Klap <kttp://www.klaptek.com>
 * @author     Oscar <oscar@klaptek.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class GradeCollector extends Collector {
    
    const MAX_REGS = 1000;
    
    public function collectData($data=null){
        global $DB;
        
        if ($data === null || $data )
            $data = $this->dataprovider->getGrades($this); 
        return (empty($data)) ? null : $data;
        
    }
    
    public function prepareStatement(StatementRequest $xAPI_statement, $object){
        //SetActor
        $xAPI_statement->setActor($object->userid);
        
        //SetVerb
        $xAPI_statement->setVerb('scored');
        
        //SetObject
        $activity = new Activity($this->dataprovider->getActivityId($object->activitytype, $object->activityid));
        $activity->setDefinition( new ActivityDefinition($this->dataprovider->getActivityType($object->activitytype)) );
        $xAPI_statement->setObject($activity);
        
        //SetResult
        $xAPI_statement->setResult('score',new Score( floatval($object->score), floatval($object->minscore), floatval($object->maxscore) ));
        
        //SetContext
        $xAPI_statement->setContext('contextActivities',  array('parent'=>$this->dataprovider->getCourseId($object->courseid)) );
        $xAPI_statement->setContext('contextActivities',  array('grouping'=>$this->dataprovider->getModuleId($object->courseid, $object->moduleid)) );
        
        $role_extension = new Extension(
                                            'http://l-miner.klaptek.com/xapi/extensions/role',
                                            $this->dataprovider->getRole($object->userid, $object->courseid)
                                            );
        $xAPI_statement->setContext('extension',  $role_extension );
        
        //SetTimeStamp
        $xAPI_statement->setTimestamp($object->time);
        
        //$this->registerAdiccionalCollector ('ModuleGrade', $object);
        //$this->registerAdiccionalCollector ('CourseGrade', $object);
        
        return $xAPI_statement;
    } 
    
    public function getMaxId() {
        return $this->dataprovider->getMaxId('grade_grades_history'); 
    }
}