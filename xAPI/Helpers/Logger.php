<?php
namespace Klap\xAPI;

/**
 * Logger Class. Store Log File in Moodle Filesystem
 *
 * @package    local_klap
 * @copyright  Klap <kttp://www.klaptek.com>
 * @author     Oscar <oscar@klaptek.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class Logger {
    
    static $logger = '';
  
  
    public static function add_to_log ($str = '') {
        if ( get_config('local_klap', 'save_log') != 1 || empty ($str) ) return;

        self::$logger .= date('d/m/Y H:i:s') . ' ' . $str . PHP_EOL; 

    }

    public static function save_log () {

        if ( get_config('local_klap', 'save_log') != 1) return;

        $syscontext = \context_system::instance();
        $fs = get_file_storage();

        $filename = 'xAPI_log_' . date('Ymd_His') . '.txt';

        // Prepare file record object
        $fileinfo = array(
            'contextid' => $syscontext->id, // ID of context
            'component' => 'local_klap',     // usually = table name
            'filearea' => 'local_klap',     // usually = table name
            'itemid' => 0,               // usually = ID of row in table
            'filepath' => '/',           // any path beginning and ending in /
            'filename' => $filename); // any filename

        // Create file containing text 'hello world'
        $fs->create_file_from_string($fileinfo, self::$logger);
        
        self::clear_log();
        
        return $filename;

    }


    public static function read_log ($filename=null) {
        $syscontext = \context_system::instance();
        $fs = get_file_storage();

        // Prepare file record object
        $fileinfo = array(
            'component' => 'local_klap',     // usually = table name
            'filearea' => 'local_klap',     // usually = table name
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
        self::$logger = '';
    }
    
    public static function get_url ($filename) {
        $syscontext = \context_system::instance();
        return \moodle_url::make_pluginfile_url(
                                                $syscontext->id,
                                                'local_klap',
                                                'local_klap',
                                                0,
                                                '/',
                                                $filename
                                                );
    }
    
    
    public static function get_logs (){
        $out = array();
        $syscontext = \context_system::instance();
        $fs = get_file_storage();
        $files = $fs->get_area_files($syscontext->id, 'local_klap', 'local_klap', 0, 'timemodified DESC', false);
        
        $count = 1;
        foreach ($files as $file) {
            $filename = $file->get_filename();
            $url = \moodle_url::make_pluginfile_url( $file->get_contextid(), 
                                                    'local_klap',
                                                    'local_klap', 
                                                    0, 
                                                    '/',
                                                    $filename);
            $out[] = \html_writer::link($url, str_pad($count++, 4, '0', STR_PAD_LEFT) . '. ' . date('d/m/Y H:i:s',$file->get_timemodified()) . ' --- ' . $filename);
        }
        $br = \html_writer::empty_tag('br');

        return implode($br, $out);
    }
    
    
    public static function delete_all_logs (){
        $syscontext = \context_system::instance();
        $files = $fs->delete_area_files($syscontext, 'local_klap', 'local_klap', 0);
    }
  
}
