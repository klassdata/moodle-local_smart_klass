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
 * Klap xAPI Settings
 *
 * @package    local_klap
 * @copyright  Klap <kttp://www.klaptek.com>
 * @author     Oscar <oscar@klaptek.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig) {
    
    //Creo menú en el Bloque de administración para el plugin
    $ADMIN->add('root', new admin_category('klap', get_string('pluginname', 'local_klap')));
    $ADMIN->add('klap', new admin_externalpage('klapsettings', get_string('settings'),
            $CFG->wwwroot.'/admin/settings.php?section=local_klap', 'local/klap:manage'));
    $ADMIN->add('klap', new admin_externalpage('klapcontrolpanel', get_string('controlpanel', 'local_klap'),
            $CFG->wwwroot.'/local/klap/view.php', 'local/klap:manage'));
    
    //Agrego página de configuración para acceder desde plugins locales
    $settings = new admin_settingpage('local_klap', get_string('pluginname', 'local_klap'));
    $ADMIN->add('localplugins', $settings);
    
    //Agrego campos de configuración del Plugin
    $settings->add(new admin_setting_configcheckbox('local_klap/activate', get_string('activate', 'local_klap'), get_string('activatedescription', 'local_klap'), 0));
    
    $settings->add(new admin_setting_configcheckbox('local_klap/save_log', get_string('save_log', 'local_klap'), get_string('savelogdescription', 'local_klap'), 0));
    
    $settings->add(new admin_setting_configcheckbox('local_klap/savelog_ok_statement', get_string('savelog_ok_statement', 'local_klap'), get_string('savelog_ok_statement_description', 'local_klap'), 0));
    
    $options = array('http://l-miner.klaptek.com/data/xAPI/' => get_string('defaultserver', 'local_klap'),
        'https://l-miner.klaptek.com/data/xAPI/' => get_string('secureserver', 'local_klap'), 'http://l-miner.klaptek.local/data/xAPI/'=>get_string('localserver', 'local_klap'));
    $settings->add(new admin_setting_configselect('local_klap/endpoint', get_string('endpoint',
        'local_klap'), get_string('endpointdescription', 'local_klap'), 'http://l-miner.klaptek.com/data/xAPI/', $options));
    
    $options = array('basic' => get_string('basic', 'local_klap')/*, 'oauth' => get_string('oauth', 'local_klap')*/);
    $settings->add(new admin_setting_configselect('local_klap/authtype', get_string('authtype',
        'local_klap'), get_string('authtypedescription', 'local_klap'), 'basic', $options));
    
    $settings->add(new admin_setting_configtext('local_klap/username', get_string('username', 'local_klap'),
                       get_string('usernamedescription', 'local_klap'), '', PARAM_RAW));
    
    $settings->add(new admin_setting_configtext('local_klap/password', get_string('password', 'local_klap'),
                       get_string('passworddescription', 'local_klap'), '', PARAM_RAW));
    
    
    $settings->add(new admin_setting_configcheckbox('local_klap/check_statement', get_string('check_statement', 'local_klap'), get_string('checkstatementdescription', 'local_klap'), 0));

}