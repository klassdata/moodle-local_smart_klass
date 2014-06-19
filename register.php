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
 * Register Execution Klap Server Connection
 *
 * @package    local_klap
 * @copyright  Klap <kttp://www.klaptek.com>
 * @author     Oscar <oscar@klaptek.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once (dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(__FILE__).'/locallib.php');

require_once(dirname(__FILE__).'/register_form.php');

require_capability('local/klap:manage', get_context_instance(CONTEXT_SYSTEM));
require_login(); 

$strheading = get_string('configure_access','local_klap');
$PAGE->set_pagelayout('standard');
$PAGE->set_url(new moodle_url('/local/klap/register.php'));
$PAGE->set_title( $strheading );
$PAGE->navbar->add($strheading);
echo $OUTPUT->header();
echo $OUTPUT->box_start();

$url = new moodle_url( get_config('local_klap', 'endpoint') . '../../register/');
echo $OUTPUT->action_link($url, get_string('register', 'local_klap'), new popup_action('click', $url), array('style'=>'text-align:center;'));
echo $OUTPUT->box_end();




echo $OUTPUT->box_start();
$mform = new local_klap_lrs_login_form();

if ($mform->is_cancelled()) {
    redirect( new moodle_url('/local/klap/register.php'));
} else if ($mform->is_submitted() && $mform->is_validated() ) {
    $data = $mform->get_data();
    
    set_config('username', $data->username,'local_klap');
    set_config('password', $data->password,'local_klap');

    redirect("$CFG->wwwroot/");
}
$mform->display();
echo $OUTPUT->box_end();

echo $OUTPUT->footer();