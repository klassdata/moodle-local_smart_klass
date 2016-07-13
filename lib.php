<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Plugin version info
 *
 * @package    local_smart_klass
 * @copyright  KlassData <kttp://www.klassdata.com>
 * @author     Oscar Ruesga <oscar@klassdata.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

define('SMART_KLASS_ACTION_DEFAULT',        'd');
define('SMART_KLASS_ACTION_HARVERTS',       'h');
define('SMART_KLASS_ACTION_EDIT',           'e');
define('SMART_KLASS_ACTION_ACTIVATE',       'a');


define('SMART_KLASS_DASHBOARD_STUDENT',          1);
define('SMART_KLASS_DASHBOARD_TEACHER',          2);
define('SMART_KLASS_DASHBOARD_INSTITUTION',      3);


// define('SMART_KLASS_OAUTHSERVER_URL', 'http://develop2.klaptek.com/oauth/resource.php');
// define('SMART_KLASS_DASHBOARD_URL',   'http://demo.klassdata.com/');

define('SMART_KLASS_MOODLE_27',   2014051200);
define('SMART_KLASS_MOODLE_26',   2013111800);
define('SMART_KLASS_MOODLE_25',   2013051300);

define('SMART_KLASS_TRACKER_EMPTYVALUE',   'EMPTY/');


/**
 * Get a valid access token from oAuth server
 *
 * @param  integer $userid    The moodle user id
 * @param  integer $role the dashboard role. Values: 1/Student; 2/Teacher; 3/Institution
 *
 * @return stdClass Object with a valid oauth credentials. null if invalid oauth credentials
 */



/**
 * Validate access token in oauth servr
 * @param  string $accesstoken    accesstoken to validate with Smart Klass Dashboard
 * @return
 */
function local_smart_klass_get_dashboard ($access_token=null){
    //TODO 3 Get the correct dashboard from Smart Klass Dashboard
}


/**
 * Determines whether the current user can access to manage Smart Klass Configuration
 *
 * @return bool true if user can access logs
 */
function local_smart_klass_can_manage() {
    $context = context_system::instance();

    if (has_capability('local/smart_klass:manage', $context)) {
        return true;
    }

    return false;
}



function local_smart_klass_dashboard_roles ($userid, $context) {
    $roles	= get_user_roles($context, $userid, true);

    $dashboard	= new stdClass ();

    $dashboard->student		= false;
    $dashboard->teacher		= false;
    $dashboard->institution	= false;

    $student_default_role		= array(5);
    $teacher_default_role		= array(3,4);
    $intitution_default_role	= array(1);

    foreach($roles as $role){
        if(in_array($role->roleid, $student_default_role))
            $dashboard->student = true;
        if(in_array($role->roleid, $teacher_default_role))
            $dashboard->teacher = true;
        if(in_array($role->roleid, $intitution_default_role))
            $dashboard->institution = true;
    }
    if (is_siteadmin()) $dashboard->institution = true;
    return $dashboard;
}



function local_smart_klass_extends_navigation(global_navigation $navigation) {
    global $CFG, $PAGE, $USER;

    //Creo menú en el Bloque de administración para el plugin
    $nodeSmartKlap = $navigation->add(get_string('pluginname', 'local_smart_klass') );

    $serialNumber = get_config('local_smart_klass', 'smartklass_serialnumber');
    if (empty($serialNumber) ){
        if ( local_smart_klass_can_manage() ) {
            $nodeSmartKlap->add(
                get_string('configure_access', 'local_smart_klass'),
                new moodle_url($CFG->wwwroot.'/local/smart_klass/register.php' )
            );
        }
    } else {
        $dashboard_roles = local_smart_klass_dashboard_roles($USER->id, $PAGE->context);
        if ( get_config('local_smart_klass', 'activate_student_dashboard') == '1' && $dashboard_roles->student ) {
            $nodeSmartKlap->add(
                get_string('studentdashboard', 'local_smart_klass'),
                new moodle_url(
                    $CFG->wwwroot.'/local/smart_klass/dashboard.php',
                    array('cid' => $PAGE->context->id, 'dt'=>SMART_KLASS_DASHBOARD_STUDENT)
                )
            );
        }
        if ( get_config('local_smart_klass', 'activate_teacher_dashboard') == '1' && $dashboard_roles->teacher ) {
            $nodeSmartKlap->add(
                get_string('teacherdashboard', 'local_smart_klass'),
                new moodle_url(
                    $CFG->wwwroot.'/local/smart_klass/dashboard.php',
                    array('cid' => $PAGE->context->id, 'dt'=>3)
                )
            );
        }

        if ( get_config('local_smart_klass', 'activate_institution_dashboard') == '1' &&  ($dashboard_roles->institution || local_smart_klass_can_manage()) ) {
            $nodeSmartKlap->add(
                get_string('institutiondashboard', 'local_smart_klass'),
                new moodle_url(
                    $CFG->wwwroot.'/local/smart_klass/dashboard.php',
                    array('cid' => $PAGE->context->id, 'dt'=>SMART_KLASS_DASHBOARD_INSTITUTION)
                )
            );
        }
    }

    //Remove Smart Klass root node it empty
    if ( !$nodeSmartKlap->has_children()) {
        $nodeSmartKlap->remove();
    }
 }

/**
 * Function to be run periodically according to the moodle cron
 * Prepare all statemenst and send it to an LRS
 * throw the xAPI services
 * @return void
 */

function local_smart_klass_cron() {
    global $CFG;
    if ($CFG->version < SMART_KLASS_MOODLE_27) local_smart_klass_harvest();
}


function local_smart_klass_harvest( $collector=array() ) {
    if ( get_config('local_smart_klass', 'activate') != 1){
        echo get_string('harvester_service_unavailable', 'local_smart_klass');
        return;
    }

    print_r(get_config('local_smart_klass', 'harvestcicles'));
    $max_cicles = get_config('local_smart_klass', 'max_block_cicles');
    $harvest_cicles = get_config('local_smart_klass', 'harvestcicles');
    $harvest_cicles = ( empty($harvest_cicles) ) ? 0 : $harvest_cicles;
    $harvest_cicles++;
    set_config('harvestcicles', $harvest_cicles, 'local_smart_klass');

    print_r(get_config('local_smart_klass', 'harvestcicles'));

    if ($harvest_cicles >= $max_cicles) {
        set_config('croninprogress', false, 'local_smart_klass');
        set_config('harvestcicles', 0, 'local_smart_klass');
    }

    if (get_config('local_smart_klass', 'croninprogress') == true){
        echo get_string('harvester_service_instance_running', 'local_smart_klass');
        return;
    }

    global $CFG, $USER, $DB;

    set_config('croninprogress', true, 'local_smart_klass');

    $out = array();

    //Autoload library class
    require_once (dirname(__FILE__) . '/classes/xAPI/Autoloader.php');
    SmartKlass\xAPI\Autoloader::register();

    $objlog = new stdClass();
    $objlog->init = 0;
    $objlog->finish = 0;
    $objlog->result = 0;
    $objlog->collectors = '';
    $objlog->logfile = '';
    $objlog->error = '';

    try {
        $objlog->init = time();
        $out[] = 'Enviando xAPI statements............... -- ' . date('r', $objlog->init);

        if (!empty($collector)) {
            $custom_collector = (count($collector)>0) ? ' AND id IN (' . implode(',', $collector) . ') ' : '';
        }
        $collectors = $DB->get_records_select('local_smart_klass', 'active=?' . $custom_collector, array(1));

        foreach ($collectors as $item){
            $trace =  '...... Recolectando ' . $item->name . ' -- Inicio: ' . date('r')  . ' / ';
            $collector_class = 'SmartKlass\\xAPI\\' . $item->name . 'Collector';
            $collector = new $collector_class;
            $trace .= 'Fin: ' . date('r');
            $out[] = $trace;
        }
        $objlog->logfile = SmartKlass\xAPI\Logger::save_log();
        $objlog->finish = time();
        $objlog->result = 1;
        $objlog->error = '';
        $out[] = 'Enviados xAPI statements............... -- ' . date('r', $objlog->finish);
        $url = SmartKlass\xAPI\Logger::get_url($objlog->logfile);
        $out[] = html_writer::link($url, $objlog->logfile);

        set_config('croninprogress', false, 'local_smart_klass');
        set_config('harvestcicles', 0, 'local_smart_klass');
        set_config('lastcron', $objlog->finish, 'local_smart_klass');

    } catch (Exception $e){
        set_config('croninprogress', false, 'local_smart_klass');
        $objlog->error = json_encode($e);
        $objlog->logfile = SmartKlass\xAPI\Logger::save_log();
        $objlog->finish = time();
        $objlog->result = 0;
        $out[] = 'Error............... -- ' . date('r', $objlog->finish);
        $url = SmartKlass\xAPI\Logger::get_url($objlog->logfile);
        $out[] = html_writer::link($url, $objlog->logfile);
    }

    $collectors = $DB->get_records_select('local_smart_klass', 'active=?' . $custom_collector, array(1));
    $objlog->collectors = json_encode($collectors);
    $DB->insert_record('local_smart_klass_log', $objlog);
    $br = html_writer::empty_tag('br');
    echo implode($br, $out);

}

function local_smart_klass_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options=array()) {
    // Check the contextlevel is as expected - if your plugin is a block, this becomes CONTEXT_BLOCK, etc.
    if ($context->contextlevel != CONTEXT_SYSTEM) {
        return false;
    }

    // Make sure the filearea is one of those used by the plugin.
    if ($filearea !== 'local_smart_klass' ) {
        return false;
    }

    // Make sure the user is logged in and has access to the module (plugins that are not course modules should leave out the 'cm' part).
    require_login();

    // Check the relevant capabilities - these may vary depending on the filearea being accessed.
    if (!has_capability('local/smart_klass:manage', $context)) {
        return false;
    }

    $forcedownload = true;

    // Leave this line out if you set the itemid to null in make_pluginfile_url (set $itemid to 0 instead).
    $itemid = array_shift($args); // The first item in the $args array.

    // Use the itemid to retrieve any relevant data records and perform any security checks to see if the
    // user really does have access to the file in question.

    // Extract the filename / filepath from the $args array.
    $filename = array_pop($args); // The last item in the $args array.
    if (!$args) {
        $filepath = '/'; // $args is empty => the path is '/'
    } else {
        $filepath = '/'.implode('/', $args).'/'; // $args contains elements of the filepath
    }

    set_config('croninprogress', false, 'local_smart_klass');

    // Retrieve the file from the Files API.
    $fs = get_file_storage();
    $file = $fs->get_file($context->id, 'local_smart_klass', $filearea, $itemid, $filepath, $filename);
    if (!$file) {
        return false; // The file does not exist.
    }

    send_stored_file($file, 'default', 0, true, $options);
}

function local_smart_klass_activate_harvester( $collectorid ) {
    global $DB;
    $collector= $DB->get_record('local_smart_klass', array('id'=>$collectorid), 'id, active' );
    $collector->active = ($collector->active == 1) ? 0 : 1;
    $DB->update_record('local_smart_klass', $collector);
}

function local_smart_klass_get_harvesters () {
    global $CFG, $DB;

    $harvesters = $DB->get_records('local_smart_klass', null, null, 'name');

    $collectors_class = scandir(dirname(__FILE__).'/classes/xAPI/Collectors');

    $key = array_search ( 'Collector.php', $collectors_class );
    unset($collectors_class[$key]);
    $key = array_search ( '.', $collectors_class );
    unset($collectors_class[$key]);
    $key = array_search ( '..', $collectors_class );
    unset($collectors_class[$key]);

    foreach ($collectors_class as &$item) {
        $item = str_replace('Collector.php', '', $item);

        if ( !array_key_exists ($item, $harvesters) ) {

            try {
                //Autoload library class
                require_once (dirname(__FILE__) . '/classes/xAPI/Autoloader.php');
                SmartKlass\xAPI\Autoloader::register();

                $class_file = dirname(__FILE__) . "/classes/xAPI/Collectors/{$item}Collector.php";
                if ( !file_exists($class_file) ) continue;
               // if (!class_exists("SmartKlass\\xAPI\\$item") ) continue;
                $class = new \ReflectionClass("SmartKlass\\xAPI\\{$item}Collector");
                $parentclass = $class->getParentClass();
                if( $parentclass->name != 'SmartKlass\\xAPI\\Collector' ||
                    $class->getMethod('collectData') == null ||
                    $class->getMethod('prepareStatement') == null
                  ) continue;

                $o = new stdClass();
                $o->name = $item;
                $o->data = null;
                $o->active = 1;
                $o->deleted = 0;
                $o->lastregistry = 0;
                $o->lastexectime = 0;
                $id = $DB->insert_record ('local_smart_klass', $o);
            } catch (Exception $ex) {}
         }
        unset($harvesters[$item]);

    }
    if (count($harvesters) > 0) {
        foreach ($harvesters as $key=>$value) {
            $o = $DB->get_record('local_smart_klass', array('name'=>$key), 'id, deleted' );
            $o->deleted = '1';
            $status = $DB->update_record_raw('local_smart_klass', $o);
        }
    }
    $harvesters = $DB->get_records('local_smart_klass', array('deleted'=>'0'));
    return $harvesters;

}


//Autoload library class
require_once (dirname(__FILE__) . '/classes/xAPI/Autoloader.php');
SmartKlass\xAPI\Autoloader::register();

