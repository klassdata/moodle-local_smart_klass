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
           
        $access_token = $this->dataprovider->getConfig('oauth_access_token');

        if ($access_token == false) return false;

        $server = $this->dataprovider->getConfig('oauth_server') . '/lms/payload';

        $curl = new Curl;
        $output = $curl->get( $server, array('access_token' => $access_token));
        
        if ( isset($output->error) ) {
            $refresh_token = $this->refreshToken ();
            if ($refresh_token == false) {
                set_config('oauth_client_id', null, 'local_smart_klass');
                set_config('oauth_client_secret', null, 'local_smart_klass');
                set_config('oauth_access_token', null, 'local_smart_klass');
                set_config('oauth_refresh_token', null, 'local_smart_klass');
                redirect( moodle_url('/local/smart_klass/register.php') );
            }
            return;
        }
        $output = json_decode ($output);
        
        $value =  ( isset($output->lrs_endpoint) ) ? $output->lrs_endpoint : null;
        set_config('lrs_endpoint', $value, 'local_smart_klass');
        
        $value =  ( isset($output->lrs_username) ) ? $output->lrs_username : null;
        set_config('lrs_username', $value, 'local_smart_klass');
        
        $value =  ( isset($output->lrs_password) ) ? $output->lrs_password : null;
        set_config('lrs_password', $value, 'local_smart_klass');
        
        $value =  ( isset($output->dashboard_endpoint) ) ? $output->dashboard_endpoint : null;
        set_config('dashboard_endpoint', $value, 'local_smart_klass');
        
        
        $value =  ( isset($output->tracker_endpoint) ) ? $output->tracker_endpoint : null;
        set_config('tracker_endpoint', $value, 'local_smart_klass');
        
        $value =  ( isset($output->tracker_id) ) ? $output->tracker_id : null;
        set_config('tracker_id', $value, 'local_smart_klass');

    }
    
    
    public function getCredentials () {       
        $cicle = (integer) $this->dataprovider->getConfig('credential_cicle');
        if ($cicle == 0 ||  $cicle <= time()) {
            $this->dataprovider->setConfig('credential_cicle', time() + self::MAX_TIME);
            $this->updateCredentials();      
        }
       

        $credentials = new \stdClass();
        $credentials->lrs_endpoint = ($this->dataprovider->getConfig('lrs_endpoint') != false) ? 
                $this->dataprovider->getConfig('lrs_endpoint') : null;
        $credentials->lrs_username = ($this->dataprovider->getConfig('lrs_username') != false) ?
            $this->dataprovider->getConfig('lrs_username') : null;
        $credentials->lrs_password = ($this->dataprovider->getConfig('lrs_password') != false) ?
             $this->dataprovider->getConfig('lrs_password') : null;
        $credentials->dashboard_endpoint = ($this->dataprovider->getConfig('dashboard_endpoint') != false) ?
             $this->dataprovider->getConfig('dashboard_endpoint') : null;
        $credentials->tracker_endpoint = ($this->dataprovider->getConfig('tracker_endpoint') != false) ?
             $this->dataprovider->getConfig('tracker_endpoint') : null;
        $credentials->tracker_id = ($this->dataprovider->getConfig('tracker_id') != false) ?
             $this->dataprovider->getConfig('tracker_id') : null;
        
        return $credentials;
    }
    
    public function refreshToken(){   
        $refresh_token = $this->dataprovider->getConfig('oauth_refresh_token');

        if ($refresh_token == false) return false;

        $server = $this->dataprovider->getConfig('oauth_server') . '/oauth/refresh_token';

        $curl = new Curl;
        
        $params = array ( 
                            'client_id' => $this->dataprovider->getConfig('oauth_client_id'),
                            'client_secret' => $this->dataprovider->getConfig('oauth_client_secret'),
                            'refresh_token' => $this->dataprovider->getConfig('oauth_refresh_token'),
                            'grant_type' => 'refresh_token',
        );
        $output = $curl->post( $server, $params);
        
        if ( isset($output->error) ) return false;
        
        if ( isset($output->refresh_token) ) 
            $this->dataprovider->setConfig('oauth_access_token', $output->access_token);
        if ( isset($output->access_token) )
            $this->dataprovider->setConfig('oauth_access_token', $output->access_token);
        
        $this->updateCredentials();
    }
    
   
    
    
}
