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

class DataProvider {

    private $roles;

    public function __construct() {
        global $DB;
        $R = $DB->get_records('role');
        foreach ($R as $r){
            $this->roles[$r->id] = $r->shortname;
        }
    }


    public function getAuth(){
       $provider = Credentials::getProvider();
       $credentials = $provider->getCredentials();
       $auth = new \stdClass();
       $auth->endpoint = $credentials->lrs_endpoint;
       $auth->type = 'basic';

       switch ($auth->type){
           case 'basic':
               $auth->chain = $credentials->lrs_username . ':' . $credentials->lrs_password;
               break;

           case 'oauth':
                //TODO: Incluir autenticacion oAUTH con LRS.
               break;
       }
        return $auth;
    }

    public function validateStatements () {
        return ( 'true' == get_config('local_smart_klass', 'check_statement') );
    }

    public function get_user ($userid) {
        global $DB;

        return $DB->get_record('user', array('id'=>$userid) );
    }

    public function get_homepage () {
        global $CFG;
        return $CFG->wwwroot;
    }

    public function getManager () {
        global $DB;
        $manager_email = get_config('local_smart_klass', 'managerid');
        $manager = $DB->get_record('user', array('email'=>$manager_email));
        return ( empty($manager_email) ) ? get_admin() : $manager;
    }

    public function getInstructors ($courseid) {
        global $DB;
        $context = \context_course::instance($courseid);

        //teacher rolesids
        $teacher_role_id = array(3,4);

        $teachers = get_role_users($teacher_role_id, $context);
        $teachers_ids = array();
        foreach ($teachers as $teacher){
            $teachers_ids[] = $teacher->id;
        }

        return $teachers_ids;
    }

    public function get_platform_version () {
        global $CFG;
        $platform_version = "Moodle {$CFG->release} v. {$CFG->version}";
        if ($CFG->version >= SMART_KLASS_MOODLE_26) {
            $pluginman = \core_plugin_manager::instance();
        } else {
            require_once($CFG->libdir . '/pluginlib.php');
            $pluginman = \plugin_manager::instance();
        }
        $plugin_info = $pluginman->get_plugin_info('local_smart_klass');
        $plugin_release = ($CFG->version >= SMART_KLASS_MOODLE_26) ? $plugin_info->release : '';
        $plugin_version = $plugin_info->versiondisk;
        $plugin_version = "SmartKlass {$CFG->release} v. {$plugin_version}";

        return $platform_version . ' - ' . $plugin_version;
    }

    public function getCollector($name=null) {
        global $DB;
        $collector = $DB->get_record('local_smart_klass', array('name'=>$name, 'active'=>1));
        return ( empty($collector) ) ? null : $collector;
    }

    public function updateCollector ($name='', $new_data){
        global $DB;
        if ( empty($name) )return false;



        $collector = $DB->get_record('local_smart_klass', array('name'=>$name));

        if ( empty($collector) )return false;

        if ($new_data != null )
            $collector->data = json_encode($new_data);


        $DB->update_record('local_smart_klass', $collector);
    }

    public function getCourses (Collector $collector) {
        global $DB;

        $table = 'course';
        $reprocess = (count($collector->getReproccessIds($table))>0) ? ' OR id IN (' . implode(',', $collector->getReproccessIds($table)) . ') ' : '';
        $limit = $collector->getMaxRegistrys();
        $limit = ($limit == null) ? 0 : $limit;
        $reg = $DB->get_records_select ($table,
                                        'id>?' . $reprocess,
                                        array($collector->getLastRegistry($table)),
                                        '',
                                        'id, category, fullname, shortname,  startdate, timecreated, timemodified, enablecompletion',
                                        0, $limit
          );

        foreach($reg as &$course) {
            global $DB;
            $sections = $DB->get_records('course_sections', array('course'=>$course->id));
            $course->modules = array();
            foreach ($sections as $section) {
                $moduleinfo = $this->getModule($section);
                if ( !empty($moduleinfo) ) $course->modules[] = $moduleinfo;
            }
            ksort($course->modules, SORT_NUMERIC);
        }
        return $reg;
    }

    public function getCourseUserTime ($courseid=0, $userid=0){
        global $DB;

        $limitinseconds = 45 * 60; //Establezco el tiempo en minutos que considero una sesion valida

       	$sql = "course = $courseid AND userid = ".$userid."";

        if ($logs = $DB->get_records_select('log', $sql , array ("action"=>'view'),'', 'id,time')) {

            $previouslog = array_shift($logs);
            $previouslogtime = $previouslog->time;
            $sessionstart = $previouslogtime;
            $dedication = 0;



            foreach ($logs as $log) {
                if (($log->time - $previouslogtime) > $limitinseconds) {
                    $dedication += $previouslogtime - $sessionstart;
                    $sessionstart = $log->time;
                }
                $previouslogtime = $log->time;
            }
            $dedication += $previouslogtime - $sessionstart;
        } else {
            $dedication = 0;
        }

        return $dedication;
    }

    public function getModules (Collector $collector) {
        global $DB;

        $table = 'course_sections';
        $reprocess = (count($collector->getReproccessIds($table))>0) ? ' OR id IN (' . implode(',', $collector->getReproccessIds($table)) . ') ' : '';
        $limit = $collector->getMaxRegistrys();
        $limit = ($limit == null) ? 0 : $limit;
        $reg = $DB->get_records_select ($table,
                                        "(id>?{$reprocess}) AND visible=?",
                                        array($collector->getLastRegistry($table), 1),
                                        '',
                                        'id, course, name, summary, section, sequence, visible, availablefrom, availableuntil',
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
                                            'id, course, name, summary, section, sequence, visible, availablefrom, availableuntil'
                                        );
        }
        $obj = new \stdClass();
        $obj->id = $section->id;
        $obj->course = $section->course;
        $obj->name = (empty($section->name)) ? 'MÃ³dulo ' . $section->section : $section->name;
        if ( !empty($section->summary) ){
            $obj->summary = strip_tags($section->summary);
        }

        $modulesarr = array();
        $modules = $DB->get_records_sql("
            SELECT cm.id, m.name AS modname, added, availablefrom, availableuntil
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
                if ($modules[$moduleid]->availablefrom > 0)
                    $module['availablefrom'] = $modules[$moduleid]->availablefrom;
                if ($modules[$moduleid]->availableuntil >0)
                    $module['availableuntil'] = $modules[$moduleid]->availableuntil;
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
                if ($m->availablefrom > 0)
                    $module['availablefrom'] = $m->availablefrom;
                if ($m->availableuntil >0)
                    $module['availableuntil'] = $m->availableuntil;
                $modulesarr[$m->id] = (object)$module;

                $module_date_creation = ($module_date_creation == null || $module_date_creation > $m->added) ?
                                            $m->added :
                                            $module_date_creation;
            }
        }

        if ( count($modulesarr)>0 )
            $obj->activities = $modulesarr;

        if ($section->availablefrom > 0)
            $obj->availablefrom = $section->availablefrom;

        if ($section->availableuntil > 0)
            $obj->availableuntil = $section->availableuntil;

        if ( empty($module_date_creation) )
           $module_date_creation = $DB->get_field('course', 'timecreated', array('id'=>$section->course) );

        $obj->timecreated = $module_date_creation;
        return $obj;
    }

    public function getLog (Collector $collector) {
        global $DB;
        $table = 'log';
        $reprocess = (count($collector->getReproccessIds($table))>0) ? ' OR id IN (' . implode(',', $collector->getReproccessIds($table)) . ') ' : '';
        $limit = $collector->getMaxRegistrys();
        $limit = ($limit == null) ? 0 : $limit;
        $reg = $DB->get_records_select ($table,
                                        '(id>? AND cmid>0 AND module IN ("assign", "chat", "course", "feedback", "folder", "forum", "page", "quiz", "resource", "scorm", "url") )' . $reprocess,
                                        array($collector->getLastRegistry($table)),
                                        '',
                                        'id, time, userid, course, module as modname, cmid, action',
                                        0, $limit
          );

        foreach ($reg as &$item) {
            if ($item->cmid == 0) continue;
            $d = $DB->get_record('course_modules', array('id'=>$item->cmid), 'instance as activityid,section as moduleid');
            if ($d != null) {
                $item->activityid = $d->activityid;
                $item->moduleid = $d->moduleid;
            }
        }
        return $reg;
    }

    public function getAllEnrolments(Collector $collector) {
		global $DB;

		$table = 'user_enrolments';
		$reprocess = (count($collector->getReproccessIds($table))>0) ? " OR id IN (" . implode(',', $collector->getReproccessIds($table)) . ") " : "";

		$limit = $collector->getMaxRegistrys();
        $limit = ($limit == null) ? 0 : $limit;

		if (!empty($CFG->gradebookroles)) {
			$roles = ' AND ra.roleid IN ('.$CFG->gradebookroles.')';
		} else {
			// This causes it to default to everyone (if there is no student role)
			$roles = '';
		}
		//This sql is based on sql of ./completion/cron.php
		$sql = "
        SELECT
			ue.id,
            e.courseid AS course,
            u.id AS userid,
            ue.timestart AS time
        FROM
            {user} u
        INNER JOIN
            {user_enrolments} ue
         ON ue.userid = u.id
        INNER JOIN
            {enrol} e
         ON e.id = ue.enrolid
        INNER JOIN
            {role_assignments} ra
         ON ra.userid = u.id
        WHERE
			ue.status = 0
        AND e.status = 0
        AND u.deleted = 0
        AND ue.timestart < ?
        AND (ue.timeend > ? OR ue.timeend = 0)
        AND ue.id > ? $reprocess
			$roles
        ORDER BY
            course,
            userid
        LIMIT 0, $limit
		";

		$now = time();
		$reg = $DB->get_records_sql($sql, array($now, $now, $collector->getLastRegistry($table)));

        return $reg;
		/*$sql = "SELECT DISTINCT ue.id, u.username, e.courseid
				FROM mdl_user_enrolments ue, mdl_user u, mdl_enrol e
				WHERE ue.userid = u.id AND ue.enrolid = e.id
				ORDER BY ue.id";*/
    }

    public function getEnrolments (Collector $collector) {
		$studentEnrols = $this->getCourseCompletion($collector, 'id, userid, course, timeenrolled as time');
        return $enrols;

    }

    public function getCourseInitDate (Collector $collector) {
        return $this->getCourseCompletion($collector, 'id, userid, course, timeenrolled, timestarted as time');
    }


    public function getCourseCompletedDate (Collector $collector) {
        return $this->getCourseCompletion($collector, 'id, userid, course, timeenrolled, timecompleted as time');
    }

    public function getCourseCompletion (Collector $collector, $colums='*' ) {
        global $DB;
        $table = 'course_completions';
        $reprocess = (count($collector->getReproccessIds($table))>0) ? ' OR id IN (' . implode(',', $collector->getReproccessIds($table)) . ') ' : '';
        $limit = $collector->getMaxRegistrys();
        $limit = ($limit == null) ? 0 : $limit;
        $reg = $DB->get_records_select ($table,
                                        'id>?' . $reprocess,
                                        array($collector->getLastRegistry($table)),
                                        '',
                                        $colums,
                                        0, $limit
          );
        return $reg;
    }

    public function getActivitiesCompletion (Collector $collector) {
        global $DB;
        $table = 'course_modules_completion';
        $reprocess = (count($collector->getReproccessIds($table))>0) ? ' OR id IN (' . implode(',', $collector->getReproccessIds($table)) . ') ' : '';
        $limit = $collector->getMaxRegistrys();
        $limit = ($limit == null) ? 0 : $limit;
        $reg = $DB->get_records_select ($table,
                                        'id>?' . $reprocess,
                                        array($collector->getLastRegistry($table)),
                                        '',
                                        'id, coursemoduleid as cmid, userid, completionstate, timemodified',
                                        0, $limit
          );
        foreach ($reg as &$item) {
            if ($item->cmid == 0) continue;
            $d = $DB->get_record('course_modules', array('id'=>$item->cmid), 'course, instance as activityid,section as sectionid, module as modid');
            if ($d == false){
                $item = null;
                continue;
            }
            $item->activityid = $d->activityid;
            $item->section = $d->sectionid;
            $item->course = $d->course;
            $item->mod = $DB->get_field('modules', 'name', array('id'=>$d->modid));
        }
        return array_filter($reg);
    }

    public function getGrades (Collector $collector) {
        global $DB;

        $table = 'grade_grades_history';
        $reprocess = (count($collector->getReproccessIds($table))>0) ? ' OR ggh.id IN (' . implode(',', $collector->getReproccessIds($table)) . ') ' : '';
        $limit = $collector->getMaxRegistrys();
        $limit = ($limit == null) ? 0 : $limit;
        $reg = $DB->get_records_sql("
                 SELECT ggh.id,
                        ggh.userid,
                        gi.courseid,
                        (SELECT id
                           FROM {course_modules} cm
                          WHERE     cm.module = (SELECT id
                                                   FROM {modules} m
                                                  WHERE m.name = gi.itemmodule)
                                AND cm.instance=gi.iteminstance AND cm.course = gi.courseid)
                           AS moduleid,
                        gi.itemmodule as activitytype,
                        gi.iteminstance AS activityid,
                        gi.grademax AS maxscore,
                        gi.grademin AS minscore,
                        ggh.finalgrade AS score,
                        ggh.timemodified as time
                   FROM {{$table}} ggh
                        JOIN {grade_items} gi ON gi.id = ggh.itemid
                  WHERE     (ggh.id > ?{$reprocess})
                        AND ggh.source LIKE 'mod/%'
                        AND ggh.finalgrade IS NOT NULL ORDER BY ggh.id ASC",
                    array($collector->getLastRegistry($table)), 0, $limit);
        return $reg;
    }

    public function getActivityId($type, $id){
        global $CFG;
        return $CFG->wwwroot . '/' . $type . '/' . $id;
    }

    public function getModuleId($courseid, $sectionid){
        global $CFG;
        //
        return $CFG->wwwroot . '/module/' . $courseid . '/' . $sectionid;
    }


    public function getCourseId($courseid){
        global $CFG;
        return $CFG->wwwroot . '/course/' . $courseid;
    }

    public function getActivityType ($module) {
        switch ($module){
            case 'assign': return 'performance';
            case 'assignment': return 'performance';
            case 'chat': return 'meeting';
            case 'course': return 'course';
            case 'feedback': return 'performance';
            case 'survey': return 'performance';
            case 'folder': return 'file';
            case 'forum': return 'meeting';
            case 'page': return 'lesson';
            case 'quiz': return 'assessment';
            case 'resource': return 'file';
            case 'scorm': return 'lesson';
            case 'book': return 'lesson';
            case 'choice': return 'question';
            case 'workshop': return 'assessment';
            case 'url': return 'link';
            default: return 'lesson';
        }
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
                    case 'view forum':
                    case 'view discussion':
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

    public function getMaxId($table){
        global $DB;
        return $DB->get_field($table, 'MAX(id)', array());
    }

    public function get_reg_id ($id, $collectorname){
        global $CFG;
        $arr_collectors =  explode("\\", $collectorname);
        $collectorname = array_pop($arr_collectors);

        if (!is_numeric($id)){
            $splitter = explode('/',$id);
            $tablename = ucwords ($splitter[0]);
            $tablename = str_replace(' ', '', $tablename);
            $collectorname .= '_'  . $tablename;
            $id = $splitter[1];

        }
        echo "***** Collector Name: $collectorname *****\n";
        switch ($collectorname){
            case 'ActivityCompletedCollector': $table_base = 'course_modules_completion'; break;
            case 'CourseCompletedCollector': $table_base = 'course_completions'; break;
            case 'CourseCreateCollector': $table_base = 'course'; break;
            case 'CourseEnrolCollector': $table_base = 'user_enrolments'; break;
            case 'CourseInitializedCollector': $table_base = 'course_completions'; break;
            case 'GradeCollector': $table_base = 'grade_grades_history'; break;
            case 'LogCollector_LegacyLog': case 'LogCollector':  $table_base = 'log'; break;
            case 'LogCollector_StandardLog': $table_base = 'logstore_standard_log'; break;
            case 'LogCollector_LogEstándar': $table_base = 'logstore_standard_log'; break;
            case 'ModuleCreateCollector': $table_base = 'course_sections'; break;
            default: echo "¡¡ERROR: Nombre de colector [$collectorname] no encontrado!!\n";
        }
        $return = new \stdClass();
        $return->uri = $CFG->wwwroot . '/' . $table_base . '/' . $id;
        $return->id = $id;
        $return->table = $table_base;
        return $return;
    }

    public function getRole ($userid, $courseid){
        $context = \context_course::instance($courseid);
        $roles = get_user_roles($context, $userid, true);
        return current($roles)->shortname;
    }

    public function getLanguageString ($identifier, $component = '', $a = null, $lazyload = false) {
        return get_string ($identifier, $component, $a, $lazyload);
    }

    public function getConfig ($param) {
        return get_config('local_smart_klass', $param);
    }

    public function setConfig($param, $value) {
        return set_config($param, $value, 'local_smart_klass');
    }

    public function getProxy () {
        global $CFG;

        $proxy = array();

        $proxy['proxyhost'] = $CFG->proxyhost;
        $proxy['proxyport'] = $CFG->proxyport;
        $proxy['proxyuser'] = $CFG->proxyuser;
        $proxy['proxypassword'] = $CFG->proxypassword;
        $proxy['proxytype'] = $CFG->proxytype;

        return $proxy;
    }

}
