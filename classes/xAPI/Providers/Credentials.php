<?php
namespace SmartKlass\xAPI;

/**
 * xAPI Credentials Class
 *
 * @package    local_smart_klass
 * @copyright  KlassData <kttp://www.klassdata.com>
 * @author     Oscar Ruesga <oscar@klassdata.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class Credentials {

    const MAX_TIME = 86400;
    //const MAX_TIME = 0;

    private $dataprovider;
    private static $credentials;

    public static function getProvider () {

        if ( !(self::$credentials instanceof Credentials) ){
            self::$credentials = new Credentials();
            self::$credentials->dataprovider = DataProviderFactory::build();
        }
        return  self::$credentials;
    }

    public function updateCredentials () {
    /*
		set_config('lrs_endpoint', $this->dataprovider->getConfig('lrs_endpoint'), 'local_smart_klass');
		set_config('lrs_username', $this->dataprovider->getConfig('lrs_username'), 'local_smart_klass');
		set_config('lrs_password',  $this->dataprovider->getConfig('lrs_password'), 'local_smart_klass');
		set_config('smartklass_serialnumber', $this->dataprovider->getConfig('dashboard_endpoint'), 'local_smart_klass');
    */
    }

    public function getCredentials () {

        $credentials = new \stdClass();
        $credentials->lrs_endpoint = ($this->dataprovider->getConfig('lrs_endpoint') != false) ?
            $this->dataprovider->getConfig('lrs_endpoint') : null;
        $credentials->lrs_username = ($this->dataprovider->getConfig('lrs_username') != false) ?
            $this->dataprovider->getConfig('lrs_username') : null;
        $credentials->lrs_password = ($this->dataprovider->getConfig('lrs_password') != false) ?
            $this->dataprovider->getConfig('lrs_password') : null;
        $credentials->dashboard_endpoint = ($this->dataprovider->getConfig('dashboard_endpoint') != false) ?
            $this->dataprovider->getConfig('dashboard_endpoint') : null;
        $credentials->tracker_endpoint = "";
        $credentials->tracker_id = "";

        return $credentials;
    }

}
