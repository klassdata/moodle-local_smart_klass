<?php
namespace SmartKlass\xAPI;

/**
 * Logger Class. Store Log File in Moodle Filesystem
 *
 * @package    local_smart_klass
 * @copyright  KlassData <kttp://www.klassdata.com>
 * @author     Oscar Ruesga <oscar@klassdata.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class Logger {
    
    static $logger = array();
  
  
    public static function add_to_log ($key = '', $value = '') {
        
        if ( get_config('local_smart_klass', 'save_log') != 1 || empty ($key)) return;     
        $logobj = new \stdClass();
        $logobj->key = $key;
        $logobj->value = $value;
        $logobj->date = date('d/m/Y H:i:s');       
        self::$logger[] = $logobj;
    }

    public static function save_log ($name='') {

        if ( get_config('local_smart_klass', 'save_log') != 1) return;

        $syscontext = \context_system::instance();
        $fs = get_file_storage();

        $filename = ( ( !empty($name) ) ? $name : 'xAPI_log_' . date('Ymd_His') ) . '.json';

        // Prepare file record object
        $fileinfo = array(
            'contextid' => $syscontext->id, // ID of context
            'component' => 'local_smart_klass',     // usually = table name
            'filearea' => 'local_smart_klass',     // usually = table name
            'itemid' => 0,               // usually = ID of row in table
            'filepath' => '/',           // any path beginning and ending in /
            'filename' => $filename); // any filename

        $fs->create_file_from_string($fileinfo, json_encode(self::$logger));
        
        self::clear_log();
        
        return $filename;

    }


    public static function read_log ($filename=null) {
        $syscontext = \context_system::instance();
        $fs = get_file_storage();

        // Prepare file record object
        $fileinfo = array(
            'component' => 'local_smart_klass',     // usually = table name
            'filearea' => 'local_smart_klass',     // usually = table name
            'itemid' => 0,               // usually = ID of row in table
            'contextid' => $syscontext->id, // ID of context
            'filepath' => '/',           // any path beginning and ending in /
            'filename' => $filename); // any filename

        // Get file
        $file = $fs->get_file($fileinfo['contextid'], $fileinfo['component'], $fileinfo['filearea'],
                              $fileinfo['itemid'], $fileinfo['filepath'], $fileinfo['filename']);

        // Read contents
        if ($file) {
            return $file->get_content();
        } else {
            // file doesn't exist - do something
            return null;
        }
    }
    
    public static function clear_log (){
        self::$logger = new \stdClass();
    }
    
    public static function get_url ($filename) {
        $syscontext = \context_system::instance();
        return \moodle_url::make_pluginfile_url(
                                                $syscontext->id,
                                                'local_smart_klass',
                                                'local_smart_klass',
                                                0,
                                                '/',
                                                $filename
                                                );
    }
    
    
    public static function get_logs (){
        $out = array();
        $syscontext = \context_system::instance();
        $fs = get_file_storage();
        $files = $fs->get_area_files($syscontext->id, 'local_smart_klass', 'local_smart_klass', 0, 'timemodified DESC', false);
        
        $count = 1;
        foreach ($files as $file) {
            $filename = $file->get_filename();
            $url = \moodle_url::make_pluginfile_url( $file->get_contextid(), 
                                                    'local_smart_klass',
                                                    'local_smart_klass', 
                                                    0, 
                                                    '/',
                                                    $filename);
            $out[] = \html_writer::link($url, str_pad($count++, 4, '0', STR_PAD_LEFT) . '. ' . date('d/m/Y H:i:s',$file->get_timemodified()) . ' --- ' . $filename);
        }
        $br = \html_writer::empty_tag('br');

        return implode($br, $out);
    }
    
    
    public static function delete_all_logs (){
        $fs = get_file_storage();
        $syscontext = \context_system::instance();
        $files = $fs->delete_area_files($syscontext->id, 'local_smart_klass', 'local_smart_klass', 0);
    }
  
}
