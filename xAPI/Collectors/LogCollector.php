<?php
namespace Klap\xAPI;

/**
 * LogCollector Class
 *
 * @package    local_klap
 * @copyright  Klap <kttp://www.klaptek.com>
 * @author     Oscar <oscar@klaptek.com>
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
        $verb = $this->getVerb($object->modname, $object->action);
        if ($verb == null) return null;
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
                                            'http://l-miner.klaptek.com/xapi/extensions/role',
                                            $this->dataprovider->getRole($object->userid, $object->course)
                                            );
        $xAPI_statement->setContext('extension',  $role_extension );
        
        $instructors = $this->getInstructors($object->course);
        if ( !empty($instructors) ) $xAPI_statement->setContext('instructor',  $instructors );
           
        //SetTimeStamp
        $xAPI_statement->setTimestamp($object->time);
        
        return $xAPI_statement;
    }
    
    private function getVerb ($module, $action) {
        switch ($module){
            case 'assign':
                switch ($action){
                    case 'submit': return 'answered';
                    case 'view': return 'attempted';
                    default: return null;
                }        
            case 'chat':
                switch ($action){
                    case 'talk': return 'interacted';
                    case 'view': return 'attempted';
                    default: return null;
                }
            case 'course':
                switch ($action){
                    case 'view': return 'attempted';
                    default: return null;
                }
            case 'feedback':
                switch ($action){
                    case 'startcomplete': return 'attempted';
                    default: return null;
                }
            case 'folder':
                switch ($action){
                    case 'view': return 'attempted';
                    default: return null;
                }
            case 'forum':
                switch ($action){
                    case 'add post': return 'commented';
                    case 'add discussion': return 'created';
                    case 'view forum': return 'attempted';
                    default: return null;
                }
                break;
            
            case 'page':
                switch ($action){
                    case 'view': return 'attempted';
                    default: return null;
                }
                break;
            
            case 'quiz':
                switch ($action){
                    case 'attempt': return 'attempted';
                    case 'close attempt': return 'suspended';
                    case 'continue attempt': return 'resumed';
                    case 'view': return 'launched';
                    case 'preview': return 'experienced';
                    case 'view summary': return 'experienced';
                    default: return null;
                }
                break;
            
            case 'resource':
                switch ($action){
                    case 'view': return 'attempted';
                    default: return null;
                }
                break;
            
            case 'scorm':
                switch ($action){
                    case 'launch': return 'attempted';
                    default: return null;
                }
                break;
            
            case 'url':
                switch ($action){
                    case 'view': return 'attempted';
                    default: return null;
                }
                break;
        }
    }
    
    public function getMaxId() {
        return $this->dataprovider->getMaxId('log'); 
    }
}