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


define('SMART_KLASS_OAUTHSERVER_URL', 'http://develop2.klaptek.com/oauth/resource.php');
define('SMART_KLASS_DASHBOARD_URL',   'http://demo.klassdata.com/');

define('SMART_KLASS_MOODLE_27',   2014051200);
define('SMART_KLASS_MOODLE_26',   2013111800);
define('SMART_KLASS_MOODLE_25',   2013051300);


/**
 * Get a valid access token from oAuth server
 *
 * @param  integer $userid    The moodle user id
 * @param  integer $role the dashboard role. Values: 1/Student; 2/Teacher; 3/Institution
 * 
 * @return stdClass Object with a valid oauth credentials. null if invalid oauth credentials
 */
function local_smart_klass_get_oauth_accesstoken ($userid, $role){
    global $DB;
    
    $oauth_obj = $DB->get_record( 'local_smart_klass_dash_oauth', array('userid'=>$userid, 'dashboard_role'=>$role) );


    
    if (local_smart_klass_oauth_validate($oauth_obj->access_token)){
        $oauth_obj->modified = time();
        
    } else {
        //Try refresh token
        $accesstoken = local_smart_klass_oauth_refreshtoken ($oauth_obj->refresh_token);
        if ( empty($accesstoken) ) return null;
        $time = time();
        $oauth_obj->access_token = $accesstoken;
        $oauth_obj->modified = $time;
        $oauth_obj->created = $time;
    }

    $DB->update_record('local_smart_klass_dash_oauth', $oauth_obj);

    return $oauth_obj;
}

/**
 * Insert a new access_token registry
 * @param  string $code    accesstoken to validate with oauth server
 * @param  string $refresh    refreshtoken to validate with oauth server
 * @param  string $email    user email use with access_token (oautn uid)
 * @param  string $rol    rol allow by access token
 * @param  string $user_id    moodle user_id associate with accesstoken
 * @return mixed record id if OK or false if KO
 */
function local_smart_klass_save_access_token ($code, $refresh, $email, $rol, $user_id) {
    global $DB;
    
    $t = time();
    $obj = new stdClass();
    $obj->access_token = $code;
    $obj->refresh_token = $refresh;
    $obj->userid = $user_id;
    $obj->email = $email;
    $obj->dashboard_role = $rol;
    $obj->modified = $t;
    $obj->created = $t;

    return $DB->insert_record('local_smart_klass_dash_oauth', $obj);
}


/**
 * Validate access token in oauth servr
 * @param  string $accesstoken    accesstoken to validate with oauth server
 * @return bool true if oauth accesstoken is ok, false is oauth accesstoken is KO
 */
function local_smart_klass_oauth_validate ($access_token=null){
		$fields = array('access_token' => $access_token);
		$ch = curl_init(SMART_KLASS_OAUTHSERVER_URL);
		
		$url = SMART_KLASS_OAUTHSERVER_URL;
		$qry_str = "?access_token=".$access_token;


		// Set query data here with the URL
		curl_setopt($ch, CURLOPT_URL,$url.$qry_str); 
		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, '3');		
		
		
		$curl_response = curl_exec($ch);	// execute curl request
	    $curl_response  = json_decode($curl_response);
		curl_close($ch);

		if(empty($curl_response) || $curl_response->error=='invalid_token')
			return false;
		else
			return true;
}


/**
 * Get a valid token throw refresh token in oauth servr
 * @param  string $refreshtoken    Valid oauth refresh token
 * @return string new access token if OK, null if KO
 */
function local_smart_klass_oauth_refreshtoken ($refresh_token=null){
    //TODO 2  Try to get the access_token throw refresh_token in oauth server
}




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



function local_smart_klass_dashboard_roles ($userid) {
    global $PAGE;
    $roles	= get_user_roles($PAGE->context, $userid, true);

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
    return $dashboard;
}



function local_smart_klass_extends_navigation(global_navigation $navigation) {
    global $CFG, $PAGE, $USER;
    
    //Creo menú en el Bloque de administración para el plugin
    $nodeSmartKlap = $navigation->add(get_string('pluginname', 'local_smart_klass') );
	
    
	if (get_config('local_smart_klass', 'oauth_clientid') == '' || get_config('local_smart_klass', 'oauth_secret') == ''){
        if ( local_smart_klass_can_manage() )
            $nodeSmartKlap->add( get_string('configure_access', 'local_smart_klass'), new moodle_url($CFG->wwwroot.'/local/smart_klass/register.php' ));
    } else {
        $dashboard_roles = local_smart_klass_dashboard_roles($USER->id);
        if ( get_config('local_smart_klass', 'activate_student_dashboard') == '1' && $dashboard_roles->student ) {
            $nodeSmartKlap->add( get_string('studentdashboard', 'local_smart_klass'), new moodle_url($CFG->wwwroot.'/local/smart_klass/dashboard.php', array('cid' => $PAGE->context->id, 'dt'=>SMART_KLASS_DASHBOARD_STUDENT)));
        }
        if ( get_config('local_smart_klass', 'activate_teacher_dashboard') == '1' && $dashboard_roles->teacher ) {
            $nodeSmartKlap->add( get_string('teacherdashboard', 'local_smart_klass'), new moodle_url($CFG->wwwroot.'/local/smart_klass/dashboard.php', array('cid' => $PAGE->context->id, 'dt'=>SMART_KLASS_DASHBOARD_TEACHER)));
        }

        if ( get_config('local_smart_klass', 'activate_institution_dashboard') == '1' &&  ($dashboard_roles->institution || local_smart_klass_can_manage()) ) {
            $nodeSmartKlap->add( get_string('institutiondashboard', 'local_smart_klass'), new moodle_url($CFG->wwwroot.'/local/smart_klass/dashboard.php', array('cid' => $PAGE->context->id, 'dt'=>SMART_KLASS_DASHBOARD_INSTITUTION)));
        }  
    }
    
    //Remove Smart Klass root node it empty
    if ( !$nodeSmartKlap->has_children()) 
        $nodeSmartKlap->remove();
       
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
    
    if (get_config('local_smart_klass', 'croninprogress') == true){
        echo get_string('harvester_service_instance_running', 'local_smart_klass');
        return;
    }
    
    global $CFG, $USER, $DB;
    
    $harvest_cicles = get_config('harvestcicles', 'local_smart_klass');
    $harvest_cicles = ( empty($harvest_cicles) ) ? 0 : $harvest_cicles;
    $harvest_cicles++;
    
    $max_cicles = get_config('max_block_cicles', 'local_smart_klass');
    
    if ($harvest_cicles >= $max_cicles) {
        set_config('croninprogress', false, 'local_smart_klass');
        set_config('max_block_cicles', 0, 'local_smart_klass');
    } else {
        set_config('croninprogress', true, 'local_smart_klass');
        set_config('max_block_cicles', $harvest_cicles, 'local_smart_klass');
    }
    
    
    
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



function local_smart_klass_trackurl() {
    global $DB, $PAGE, $COURSE, $SITE, $USER;
    $pageinfo = get_context_info_array($PAGE->context->id);
    $trackurl = "'";
    if (isset($pageinfo[1]->category)) {
        if ($category = $DB->get_record('course_categories', array('id'=>$pageinfo[1]->category))) {
            $cats=explode("/",$category->path);
            foreach(array_filter($cats) as $cat) {
                if ($categorydepth = $DB->get_record("course_categories", array("id" => $cat))) {;
                    $trackurl .= $categorydepth->name.'/';
                }
            }
        }
    }
    if (isset($pageinfo[1]->fullname)) {
        if (isset($pageinfo[2]->name)) {
            $trackurl .= $pageinfo[1]->fullname.'/';
        } else if ($PAGE->user_is_editing()) {
            $trackurl .= $pageinfo[1]->fullname.'/'.get_string('edit', 'local_smart_klass') . '/';
        } else {
            $trackurl .= $pageinfo[1]->fullname.'/'.get_string('view', 'local_smart_klass') . '/';
        }
    }
    if (isset($pageinfo[2]->name)) {
        $trackurl .= $pageinfo[2]->modname.'/'.$pageinfo[2]->name . '/';
    }
    if (!empty($USER->id)) {
        $trackurl .= $USER->email . '/';
        
        $roles	= get_user_roles($PAGE->context, $USER->id, true);
        foreach($roles as $role){
            $trackurl .= $role->name . '|';
        }
        $trackurl .= '/';
    }
    $trackurl .= "'";
    return $trackurl;
}

function local_smart_klass_set_oauthserver ($endpoint) {
    set_config('oauth_server', $endpoint, 'local_smart_klass');
}
 
function local_smart_klass_insert_analytics_tracking() {
    global $CFG, $USER;

    $siteurl = get_config('local_smart_klass', 'tracker_url');
    $siteid = get_config('local_smart_klass', 'tracker_id');
    $userid = ( empty($USER->email) ) ? 'void@klassdata.com' : $USER->email;
    
	if (!empty($siteurl) && !empty($siteid)) {
			$CFG->additionalhtmlfooter .= "
<script type='text/javascript'>
    var _paq = _paq || [];
    _paq.push(['setDocumentTitle', ".local_smart_klass_trackurl()."]);
    _paq.push(['setUserId', '" . $userid . "']);
    _paq.push(['trackPageView']);
    _paq.push(['enableLinkTracking']);
    (function() {
      var u='//".$siteurl."/';
      _paq.push(['setTrackerUrl', u+'piwik.php']);
      _paq.push(['setSiteId', ".$siteid."]);
      var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
    g.type='text/javascript'; g.async=true; g.defer=true; g.src=u+'piwik.js'; s.parentNode.insertBefore(g,s);
    })();
</script>
<noscript><p><img src=".$siteurl."/piwik.php?idsite=".$siteid." style='border:0;' alt='' /></p></noscript>";
		
	}
}

local_smart_klass_insert_analytics_tracking();
