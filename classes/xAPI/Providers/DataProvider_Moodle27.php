<?php
namespace SmartKlass\xAPI;

/**
 * xAPI DataProvider Class
 *
 * @package    local_smart_klass
 * @copyright  KlassData <kttp://www.klassdata.com>
 * @author     Oscar <oscar@klassdata.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class DataProvider_Moodle27 extends DataProvider {
    
    public function __construct() {   
    }
    
    
    
    
    public function getModules (Collector $collector) {
        global $DB;
        
        $reprocess = (count($collector->getReproccessIds())>0) ? ' OR id IN (' . implode(',', $collector->getReproccessIds()) . ') ' : '';
        $limit = $collector->getMaxRegistrys();
        $limit = ($limit == null) ? 0 : $limit;
        $reg = $DB->get_records_select ('course_sections', 
                                        "(id>?{$reprocess}) AND visible=?", 
                                        array($collector->getLastRegistry(), 1),
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
        

        //$modinfo = get_fast_modinfo($section->course);
        //$cms = $modinfo->get_cms();
        //$availableinfo = $cms->get_available_info();
            
            
        
        
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
}
