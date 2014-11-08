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
 * Strings for component 'local_smart_klass', language 'en'
 *
 * @package    local_smart_klass
 * @copyright  KlassData <kttp://www.klassdata.com>
 * @author     Oscar Ruesga <oscar@klassdata.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Smart Klass';

$string['controlpanel'] = 'Control Panel';
$string['fullharvester'] = 'Execute data harvest';
$string['activate'] = 'Enable';
$string['activatedescription'] = 'Enables Moodle data tracing to Smart Klass Server';
$string['endpoint'] = 'Smart Klass Server';
$string['endpointdescription'] = 'Select the Smart Klass server you wish to connect';
$string['defaultserver'] = 'Default Server';
$string['secureserver'] = 'Secure Server';
$string['localserver'] = 'Local Server';
$string['authtype'] = 'Authentication Type';
$string['authtypedescription'] = 'Select the type of authentication to the platform Smart Klass';
$string['basic'] = 'Basic';
$string['oauth'] = 'oAUTH';
$string['username'] = 'Username';
$string['usernamedescription'] = 'For basic authentication, enter the username to use the platform Smart Klass';
$string['password'] = 'Password';
$string['passworddescription'] = 'For basic authentication, enter the password to use the platform Smart Klass';
$string['save_log'] = 'Save Log';
$string['savelogdescription'] = 'Store all executions of harvest task in Moodle filesystem';
$string['savelog_ok_statement'] = 'Save in log Ok statement result';
$string['savelog_ok_statement_description'] = 'If checked store in log either OK and KO operations statements otherwise only store in log KO operations statements';
$string['check_statement'] = 'Check statements';
$string['checkstatementdescription'] = 'Check the validity of statements prior sent to Smart Klass Server';
$string['collector_status'] = 'Collector Status';
$string['collector_name'] = 'Collector Name';
$string['active'] = 'Active';
$string['last_registry'] = 'Last Record (harvested/Max)';
$string['last_exectime'] = 'Last harvested timestamp';
$string['execution_log'] = 'Execution Log';
$string['dashboard'] = 'Dashboard';
$string['studentdashboard'] = 'Dashboard: Student';
$string['teacherdashboard'] = 'Dashboard: Teacher';
$string['institutiondashboard'] = 'Dashboard: Institution';
$string['nostudentrole'] = 'You don´t have de correct rights to view the Student Dashboard';
$string['noteacherrole'] = 'You don´t have de correct rights to view the Teacher Dashboard';
$string['noinstitutionrole'] = 'You don´t have de correct rights to view the Institution Dashboard';
$string['configure_access'] = 'Configure access to Smart Klass Dashboard';
$string['register'] = 'Register new user in Smart Klass';
$string['noaccess'] = 'Access denied for user';
$string['activate_student_dashboard'] = 'Activate student dashboard';
$string['activate_teacher_dashboard'] = 'Activate teacher dashboard';
$string['activate_institution_dashboard'] = 'Activate institution dashboard';
$string['activate_student_dashboard_description'] = 'Activate access to the student dashboard in courses';
$string['activate_teacher_dashboard_description'] = 'Activate access to the teacher dashboard in courses';
$string['activate_institution_dashboard_description'] = 'Activate access to the institution dashboard in site';
$string['student_dashboard_noactive'] = 'Student dashboard is inactive. Check with the administrator of the platform';
$string['teacher_dashboard_noactive'] = 'Teacher dashboard is inactive. Check with the administrator of the platform';
$string['institution_dashboard_noactive'] = 'Institution dashboard is inactive. Check with the administrator of the platform';
$string['invalid_dataprovider'] = 'Invalid DataProvider type given.';
$string['harvester_service_unavailable'] = 'Harvester service not available';
$string['harvester_service_instance_running'] = 'Harvester service instance is currently running. There can be only one active instance';

$string['user_no_complete_course'] = 'User {$a->user} do not completed the course {$a->course} so the record will not be sent';
$string['user_no_enrol_course'] = 'User {$a->user} do not enrol the course {$a->course} so the record will not be sent';
$string['user_no_init_course'] = 'User {$a->user} do not init the course {$a->course} so the record will not be sent';
$string['user_no_completed_activity'] = 'User {$a->user} do not completed the activity {$a->activity} so the record will not be sent';
$string['error_lo_verb_from_log'] = 'Unable to generate a verb for the module {$a->module} and the action {$a->action}';
$string['no_record_to_update'] = 'No records to update';
$string['statement_send_ok'] =  'The statement will be send correctly';
$string['ok'] =  'OK';
$string['ko'] =  'KO';
$tring['crontask'] = 'Smart Class cron task';
$string['view'] = 'View';
$string['edit'] = 'Edit';