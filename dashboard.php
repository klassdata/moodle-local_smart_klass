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
// defined('MOODLE_INTERNAL') || die();
require_once (dirname(dirname(dirname(__FILE__))).'/config.php');

// require_capability('local/newplugin:manage', context_system::instance());
require_login();


function encrypt_data($plaintext) {
    $_RIJNDAEL_KEY_ = "uUxJIpSKMbOQQdtm6Y4rPEXeE9TAKUns";
    $_RIJNDAEL_IV_  = "PiToVoRjwlg8UwxUxQKI4w==";
    $ciphertext = $plaintext;
    $content = mcrypt_decrypt(
        MCRYPT_RIJNDAEL_128,
        $_RIJNDAEL_KEY_,
        base64_decode($ciphertext),
        MCRYPT_MODE_ECB,
        $_RIJNDAEL_IV_
    );
    return base64_encode($plaintext);
}

$contextid      = required_param('cid', PARAM_INT);
/*
if ( !is_null($dashboard_role) ) {
    $SESSION->cid = $contextid;
}
*/
$dashboard_role = optional_param('dt', null, PARAM_INT);



list($context, $course, $cm) = get_context_info_array($contextid);


$strheading = get_string('configure_access','local_smart_klass');
$PAGE->set_pagelayout('standard');
//$PAGE->set_context(context_system::instance());
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/smart_klass/dashboard.php'));
$PAGE->set_title( $strheading );
$PAGE->navbar->add($strheading);


echo $OUTPUT->header();
echo $OUTPUT->box_start();

//
$login_email = $USER->email;
if ($dashboard_role == 3) {
    $admin_user = get_admin();
    $login_email = $admin_user->email;
}


$dataurl = "email=".$login_email."&locale=".$USER->lang."&role=".$dashboard_role;

$dataurl = encrypt_data($dataurl);

$salt = base64_encode("uUxJIpSKMbOQQdtm6Y4rPEXeE9TAKUns");
$dataurl = base64_encode($dataurl."".$salt);

$url = get_config('local_smart_klass', 'dashboard_endpoint');
$url .= "pluginlogin?data=".$dataurl;


echo $OUTPUT->box('', 'generalbox', 'smartklass');
$PAGE->requires->js_init_call('M.local_smart_klass.loadContent', array( $url, 'smartklass'), true );
echo $OUTPUT->box_end();
echo $OUTPUT->footer();
die();
