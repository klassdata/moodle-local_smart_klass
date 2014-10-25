<?php
namespace SmartKlass\xAPI;

/**
 * DataProviderFactory Class to build DataProvider objects 
 *
 * @package    local_smart_klass
 * @copyright  KlassData <kttp://www.klassdata.com>
 * @author     Oscar Ruesga <oscar@klassdata.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class DataProviderFactory {

    public static function build() {
        global $CFG;

        //Moodle 2.7
        if ($CFG->version >= SMART_KLASS_MOODLE_27) {
          $provider = "DataProvider_Moodle27";  
        } else {
          $provider = "DataProvider";
        }
        
        $provider = 'SmartKlass\\xAPI\\' . $provider;
        
        if(class_exists($provider)) {
          return new $provider();
        } else {
          throw new Exception( get_string('invalid_dataprovider', 'local_smart_klass') );
        }
    }
}
