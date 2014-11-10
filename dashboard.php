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



$contextid      = required_param('cid', PARAM_INT);
$contextid		= $COURSE->id;
$dashboard_role = required_param('dt', PARAM_INT);


list($context, $course, $cm) = get_context_info_array($contextid);


$dashboard_roles = local_smart_klass_dashboard_roles ($USER->id);

if($dashboard_roles->institution){
	require_login();
}else{
    require_login($course, true);
}


switch ($dashboard_role) {
    case SMART_KLASS_DASHBOARD_STUDENT:
        $strheading = get_string('studentdashboard', 'local_smart_klass');
        if ($dashboard_roles->student) print_error('nostudentrole', 'local_smart_klass');
        if (get_config('local_smart_klass', 'activate_student_dashboard') != '1') print_error('student_dashboard_noactive', 'local_smart_klass');
        break;
    
    case SMART_KLASS_DASHBOARD_TEACHER:
        $strheading = get_string('teacherdashboard', 'local_smart_klass');
        if ($dashboard_roles->teacher) print_error('noteacherrole', 'local_smart_klass');
        if (get_config('local_smart_klass', 'activate_teacher_dashboard') != '1') print_error('teacher_dashboard_noactive', 'local_smart_klass');
        break;
    
    case SMART_KLASS_DASHBOARD_INSTITUTION:
        $strheading = get_string('institutiondashboard', 'local_smart_klass');
        if ($dashboard_roles->institution) print_error('noinstitutionrole', 'local_smart_klass');
        if (get_config('local_smart_klass', 'activate_institution_dashboard') != '1') print_error('institution_dashboard_noactive', 'local_smart_klass');
        break;
}


$PAGE->set_pagelayout('standard');
$PAGE->set_url(new moodle_url('/local/smart_klass/dashboard.php'));
$PAGE->set_title( $strheading );
$PAGE->navbar->add($strheading);
echo $OUTPUT->header();
echo $OUTPUT->box_start();

/*
//VARIABLES DE GESTIÓN DEL DASHBOARD
echo 'USER_ID: ' . $USER->id . '<br>';
echo 'USER_EMAIL: ' . $USER->email . '<br>';
echo 'ROLE_SOLICITADO: ' . $dashboard_role . '<br>';
echo 'DASHBOARD A PRESENTAR: ' . $strheading . '<br>';
echo 'ROLES SOPORTADOS POR EL USUARIO EN EL CURSO: ';
if($dashboard_roles->student)  echo 'id: ' . SMART_KLASS_DASHBOARD_STUDENT . 'STUDENT, ';
if($dashboard_roles->teacher)  echo 'id: ' . SMART_KLASS_DASHBOARD_TEACHER . 'TEACHER, ';
if($dashboard_roles->institution)  echo 'id: ' . SMART_KLASS_DASHBOARD_INSTITUTION . 'INSTITUTION';
*/
$oauth_obj = local_smart_klass_get_oauth_accesstoken ($USER->id, $dashboard_role);

$u_course = $COURSE->id;
$ident_course =  $CFG->wwwroot . '/course/' . $u_course;

if ( !empty($oauth_obj) ){
	
    $options = array(); 
    $options['src'] = SMART_KLASS_OAUTHSERVER_URL . '/conexion_oauth/check_user/'.urlencode($USER->email).'/'.$oauth_obj->access_token;
    $options['width'] = '100%';
    $options['height'] = '608px';
    $options['style'] = 'border:none';
    
	

//	echo html_writer::empty_tag('iframe', $options);
	echo "<iframe src='".SMART_KLASS_DASHBOARD_URL."/conexion_oauth/check_user/".urlencode($USER->email)."/".$oauth_obj->access_token."'   width='100%' height='608px' style='border:none' /></iframe>";	



/*    //ACCESS_TOKEN OK, SEND  ACCESS TOKEN TO DASHBOARD
    if ( $dashboard = local_smart_klass_get_dashboard($oauth_obj->access_token) ){
		echo 'sdff';
		print_r($USER);
		   //
    } else {
        //TODO 5 Posiziona: Throw Smart Klass Dashboard Creation Error Exception
    }
	*/
} else {


    //ACCESS_TOKEN KO,MANAGE OAUTH LOGIN/REGISTER

    //TODO 6 Posiziona: Manage oauth_login with user/password with oauth server
    //1. Show login form / register url
    //2. Validate login form with oauth server
    //2.1. If OK create 
    //2.2 If KO show oauth server error message, redirect (1)
    //3. call $dashboard = local_smart_klass_get_dashboard($accesstoken)
    //4. create $dashboard

    if (empty($_GET['error']) && empty($_GET['ok'])) {    

	  


       // $PAGE->requires->js_init_call('M.local_smart_klass.save_access_token', array( $code, $refresh, $email, $rol, $user_id), true );
		
        $options = array(); 
        $options['src'] = SMART_KLASS_DASHBOARD_URL . '/register/reg/'.urlencode($USER->email).'/'.$dashboard_role.'/'.$USER->id.'/'.$USER->sesskey.'/'.($ident_course);
        $options['width'] = '100%';
        $options['height'] = '608px';
        $options['style'] = 'border:none';
      //  echo html_writer::tag('iframe', $options);
		
		//codificamos la url para poder pasarla por el iframe
		$ident_course  = (str_replace('http://','55hp5',$ident_course));
		$ident_course  = (str_replace('/','8br8',$ident_course));		


	    echo "<iframe src='".SMART_KLASS_DASHBOARD_URL."/register/reg/".urlencode($USER->email)."/".$dashboard_role."/".$USER->id."/".$USER->sesskey."/".$ident_course."'   width='100%' height='608px' style='border:none' /></iframe>";
		
    }else if(($_GET['error']) ){
         echo html_writer::empty_tag('br');
         echo html_writer::empty_tag('br');
         echo html_writer::tag('label', get_string('noaccess','local_smart_klass'));
    
    }
  
   
   
   
}

//TODO by POSIZIONA: Draw dashboard

echo $OUTPUT->box_end();
echo $OUTPUT->footer();