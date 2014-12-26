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
 * Smart Klass xAPI Settings
 *
 * @package    local_smart_klass
 * @copyright  KlassData <kttp://www.klassdata.com>
 * @author     Oscar Ruesga <oscar@klassdata.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig) {
    
    $ADMIN->add('root', new admin_category('smart_klass', get_string('pluginname', 'local_smart_klass')));
    $ADMIN->add('smart_klass', new admin_externalpage('smart_klasssettings', get_string('settings'),
            $CFG->wwwroot.'/admin/settings.php?section=local_smart_klass', 'local/smart_klass:manage'));
    $ADMIN->add('smart_klass', new admin_externalpage('smart_klasscontrolpanel', get_string('controlpanel', 'local_smart_klass'),
            $CFG->wwwroot.'/local/smart_klass/view.php', 'local/smart_klass:manage'));
    
   
    $settings = new admin_settingpage('local_smart_klass', get_string('pluginname', 'local_smart_klass'));
    $ADMIN->add('localplugins', $settings);

    $settings->add(new admin_setting_configcheckbox('local_smart_klass/activate', get_string('activate', 'local_smart_klass'), get_string('activatedescription', 'local_smart_klass'), 1));
    
    $settings->add(new admin_setting_configcheckbox('local_smart_klass/save_log', get_string('save_log', 'local_smart_klass'), get_string('savelogdescription', 'local_smart_klass'), 1));
    
    $settings->add(new admin_setting_configcheckbox('local_smart_klass/savelog_ok_statement', get_string('savelog_ok_statement', 'local_smart_klass'), get_string('savelog_ok_statement_description', 'local_smart_klass'), 0));
    
    $settings->add(new admin_setting_configtext('local_smart_klass/max_block_cicles', get_string('max_block_cicles', 'local_smart_klass'),
                       get_string('max_block_ciclesdescription', 'local_smart_klass'), 5, PARAM_INT));
   
    $settings->add(new admin_setting_configcheckbox('local_smart_klass/check_statement', get_string('check_statement', 'local_smart_klass'), get_string('checkstatementdescription', 'local_smart_klass'), 0));
    
    $settings->add(new admin_setting_configcheckbox('local_smart_klass/activate_student_dashboard', get_string('activate_student_dashboard', 'local_smart_klass'), get_string('activate_student_dashboard_description', 'local_smart_klass'), 1));
    $settings->add(new admin_setting_configcheckbox('local_smart_klass/activate_teacher_dashboard', get_string('activate_teacher_dashboard', 'local_smart_klass'), get_string('activate_teacher_dashboard_description', 'local_smart_klass'), 1));
    $settings->add(new admin_setting_configcheckbox('local_smart_klass/activate_institution_dashboard', get_string('activate_institution_dashboard', 'local_smart_klass'), get_string('activate_institution_dashboard_description', 'local_smart_klass'), 1));
}