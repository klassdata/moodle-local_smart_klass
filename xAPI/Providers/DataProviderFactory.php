<?php
namespace Klap\xAPI;

/**
 * DataProviderFactory Class to build DataProvider objects 
 *
 * @package    local_klap
 * @copyright  Klap <kttp://www.klaptek.com>
 * @author     Oscar <oscar@klaptek.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class DataProviderFactory {

    public static function build() {
        global $CFG;

        //Moodle 2.7
        if ($CFG->version >= KLAP_MOODLE_27) {
          $provider = "DataProvider_Moodle27";  
        } else {
          $provider = "DataProvider";
        }
        
        $provider = 'Klap\\xAPI\\' . $provider;
        
        if(class_exists($provider)) {
          return new $provider();
        } else {
          throw new Exception( get_string('invalid_dataprovider', 'local_klap') );
        }
    }
}
