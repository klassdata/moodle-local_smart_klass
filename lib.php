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
 * @package    local_klap
 * @copyright  Klap <kttp://www.klaptek.com>
 * @author     Oscar <oscar@klaptek.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

define('KLAP_ACTION_DEFAULT',        'd');
define('KLAP_ACTION_HARVERTS',       'h');
define('KLAP_ACTION_EDIT',           'e');
define('KLAP_ACTION_ACTIVATE',       'a');

/**
 * Function to be run periodically according to the moodle cron
 * Prepare all statemenst and send it to an LRS
 * throw the xAPI services
 * @return void
 */

function local_klap_cron() {  
    local_klap_harvest();
}


function local_klap_harvest( $collector=array() ) {
    if ( get_config('local_klap', 'activate') != 1){
        echo 'Servicio de recolección de datos inactivo';
        return; 
    }
    
    if (get_config('local_klap', 'croninprogress') == true){
        echo 'Instancia del servicio de recolección de datos en curso. Solamente puede existir una instancia activa';
        return;
    }
    
    global $CFG, $USER, $DB;
    
    set_config('croninprogress', true, 'local_klap');
    
    $out = array();
    
    //Autoload library class
    require_once (dirname(__FILE__) . '/xAPI/Autoloader.php');
    Klap\xAPI\Autoloader::register();
    
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
        $collectors = $DB->get_records_select('local_klap', 'active=?' . $custom_collector, array(1));

        foreach ($collectors as $item){
            $trace =  '...... Recolectando ' . $item->name . ' -- Inicio: ' . date('r')  . ' / ';      
            $collector_class = 'Klap\\xAPI\\' . $item->name . 'Collector';
            $collector = new $collector_class;
            $trace .= 'Fin: ' . date('r');   
            $out[] = $trace;
        }

        $objlog->logfile = Klap\xAPI\Logger::save_log();
        $objlog->finish = time();
        $objlog->result = 1;
        $out[] = 'Enviados xAPI statements............... -- ' . date('r', $objlog->finish);
        $url = Klap\xAPI\Logger::get_url($objlog->logfile);
        $out[] = html_writer::link($url, $objlog->logfile);
        
        set_config('croninprogress', false, 'local_klap');
        set_config('lastcron', $objlog->finish, 'local_klap');
        
    } catch (Exception $e){
        $objlog->error = json_encode($e);
        set_config('croninprogress', false, 'local_klap');
    }
    
    $collectors = $DB->get_records_select('local_klap', 'active=?' . $custom_collector, array(1));
    $objlog->collectors = json_encode($collectors);
    $DB->insert_record('local_klap_log', $objlog);
    $br = html_writer::empty_tag('br');
    echo implode($br, $out);
}

function local_klap_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options=array()) {
    // Check the contextlevel is as expected - if your plugin is a block, this becomes CONTEXT_BLOCK, etc.
    if ($context->contextlevel != CONTEXT_SYSTEM) {
        return false; 
    }
 
    // Make sure the filearea is one of those used by the plugin.
    if ($filearea !== 'local_klap' ) {
        return false;
    }
 
    // Make sure the user is logged in and has access to the module (plugins that are not course modules should leave out the 'cm' part).
    require_login();
 
    // Check the relevant capabilities - these may vary depending on the filearea being accessed.
    if (!has_capability('local/klap:manage', $context)) {
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
    
    set_config('croninprogress', false, 'local_klap');
    
    // Retrieve the file from the Files API.
    $fs = get_file_storage();
    $file = $fs->get_file($context->id, 'local_klap', $filearea, $itemid, $filepath, $filename);
    if (!$file) {
        return false; // The file does not exist.
    }

    send_stored_file($file, 'default', 0, true, $options);
}

function local_klap_activate_harvester( $collectorid ) {
    global $DB;
    $collector= $DB->get_record('local_klap', array('id'=>$collectorid), 'id, active' );
    $collector->active = ($collector->active == 1) ? 0 : 1;
    $DB->update_record('local_klap', $collector);
}

function local_klap_get_harvesters () {
    global $DB;
    
    $harvesters = $DB->get_records('local_klap', null, null, 'name');
    
    $collectors_class = scandir(dirname(__FILE__).'/xAPI/Collectors');
   
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
                require_once (dirname(__FILE__) . '/xAPI/Autoloader.php');
                Klap\xAPI\Autoloader::register();
                
                $class_file = dirname(__FILE__) . "/xAPI/Collectors/{$item}Collector.php";               
                if ( !file_exists($class_file) ) continue;
               // if (!class_exists("Klap\\xAPI\\$item") ) continue;
                $class = new \ReflectionClass("Klap\\xAPI\\{$item}Collector");
                $parentclass = $class->getParentClass();
                if( $parentclass->name != 'Klap\\xAPI\\Collector' ||
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
                $id = $DB->insert_record ('local_klap', $o);
            } catch (Exception $ex) {} 
         } 
        unset($harvesters[$item]);
        
    }
    if (count($harvesters) > 0) {
        foreach ($harvesters as $key=>$value) {
            $o = $DB->get_record('local_klap', array('name'=>$key), 'id, deleted' );
            $o->deleted = '1';
            $status = $DB->update_record_raw('local_klap', $o);
        }
    }
    $harvesters = $DB->get_records('local_klap', array('deleted'=>'0'));
    return $harvesters;
    
}
