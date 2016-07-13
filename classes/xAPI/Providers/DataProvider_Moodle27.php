<?php
namespace SmartKlass\xAPI;

/**
 * xAPI DataProvider Class
 *
 * @package    local_smart_klass
 * @copyright  KlassData <kttp://www.klassdata.com>
 * @author     Oscar Ruesga <oscar@klassdata.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class DataProvider_Moodle27 extends DataProvider {

    public function __construct() {
    }

    public function getModules (Collector $collector) {
        global $DB;
        
        $table = 'course_sections';
        $reprocess = (count($collector->getReproccessIds($table))>0) ? ' OR id IN (' . implode(',', $collector->getReproccessIds($table)) . ') ' : '';
        $limit = $collector->getMaxRegistrys();
        $limit = ($limit == null) ? 0 : $limit;
        $reg = $DB->get_records_select ('course_sections', 
                                        "(id>?{$reprocess}) AND visible=?", 
                                        array($collector->getLastRegistry($table), 1),
                                        '', 
                                        'id, course, name, summary, section, sequence, visible, availability',
                                        0, $limit
          );

        $sections = array();
        foreach ($reg as $section){
            $sections[] = $this->getModule($section);
        }
        ksort($sections, SORT_NUMERIC);
        return $sections;
    }

    public function getModule ($section) {
        global $DB;
        if ( is_int($section) ) {
            $section = $DB->get_record (    'course_sections', 
                                            array('id'=>$section, 'visible'>1),
                                            'id, course, name, summary, section, sequence, visible, availability'
                                        );
        }
        $obj = new \stdClass();
        $obj->id = $section->id;
        $obj->course = $section->course;
        $obj->name = (empty($section->name)) ? 'Módulo ' . $section->section : $section->name;

        if ( !empty($section->summary) ){
            $obj->summary = strip_tags($section->summary);
        }

        $modulesarr = array();
        $modules = $DB->get_records_sql("
            SELECT cm.id, m.name AS modname, added, availability
              FROM {course_modules} cm
              JOIN {modules} m ON m.id = cm.module
             WHERE cm.course = ?
               AND cm.section = ?
               AND cm.visible = '1' ORDER BY cm.id ASC", array($section->course, $section->id));


        $module_date_creation = null;

        foreach (explode(',', $section->sequence) as $moduleid) {
            if (isset($modules[$moduleid])) {
                $name =  $DB->get_field_sql(
                                            "
                                                SELECT
                                                    m.name
                                                FROM
                                                    {{$modules[$moduleid]->modname}} m
                                                INNER JOIN
                                                    {course_modules} cm
                                                 ON cm.id = {$modules[$moduleid]->id}
                                                AND m.id = cm.instance
                                            "
                                        );
                $module = array(
                                    'id' => $modules[$moduleid]->id, 
                                    'mod' => $modules[$moduleid]->modname,
                                    'name' => $name,
                                    'sectionid' => $section->section,
                                    'added' => $modules[$moduleid]->added
                                );

                $modulesarr[$moduleid] = (object)$module;

                $modulesarr[$moduleid] = (object)$module;

                $module_date_creation = ($module_date_creation == null || $module_date_creation > $modules[$moduleid]->added) ? 
                                            $modules[$moduleid]->added : 
                                            $module_date_creation;
                unset($modules[$moduleid]);
            }
        }

        if (!empty($modules)) { 
            foreach ($modules as $m) {
                $name =  $DB->get_field_sql(
                                            "
                                                SELECT
                                                    m.name
                                                FROM
                                                    {{$m->modname}} m
                                                INNER JOIN
                                                    {course_modules} cm
                                                 ON cm.id = {$m->id}
                                                AND m.id = cm.instance
                                            "
                                        );

                $module = array(
                                    'id' => $m->id,
                                    'mod' => $m->modname,
                                    'name' => $name,
                                    'sectionid' => $section->section,
                                    'added' => $m->added
                                );
                $modulesarr[$m->id] = (object)$module;

                $module_date_creation = ($module_date_creation == null || $module_date_creation > $m->added) ? 
                                            $m->added : 
                                            $module_date_creation;
            }
        }

        if ( count($modulesarr)>0 )
            $obj->activities = $modulesarr;

        if ( empty($module_date_creation) )
           $module_date_creation = $DB->get_field('course', 'timecreated', array('id'=>$section->course) ); 

        $obj->timecreated = $module_date_creation;
        return $obj;
    }
    
    
  
    public function getLog (Collector $collector) {
        global $DB;

        
        $limit = $collector->getMaxRegistrys();
        $limit = ($limit == null) ? 0 : $limit;

        $logmanager = get_log_manager();
        

        $readers = $logmanager->get_readers('core\log\sql_select_reader'); 

        $reg = array();
        foreach ($readers as $reader) {
            $filter = new \stdClass();
            $logreader = $reader;

            $joins = array();
            $params = array();
            if ( $reader instanceof \logstore_legacy\log\store ) {
                $joins[] = "id > " . $collector->getLastRegistry('logstore_standard_log');
                $joins[] = "cmid <> 0";
                $joins[] = 'module IN ("assign", "chat", "course", "feedback", "folder", "forum", "page", "quiz", "resource", "scorm", "url")';             
            } else {
                $joins[] = "id > " . $collector->getLastRegistry('logstore_standard_log');
                // Filter out anonymous actions, this is N/A for legacy log because it never stores them.
                $joins[] = "anonymous = 0";
                $joins[] = "component LIKE 'mod_%'";
            }
            $start = ($start == null) ? 0 : $start;
            $selector = implode(' AND ', $joins);
            
            $logs = $logreader->get_events_select($selector, $params, null,0, $limit);
            
            foreach ($logs as $k => $log){   
                $id =  $reader->get_name() . '/' . $k;
                $data = $log->get_data();
                
                if ($data['contextinstanceid'] == '0') continue;
                $obj = new \stdClass();
                $obj->time = $data['timecreated'];
                $obj->userid = $data['userid'];
                $obj->course = $data['courseid'];
                if ( $reader instanceof \logstore_legacy\log\store ) 
                    list($obj->modname, $obj->action) = explode('_', $data['eventname']);
                else {
                    list($g, $obj->modname) = explode ('_', $data['component']);
                    $obj->action = "{$data['action']} {$data['objecttable']}";
                }
    
                $obj->cmid =$data['contextinstanceid'];

                $d = $DB->get_record('course_modules', array('id'=>$obj->cmid ), 'instance as activityid,section as moduleid');
                $obj->activityid = $d->activityid;
                $obj->moduleid = $d->moduleid;
                $obj->id = $id;
                $reg[$id] = $obj;
            }

        }
        return $reg;
    }
    
    public function getVerb ($module, $action) {
        switch ($module){
            case 'assign':
                switch ($action){
                    case 'submit': return 'answered';
                    case 'view': return 'attempted';
                    default: return null;
                }        
            case 'chat':
                switch ($action){
                    case 'talk':
                    case 'send chat_messages':
                        return 'interacted';
                    case 'view': 
                    case 'viewed chat':   
                        return 'attempted';
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
                    case 'add post':
                    case 'created forum_posts': 
                        return 'commented';
                    case 'add discussion': 
                    case 'created forum_discussions':    
                        return 'created';
                    case 'view forum':
                    case 'view discussion':
                    case 'viewed forum':
                    case 'viewed forum_discussions':
                        return 'attempted';
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
}
