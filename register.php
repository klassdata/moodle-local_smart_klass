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
 * Register Execution Smart Klass Server Connection
 *
 * @package    local_smart_klass
 * @copyright  KlassData <kttp://www.klassdata.com>
 * @author     Oscar Ruesga <oscar@klassdata.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once (dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(__FILE__).'/locallib.php');

require_once(dirname(__FILE__).'/classes/xAPI/Helpers/Curl.php');

require_capability('local/smart_klass:manage', get_context_instance(CONTEXT_SYSTEM));
require_login(); 

$payload = optional_param('p', null, PARAM_RAW);


$strheading = get_string('configure_access','local_smart_klass');
$PAGE->set_pagelayout('standard');
$PAGE->set_url(new moodle_url('/local/smart_klass/register.php'));
$PAGE->set_title( $strheading );
$PAGE->navbar->add($strheading);
$PAGE->requires->js('/local/smart_klass/javascript/iframeResizer.min.js', true);

if (!is_null($payload)){
    $payload = base64_decode($payload);
    $payload = json_decode($payload);
    set_config('oauth_access_token', $payload->access_token, 'local_smart_klass');
    set_config('oauth_refresh_token', $payload->refresh_token, 'local_smart_klass');
    set_config('oauth_client_id', $payload->client_id, 'local_smart_klass');
    set_config('oauth_client_secret', $payload->client_secret, 'local_smart_klass');
}

$access_token = get_config('local_smart_klass', 'oauth_access_token');
$client_id = get_config('local_smart_klass', 'oauth_client_id');
$client_secret = get_config('local_smart_klass', 'oauth_client_secret');




$server = get_config('local_smart_klass', 'oauth_server');
$redirect_uri = implode('', array(
                                isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https' : 'http',
                                '://',
                                $_SERVER['SERVER_NAME'],
                                isset($_SERVER['SERVER_PORT']) ? ':' . $_SERVER['SERVER_PORT'] : '',
                                $_SERVER['SCRIPT_NAME'],
                            ));
echo $OUTPUT->header();

$curl = new Curl;



if ( $access_token == false || $client_id == false || $client_secret == false) {

   $server .= '/oauth/authorize'; 
    
    $output = $curl->get( $server, array('endpoint' => $CFG->wwwroot,'redirect_uri' => $redirect_uri));
    
    $output_json = json_decode($output);
    if ( is_object ($output_json) == true ) {
        $errors = $output_json->errors;
        $errors = implode('<br>', $errors);
        print_error($errors);
        echo $OUTPUT->footer();
        die;
    }    

    echo $OUTPUT->box('', 'generalbox', 'smartklass');
    $PAGE->requires->js_init_call('M.local_smart_klass.createContent', array( $output, 'smartklass'), true );
    echo $OUTPUT->footer();
    
} else {
    /*$server .= '/access_token';
    $output = $curl->get( $server, array(
                            'token' => $access_token,
                            '$client_id' => $access_token,
                            '$secret' => $access_token,
                            'redirect_uri' => $redirect_uri,
                        ));
    */
    $url = new moodle_url ('/local/smart_klass/view.php');
    $PAGE->requires->js_init_call('M.local_smart_klass.refreshContent', [(string)$url], true );
    echo $OUTPUT->footer();
    
     
    
}
