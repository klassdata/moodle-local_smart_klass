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
 * Smart Klass Dashboard
 *
 * @package    local_smart_klass
 * @copyright  KlassData <kttp://www.klassdata.com>
 * @author     Oscar Ruesga <oscar@klassdata.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once (dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(__FILE__).'/locallib.php');

$auth_code      = optional_param('code', null, PARAM_RAW);
$auth_error     = optional_param('error', null, PARAM_RAW);
$dashboard_role = optional_param('dt', null, PARAM_INT);
$token     = optional_param('token', null, PARAM_RAW);
$refresh_token     = optional_param('refresh_token', null, PARAM_RAW);
$payload = optional_param('p', null, PARAM_RAW);

if ( !is_null($dashboard_role) ) {
    $SESSION->dt = $dashboard_role;
}
$redirect_uri = implode('', array( $CFG->wwwroot,
                                   '/local/smart_klass/dashboard.php',
                            ));

if ( !is_null($auth_error) ) print_error($auth_error, 'local_smart_klass');  

//Register LMS payload
if (!is_null($payload)){
    $payload = base64_decode($payload);
    $payload = json_decode($payload);
    set_config('oauth_access_token', $payload->access_token, 'local_smart_klass');
    set_config('oauth_refresh_token', $payload->refresh_token, 'local_smart_klass');
    set_config('oauth_client_id', $payload->client_id, 'local_smart_klass');
    set_config('oauth_client_secret', $payload->client_secret, 'local_smart_klass');
    $url = new moodle_url ('/local/smart_klass/view.php');
    echo $OUTPUT->header();
    $PAGE->requires->js_init_call('M.local_smart_klass.refreshContent', array((string)$url), true );
    echo $OUTPUT->footer();
}

if ( !is_null($auth_code) ){
       
    $client_id = get_config('local_smart_klass', 'oauth_client_id');
    $client_secret = get_config('local_smart_klass', 'oauth_client_secret');

    $server = get_config('local_smart_klass', 'oauth_server');
    $server .= '/dashboard/access_token';
    
    $curl = new SmartKlass\xAPI\Curl;
    $output = $curl->post( $server, array(
                            'code' => $auth_code,
                            'client_id' => $client_id,
                            'client_secret' => $client_secret,
                            'redirect_uri' => $redirect_uri,
                            'grant_type' => 'authorization_code',
                            'user_id' => $CFG->wwwroot . '/' . $USER->id,
                            'user_name' => $USER->firstname,
                            'user_lastname' => $USER->lastname,
                            'user_email' => $USER->email,
                            'lang' => $USER->lang
                        ));

    
    $strheading = 'SmartKlass';
    $PAGE->set_pagelayout('popup');
    $PAGE->set_url(new moodle_url('/local/smart_klass/dashboard.php'));
    $PAGE->set_title( $strheading );
    $PAGE->requires->js('/local/smart_klass/javascript/iframeResizer.min.js', true);
    $PAGE->navbar->add($strheading);
    echo $OUTPUT->header();
    
    
    $output_json = json_decode($output);
    if ( is_object ($output_json) == true ) {
        $errors = $output_json->error_description;
        print_error($errors);
        echo $OUTPUT->footer();
        die;
    }    

    echo $OUTPUT->box('', 'generalbox', 'smartklass');
    $PAGE->requires->js_init_call('M.local_smart_klass.createContent', array( $output, 'smartklass'), true );
    echo $OUTPUT->footer();
    die();
}

if ( !is_null($token) && !is_null($refresh_token) ){
    if ( is_null($SESSION->dt) ) {
        print_error( get_string('no_role', 'local_smart_klass') );
    }
    
    if ( is_null($SESSION->cid) ) {
        print_error( get_string('no_context', 'local_smart_klass') );
    }

    $item = local_smart_klass_save_access_token ($token, $refresh_token, $USER->email, $SESSION->dt, $USER->id);
    
    $url = new moodle_url ('/local/smart_klass/dashboard.php', array('cid' => $SESSION->cid, 'dt'=> $SESSION->dt) );
    echo $OUTPUT->header();
    $PAGE->set_pagelayout('popup');
    echo $OUTPUT->box_start();
    $PAGE->requires->js_init_call('M.local_smart_klass.refreshContent', array($url->out(false)), true );
    echo $OUTPUT->footer();
}

$contextid      = required_param('cid', PARAM_INT);

if ( !is_null($dashboard_role) ) {
    $SESSION->cid = $contextid;
}

list($context, $course, $cm) = get_context_info_array($contextid);


$dashboard_roles = local_smart_klass_dashboard_roles ($USER->id, $context);

if($dashboard_roles->institution){
	require_login();
} else {
    require_login($course, true);
}

switch ($dashboard_role) {
    case SMART_KLASS_DASHBOARD_STUDENT:
        $strheading = get_string('studentdashboard', 'local_smart_klass');
        if (!$dashboard_roles->student) print_error('nostudentrole', 'local_smart_klass');
        if (get_config('local_smart_klass', 'activate_student_dashboard') != '1') print_error('student_dashboard_noactive', 'local_smart_klass');
        $role = 'student';
        break;
    
    case SMART_KLASS_DASHBOARD_TEACHER:
        $strheading = get_string('teacherdashboard', 'local_smart_klass');
        if (!$dashboard_roles->teacher) print_error('noteacherrole', 'local_smart_klass');
        if (get_config('local_smart_klass', 'activate_teacher_dashboard') != '1') print_error('teacher_dashboard_noactive', 'local_smart_klass');
        $role = 'teacher';
        break;
    
    case SMART_KLASS_DASHBOARD_INSTITUTION:
        $strheading = get_string('institutiondashboard', 'local_smart_klass');
        if (!$dashboard_roles->institution) print_error('noinstitutionrole', 'local_smart_klass');
        if (get_config('local_smart_klass', 'activate_institution_dashboard') != '1') print_error('institution_dashboard_noactive', 'local_smart_klass');        
        $role = 'institution';
        break;
}


$PAGE->set_pagelayout('standard');
$PAGE->set_url(new moodle_url('/local/smart_klass/dashboard.php'));
$PAGE->set_title( $strheading );
$PAGE->requires->js('/local/smart_klass/javascript/iframeResizer.min.js', true);
$PAGE->navbar->add($strheading);
$PAGE->set_context($context);

echo $OUTPUT->header();
echo $OUTPUT->box_start();

$oauth_obj = local_smart_klass_get_oauth_accesstoken ($USER->id, $dashboard_role);

if ( !empty($oauth_obj) ){
	
    require_once(dirname(__FILE__).'/classes/xAPI/Providers/Credentials.php');
    $provider = SmartKlass\xAPI\Credentials::getProvider();
    $credentials = $provider->getCredentials();
    
    if ( is_null($credentials->dashboard_endpoint) ) {
        set_config('credential_cicle', 0, 'local_smart_klass');
        print_error( get_string('no_dashboard_endpoint', 'local_smart_klass') );
        echo $OUTPUT->footer();
        die;
    }
    
    
        
    $url = $credentials->dashboard_endpoint . '/oauth/authorize/' . $COURSE->id . '/' . $oauth_obj->access_token;
  
    echo $OUTPUT->box('', 'generalbox', 'smartklass');
    $PAGE->requires->js_init_call('M.local_smart_klass.loadContent', array( $url, 'smartklass'), true );
    
} else {
    $server = get_config('local_smart_klass', 'oauth_server');
    $access_token = get_config('local_smart_klass', 'oauth_access_token');
    $client_id = get_config('local_smart_klass', 'oauth_client_id');

    if ( $access_token == false || $client_id == false || $server == false) {
        $url = new moodle_url ('/local/smart_klass/register.php');
        $PAGE->set_pagelayout('popup');
        $PAGE->requires->js_init_call('M.local_smart_klass.refreshContent', array((string)$url), true );
        echo $OUTPUT->footer();
        
    }
    
    $server .= '/dashboard/authorize'; 

    require_once(dirname(__FILE__) . '/classes/xAPI/Helpers/Curl.php');
    $curl = new Curl;
    $output = $curl->get( $server, array(   'client_id' => $client_id,
                                            'redirect_uri' => $redirect_uri, 
                                            'grant_type'=>'auth_code', 
                                            'response_type'=>'code', 
                                            'id'=> $CFG->wwwroot . '/' . $USER->id,
                                            'scope' => $role
                                             ));

    $output_json = json_decode($output);
    if ( empty($output) || is_object ($output_json) == true ) {
        $errors = (empty($output)) ? get_string('no_oauth_comunication', 'local_smart_klass') : $output_json->error_description;
        print_error($errors);
        echo $OUTPUT->footer();
        die;
    }    

    echo $OUTPUT->box('', 'generalbox', 'smartklass');
    $PAGE->requires->js_init_call('M.local_smart_klass.createContent', array( $output, 'smartklass'), true );   
}
echo $OUTPUT->box_end();
echo $OUTPUT->footer();