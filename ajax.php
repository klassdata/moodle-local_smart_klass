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
 * Ajax hub
 *
 * @package    local_smart_klass
 * @copyright  Klassdata <kttp://www.klassdata.com>
 * @author     Oscar Ruesga <oscar@klassdata.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


if (!defined('AJAX_SCRIPT')) {
    define('AJAX_SCRIPT', true);
}

require_once (dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(__FILE__).'/locallib.php');

$result = new stdClass();

$criteriaid = required_param('action', PARAM_RAW);
$data = required_param('data', PARAM_RAW);

$data = json_decode($data);



switch ($action) {
    case 'save_access_token':
        $result->success = local_smart_klass_save_access_token($data->code, $data->refresh, $data->email, $data->rol, $data->user_id);
        $result->data = null;
        break;
        
}


echo json_encode ($result);

?>
